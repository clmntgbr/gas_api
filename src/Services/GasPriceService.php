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

    /**
     * @param array<mixed> $gasTypes
     */
    public function createGasPrices(GasStationId $gasStationId, array $element, array $gasTypes): void
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

            $this->createGasPrice($gasStationId, $gasTypeId, $date, $value);
        }
    }

    private function createGasPrice(GasStationId $gasStationId, GasTypeId $gasTypeId, ?string $date, ?string $value): void
    {
        $this->messageBus->dispatch(new CreateGasPriceMessage(
            $gasStationId,
            $gasTypeId,
            $date,
            $value
        ), [new AmqpStamp('async-priority-medium', 0, [])]);
    }
}
