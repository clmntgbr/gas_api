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

    public function update(string $year, string $department): void
    {
        $xmlPath = $this->downloadGasPriceFile(
            GasPriceUpdateService::PATH,
            GasPriceUpdateService::FILENAME,
            $year
        );

        $elements = Safe\simplexml_load_file($xmlPath);

        foreach ($elements as $element) {
            $json = json_encode($element);
            $element = json_decode($json, true);

            $gasStationId = $this->gasStationService->getGasStationId($element);

            if (!in_array(substr($gasStationId->getId(), 0, 2), [$department])) {
                continue;
            }

            $this->gasStationService->createGasStation($gasStationId, $element);
            $this->gasServiceService->createGasService($gasStationId, $element);
            $this->gasPriceService->createGasPricesYear($gasStationId, $element);
        }

        FileSystemService::delete($xmlPath);
    }

    public function downloadGasPriceFile(string $path, string $name, string $year): string
    {
//        FileSystemService::delete($path, $name);
//
//        FileSystemService::download(sprintf($this->gasPriceYearUrl, $year), $name, $path);
//
//        if (false === FileSystemService::exist($path, $name)) {
//            throw new Exception();
//        }
//
//        if (false === FileSystemService::unzip(sprintf('%s%s', $path, $name), $path)) {
//            throw new Exception();
//        }
//
//        FileSystemService::delete($path, $name);

        if (null === $xmlPath = FileSystemService::find($path, "%\.(xml)$%i")) {
            throw new Exception();
        }

        return $xmlPath;
    }
}