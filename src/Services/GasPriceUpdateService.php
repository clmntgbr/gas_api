<?php

namespace App\Services;

use App\Common\EntityId\GasStationId;
use App\Common\EntityId\GasTypeId;
use App\Common\Exception\GasStationException;
use App\Entity\GasStation;
use App\Message\CreateGasStationMessage;
use App\Repository\GasServiceRepository;
use App\Repository\GasStationRepository;
use App\Repository\GasTypeRepository;
use Safe;
use SimpleXMLElement;
use \Exception;
use App\Services\FileSystemService;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

final class GasPriceUpdateService
{
    const PATH = 'public/gas_prices/';
    const FILENAME = 'gas-price.zip';

    public function __construct(
        private string $gasPriceInstantUrl,
        private MessageBusInterface $messageBus
    ) {
    }

    public function update(?string $year = null): void
    {
        $xmlPath = $this->downloadGasPriceFile(
            self::PATH,
            self::FILENAME
        );

        $elements = Safe\simplexml_load_file($xmlPath);

        foreach ($elements as $element) {
            $gasStationId = $this->getGasStationId($element);
            $this->createGasStation($gasStationId, $element);
        }

        FileSystemService::delete($xmlPath);
    }

    public function getGasStationId(SimpleXMLElement $element): GasStationId
    {
        $gasStationId = (string)$element->attributes()->id;

        if (empty($gasStationId)) {
            throw new GasStationException(GasStationException::GAS_STATION_ID_EMPTY);
        }

        return new GasStationId($gasStationId);
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

        if (false === FileSystemService::unzip(sprintf("%s%s", $path, $name), $path)) {
            throw new Exception();
        }

        FileSystemService::delete($path, $name);

        if (null === $xmlPath = FileSystemService::find($path, "%\.(xml)$%i")) {
            throw new Exception();
        }

        return $xmlPath;
    }

    public function createGasStation(GasStationId $gasStationId, SimpleXMLElement $element): void
    {
        $this->messageBus->dispatch(new CreateGasStationMessage(
            $gasStationId,
            (string)$element->attributes()->pop,
            (string)$element->attributes()->cp,
            (string)$element->attributes()->longitude,
            (string)$element->attributes()->latitude,
            (string)$element->adresse,
            (string)$element->ville,
            "FRANCE",
            Safe\json_decode(str_replace("@", "", Safe\json_encode($element)), true)
        ), [new AmqpStamp('async-priority-high', AMQP_NOPARAM, [])]);
    }
}