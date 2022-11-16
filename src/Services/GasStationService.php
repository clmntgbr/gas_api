<?php

namespace App\Services;

use App\Common\EntityId\GasStationId;
use App\Common\Exception\GasStationException;
use App\Entity\GasStation;
use App\Message\CreateGasStationMessage;
use App\Message\UpdateGasStationClosedMessage;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

final class GasStationService
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    public function getGasStationId(array $element): GasStationId
    {
        $gasStationId = $element['@attributes']['id'];

        if (empty($gasStationId)) {
            throw new GasStationException(GasStationException::GAS_STATION_ID_EMPTY);
        }

        return new GasStationId($gasStationId);
    }

    public function createGasStation(GasStationId $gasStationId, array $element): void
    {
        $this->dispatchGasStation(
            $gasStationId,
            $element
        );
    }

    private function dispatchGasStation(GasStationId $gasStationId, array $element): void
    {
        $this->messageBus->dispatch(new CreateGasStationMessage(
            $gasStationId,
            $this->convert($element['@attributes']['pop'] ?? ''),
            $this->convert($element['@attributes']['cp'] ?? ''),
            $this->convert($element['@attributes']['longitude'] ?? ''),
            $this->convert($element['@attributes']['latitude'] ?? ''),
            $this->convert($element['adresse'] ?? ''),
            $this->convert($element['ville'] ?? ''),
            'FRANCE',
            $element,
        ), [new AmqpStamp('async-priority-high', 0, [])]);
    }

    private function convert($datum): string
    {
        if (is_array($datum)) {
            return implode(' ', $datum);
        }
        return $datum;
    }

    public function updateGasStationClosed(GasStation $gasStation)
    {
        $this->messageBus->dispatch(new UpdateGasStationClosedMessage(
            new GasStationId($gasStation->getId()),
        ), [new AmqpStamp('async-priority-low', 0, [])]);
    }
}