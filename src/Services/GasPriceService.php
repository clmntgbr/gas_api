<?php

namespace App\Services;

use App\Common\EntityId\GasStationId;
use App\Common\EntityId\GasTypeId;
use App\Message\CreateGasPriceMessage;
use Safe\DateTimeImmutable;
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
    public function createGasPrices(GasStationId $gasStationId, array $element): void
    {
        foreach ($element['prix'] ?? [] as $item) {
            $datum = $this->getGasTypeId($element, $item);
            $this->createGasPrice($gasStationId, $datum['gasTypeId'], $datum['date'], $datum['value']);
        }
    }

    public function createGasPricesYear(GasStationId $gasStationId, array $element)
    {
        $items = [];

        foreach ($element['prix'] ?? [] as $item) {
            $datum = $this->getGasTypeId($element, $item);
            $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', str_replace('T', ' ', substr($datum['date'], 0, 19)));
            $items[$datum['gasTypeId']->getId()][$date->format("W")] = [
                'gasTypeId' => $datum['gasTypeId'],
                'date' => $datum['date'],
                'value' => $datum['value'],
            ];
        }

        foreach ($items as $item) {
            foreach ($item as $datum) {
                $this->createGasPrice($gasStationId, $datum['gasTypeId'], $datum['date'], $datum['value']);
            }
        }
    }

    private function getGasTypeId(array $element, array $item): array
    {
        $gasTypeId = new GasTypeId($item['@attributes']['id'] ?? 0);
        $date = $item['@attributes']['maj'] ?? null;
        $value = $item['@attributes']['valeur'] ?? null;

        if (1 == count($element['prix'])) {
            $gasTypeId = new GasTypeId($item['id'] ?? 0);
            $date = $item['maj'] ?? null;
            $value = $item['valeur'] ?? null;
        }

        return ['gasTypeId' => $gasTypeId, 'date' => $date, 'value' => $value];
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
