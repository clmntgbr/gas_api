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

    public function createGasService(GasStationId $gasStationId, array $element): void
    {
        if (!array_key_exists('service', $element['services'])) {
            return;
        }

        if (is_array($element['services']['service'])) {
            foreach ($element['services']['service'] as $item) {
                $this->dispatchGasService(
                    $gasStationId,
                    $item
                );
            }

            return;
        }

        if (is_string($element['services']['service'])) {
            $this->dispatchGasService(
                $gasStationId,
                $element['services']['service']
            );
        }
    }

    private function dispatchGasService(GasStationId $gasStationId, string $item): void
    {
        $this->messageBus->dispatch(new CreateGasServiceMessage(
            $gasStationId,
            $item
        ), [new AmqpStamp('async-priority-medium', 0, [])]);
    }
}
