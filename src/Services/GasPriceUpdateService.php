<?php

namespace App\Services;

use App\Common\EntityId\GasStationId;
use App\Common\Exception\GasStationException;
use App\Entity\GasService;
use App\Message\CreateGasStationMessage;
use Exception;
use Safe;
use SimpleXMLElement;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

final class GasPriceUpdateService
{
    public const PATH = 'public/gas_prices/';
    public const FILENAME = 'gas-price.zip';

    public function __construct(
        private string $gasPriceInstantUrl,
        private MessageBusInterface $messageBus,
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
            $gasStationId = $this->getGasStationId($element);
            $this->createGasStation($gasStationId, $element);
            $this->createGasService($gasStationId, $element);
            $i++;
            if ($i == 40) {
                break;
            }
        }

        FileSystemService::delete($xmlPath);
    }

    public function getGasStationId(SimpleXMLElement $element): GasStationId
    {
        $gasStationId = (string) $element->attributes()->id;

        if (empty($gasStationId)) {
            throw new GasStationException(GasStationException::GAS_STATION_ID_EMPTY);
        }

        return new GasStationId($gasStationId);
    }

    private function createGasService(GasStationId $gasStationId, SimpleXMLElement $element): void
    {
        foreach ((array)$element->services->service as $item) {
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

    /**
     * @throws DotEnvException
     */
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