<?php

namespace App\Services;

use App\Entity\GasPrice;
use App\Repository\GasPriceRepository;
use App\Repository\GasStationRepository;
use Safe\DateTime;

final class GasPriceWeekService
{
    public function __construct(
        private GasPriceRepository $gasPriceRepository
    )
    {
    }

    public function update(): void
    {
        $date = (new DateTime('now'))->sub(new \DateInterval('P0M'));

        $gasPrices = $this->gasPriceRepository->findGasPricesByMonth($date->format('m'), $date->format('Y'));
        dd($gasPrices);
    }
}