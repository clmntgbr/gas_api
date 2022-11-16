<?php

namespace App\Services;

use Exception;
use Safe;

final class GasPriceYearService
{
    public function __construct(
        private string $gasPriceYearUrl,
        private GasServiceService $gasServiceService,
        private GasStationService $gasStationService,
        private GasPriceService $gasPriceService
    ) {
    }

    public function update(string $year): void
    {
        $scandir = array_diff(Safe\scandir("public/gas_prices/$year"), array('..', '.'));

        foreach ($scandir as $file) {
            $handle = fopen("public/gas_prices/$year/$file", "r");
            while (!feof($handle)) {
                $element = json_decode(fgets($handle), true);
                foreach ($element as $datum) {
                    $gasStationId = $this->gasStationService->getGasStationId($datum);
                    $this->gasStationService->createGasStation($gasStationId, $datum);
                    $this->gasServiceService->createGasService($gasStationId, $datum);
                    $this->gasPriceService->createGasPricesYear($gasStationId, $datum);
                }
            }
            fclose($handle);
        }
    }
}