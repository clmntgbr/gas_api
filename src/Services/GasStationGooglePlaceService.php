<?php

namespace App\Services;

use App\Entity\GasStation;
use App\Lists\GasStationStatusReference;
use App\Repository\GasStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Safe;

final class GasStationGooglePlaceService
{
    public function __construct(
        private GooglePlaceApi $googlePlaceApi,
        private GasStationRepository $gasStationRepository,
        private GooglePlaceService $googlePlaceService,
        private EntityManagerInterface $em
    ) {
    }

    public function update(): void
    {
        $gasStations = $this->gasStationRepository->findGasStationNotClosed();

        foreach ($gasStations as $gasStation) {
            if (GasStationStatusReference::FOUND_ON_GOV_MAP === $gasStation->getActualStatus()) {
                $this->findPlaceTextsearch($gasStation);
            }
        }
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
