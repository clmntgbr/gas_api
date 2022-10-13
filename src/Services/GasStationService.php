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
        $this->messageBus->dispatch(new CreateGasStationMessage(
            $gasStationId,
            $element['@attributes']['pop'] ?? throw new \Exception('Missing pop attributes'),
            $element['@attributes']['cp'] ?? throw new \Exception('Missing cp attributes'),
            $element['@attributes']['longitude'] ?? throw new \Exception('Missing longitude attributes'),
            $element['@attributes']['latitude'] ?? throw new \Exception('Missing latitude attributes'),
            $element['adresse'] ?? throw new \Exception('Missing addresse attributes'),
            $element['ville'] ?? throw new \Exception('Missing ville attributes'),
            'FRANCE',
            $element,
        ), [new AmqpStamp('async-priority-high', 0, [])]);
    }

    public function updateGasStationClosed(GasStation $gasStation)
    {
        $this->messageBus->dispatch(new UpdateGasStationClosedMessage(
            new GasStationId($gasStation->getId()),
        ), [new AmqpStamp('async-priority-low', 0, [])]);
    }
}
