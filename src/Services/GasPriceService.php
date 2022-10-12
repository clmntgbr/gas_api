<?php

namespace App\Services;

use App\Common\EntityId\GasStationId;
use App\Common\EntityId\GasTypeId;
use App\Message\CreateGasPriceMessage;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

final class GasPriceService
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    public function createGasPrice(GasStationId $gasStationId, GasTypeId $gasTypeId, ?string $date, ?string $value): void
    {
        $this->messageBus->dispatch(new CreateGasPriceMessage(
            $gasStationId,
            $gasTypeId,
            $date,
            $value
        ), [new AmqpStamp('async-priority-low', 0, [])]);
    }
}
