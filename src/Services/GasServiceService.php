<?php

namespace App\Services;

use App\Common\EntityId\GasStationId;
use App\Message\CreateGasServiceMessage;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

final class GasServiceService
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    public function createGasService(GasStationId $gasStationId, string $item): void
    {
        $this->messageBus->dispatch(new CreateGasServiceMessage(
            $gasStationId,
            $item
        ), [new AmqpStamp('async-priority-low', 0, [])]);
    }
}
