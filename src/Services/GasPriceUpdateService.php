<?php

namespace App\Services;

use App\Common\EntityId\GasStationId;
use App\Common\EntityId\GasTypeId;
use App\Repository\GasTypeRepository;
use Exception;
use Safe;

final class GasPriceUpdateService
{
    public const PATH = 'public/gas_prices/';
    public const FILENAME = 'gas-price.zip';

    public function __construct(
        private string $gasPriceInstantUrl,
        private GasServiceService $gasServiceService,
        private GasStationService $gasStationService,
        private GasPriceService $gasPriceService,
        private GasTypeRepository $gasTypeRepository
    ) {
    }

    public function update(): void
    {
        $gasTypes = $this->gasTypeRepository->findGasTypeById();

        $xmlPath = $this->downloadGasPriceFile(
            self::PATH,
            self::FILENAME
        );

        $elements = Safe\simplexml_load_file($xmlPath);

        foreach ($elements as $element) {
            $json = json_encode($element);
            $element = json_decode($json, true);

            $gasStationId = $this->gasStationService->getGasStationId($element);

            if (!in_array(substr($gasStationId->getId(), 0, 2), ['94', '92', '91', '75'])) {
                continue;
            }

            $this->createGasStation($gasStationId, $element);
            $this->createGasService($gasStationId, $element);
            $this->createGasPrices($gasStationId, $element, $gasTypes);
        }

        FileSystemService::delete($xmlPath);
    }

    private function createGasService(GasStationId $gasStationId, array $element): void
    {
        if (!array_key_exists('service', $element['services'])) {
            return;
        }

        if (is_array($element['services']['service'])) {
            foreach ($element['services']['service'] as $item) {
                $this->gasServiceService->createGasService(
                    $gasStationId,
                    $item
                );
            }

            return;
        }

        if (is_string($element['services']['service'])) {
            $this->gasServiceService->createGasService(
                $gasStationId,
                $element['services']['service']
            );
        }
    }

    private function createGasStation(GasStationId $gasStationId, array $element): void
    {
        $this->gasStationService->createGasStation(
            $gasStationId,
            $element
        );
    }

    /**
     * @param array<mixed> $gasTypes
     */
    private function createGasPrices(GasStationId $gasStationId, array $element, array $gasTypes): void
    {
        foreach ($element['prix'] ?? [] as $item) {
            $gasTypeId = new GasTypeId($item['@attributes']['id'] ?? 0);
            $date = $item['@attributes']['maj'] ?? null;
            $value = $item['@attributes']['valeur'] ?? null;

            if (1 == count($element['prix'])) {
                $gasTypeId = new GasTypeId($item['id'] ?? 0);
                $date = $item['maj'] ?? null;
                $value = $item['valeur'] ?? null;
            }

            $this->gasPriceService->createGasPrice($gasStationId, $gasTypeId, $date, $value);
        }
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
