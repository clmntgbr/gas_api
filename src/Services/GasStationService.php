<?php

namespace App\Services;

use App\Common\EntityId\GasStationId;
use App\Message\CreateGasServiceMessage;
use App\Message\CreateGasStationMessage;
use SimpleXMLElement;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;
use Safe;

final class GasStationService
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    public function createGasStation(GasStationId $gasStationId, SimpleXMLElement $element): void
    {
        $this->messageBus->dispatch(new CreateGasStationMessage(
            $gasStationId,
            (string) $element->attributes()->pop,
            (string) $element->attributes()->cp,
            (string) $element->attributes()->longitude,
            (string) $element->attributes()->latitude,
            (string) $element->adresse,
            (string) $element->ville,
            'FRANCE',
            Safe\json_decode(str_replace('@', '', Safe\json_encode($element)), true)
        ), [new AmqpStamp('async-priority-high', 0, [])]);
    }
}