<?php

namespace App\Services;

use App\Common\EntityId\GasStationId;
use Exception;
use Safe;
use SimpleXMLElement;

final class GasPriceUpdateService
{
    public const PATH = 'public/gas_prices/';
    public const FILENAME = 'gas-price.zip';

    public function __construct(
        private string $gasPriceInstantUrl,
        private GasServiceService $gasServiceService,
        private GasStationService $gasStationService
    ) {
    }

    public function update(): void
    {
        $xmlPath = $this->downloadGasPriceFile(
            self::PATH,
            self::FILENAME
        );

        $elements = Safe\simplexml_load_file($xmlPath);

        $i = 0;
        foreach ($elements as $element) {
            $gasStationId = $this->gasStationService->getGasStationId($element);
            $this->createGasStation($gasStationId, $element);
            $this->createGasService($gasStationId, $element);
            ++$i;
            if (20 == $i) {
                break;
            }
        }

        FileSystemService::delete($xmlPath);
    }

    private function createGasService(GasStationId $gasStationId, SimpleXMLElement $element): void
    {
        foreach ((array) $element->services->service as $item) {
            $this->gasServiceService->createGasService(
                $gasStationId,
                $item
            );
        }
    }

    private function createGasStation(GasStationId $gasStationId, SimpleXMLElement $element): void
    {
        $this->gasStationService->createGasStation(
            $gasStationId,
            $element
        );
    }

    public function downloadGasPriceFile(string $path, string $name): string
    {
        FileSystemService::delete($path, $name);

        FileSystemService::download($this->gasPriceInstantUrl, $name, $path);

        if (false === FileSystemService::exist($path, $name)) {
            throw new Exception();
        }

        if (false === FileSystemService::unzip(sprintf('%s%s', $path, $name), $path)) {
            throw new Exception();
        }

        FileSystemService::delete($path, $name);

        if (null === $xmlPath = FileSystemService::find($path, "%\.(xml)$%i")) {
            throw new Exception();
        }

        return $xmlPath;
    }
}
