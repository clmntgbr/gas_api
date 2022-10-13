<?php

namespace App\Services;

use App\Repository\GasStationRepository;
use Safe;

final class GasStationClosedService
{
    public function __construct(
        private GasStationService $gasStationService,
        private GasStationRepository $gasStationRepository
    ) {
    }

    public function update(): void
    {
        $gasStations = $this->gasStationRepository->findGasStationNotClosed();

        foreach ($gasStations as $gasStation) {
            if (0 === $gasStation->getGasPrices()->count()) {
                $this->gasStationService->updateGasStationClosed($gasStation);
                continue;
            }

            $isClosed = true;
            $date = (new Safe\DateTime('now'))->sub(new \DateInterval('P6M'));
            foreach ($gasStation->getLastGasPrices() as $gasPrice) {
                if ($date->getTimestamp() < $gasPrice['datetimestamp']) {
                    $isClosed = false;
                    break;
                }
            }

            if ($isClosed) {
                $this->gasStationService->updateGasStationClosed($gasStation);
            }
        }
    }
}
