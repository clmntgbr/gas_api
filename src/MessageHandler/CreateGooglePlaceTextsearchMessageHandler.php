<?php

namespace App\MessageHandler;

use App\Entity\GasStation;
use App\Lists\GasStationStatusReference;
use App\Message\CreateGooglePlaceTextsearchMessage;
use App\Repository\GasStationRepository;
use App\Services\GooglePlaceApi;
use App\Services\GooglePlaceService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Safe;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class CreateGooglePlaceTextsearchMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private GasStationRepository $gasStationRepository,
        private GooglePlaceService $googlePlaceService,
        private GooglePlaceApi $googlePlaceApi
    ) {
    }

    public function __invoke(CreateGooglePlaceTextsearchMessage $message)
    {
        if (!$this->em->isOpen()) {
            $this->em = EntityManager::create($this->em->getConnection(), $this->em->getConfiguration());
        }

        $gasStation = $this->gasStationRepository->findOneBy(['id' => $message->getGasStationId()->getId()]);

        if (null === $gasStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station doesnt exist (id : %s)', $message->getGasStationId()->getId()));
        }

        if (GasStationStatusReference::FOUND_ON_GOV_MAP !== $gasStation->getActualStatus()) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station has bad FOUND_ON_GOV_MAP status (id : %s)', $message->getGasStationId()->getId()));
        }

        $this->findPlaceTextsearch($gasStation);
    }

    private function findPlaceTextsearch(GasStation $gasStation)
    {
        $response = $this->googlePlaceApi->placeTextsearch($gasStation);
        $content = Safe\json_decode($response->getBody()->getContents(), true);

        $gasStation->setTextsearchApiResult($content);

        if ($this->checkStatus($gasStation, $content)) {
            $gasStation->setStatus(GasStationStatusReference::FOUND_IN_TEXTSEARCH);
            $gasStation->getGooglePlace()->setPlaceId($content['results'][0]['place_id']);
            $this->googlePlaceService->createGooglePlaceDetails($gasStation);
        }

        $this->em->persist($gasStation);
        $this->em->flush();
    }

    private function checkStatus(GasStation $gasStation, array $response): bool
    {
        if (array_key_exists('status', $response) && in_array($response['status'], ['OK']) && array_key_exists('results', $response) && count($response['results']) > 0 && array_key_exists('place_id', $response['results'][0])) {
            return true;
        }

        $gasStation->setStatus(GasStationStatusReference::NOT_FOUND_IN_TEXTSEARCH);

        return false;
    }
}
