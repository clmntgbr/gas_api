<?php

namespace App\MessageHandler;

use App\Lists\GasStationStatusReference;
use App\Message\CreateGooglePlaceDetailsMessage;
use App\Repository\GasStationRepository;
use App\Services\GooglePlaceService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class CreateGooglePlaceDetailsMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private GasStationRepository $gasStationRepository,
        private GooglePlaceService $googlePlaceService
    ) {
    }

    public function __invoke(CreateGooglePlaceDetailsMessage $message)
    {
        if (!$this->em->isOpen()) {
            $this->em = EntityManager::create($this->em->getConnection(), $this->em->getConfiguration());
        }

        $gasStation = $this->gasStationRepository->findOneBy(['id' => $message->getGasStationId()->getId()]);

        if (null === $gasStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station already exist (id : %s)', $message->getGasStationId()->getId()));
        }

        if (GasStationStatusReference::FOUND_IN_TEXTSEARCH !== $gasStation->getStatus()) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station has bad FOUND_IN_TEXTSEARCH status (id : %s)', $message->getGasStationId()->getId()));
        }

        if (GasStationStatusReference::PLACE_ID_ANOMALY === $gasStation->getStatus()) {
            return;
        }

        $this->googlePlaceService->findPlaceDetails($gasStation);

        $gasStationsAnomalies = $this->gasStationRepository->getGasStationGooglePlaceByPlaceId($gasStation);

        if (count($gasStationsAnomalies) > 1) {
            $this->googlePlaceService->createAnomalies($gasStationsAnomalies);
        }
    }
}
