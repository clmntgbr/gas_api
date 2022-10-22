<?php

namespace App\Services;

use App\Common\EntityId\GasStationId;
use App\Common\EntityId\GasTypeId;
use App\Dto\GasStationsMapDto;
use App\Entity\GasStation;
use App\Message\CreateGasPriceMessage;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

final class GasStationsMapService
{
    public function __construct(
        private array $lowGasPrices = []
    ) {
    }

    /** @param GasStation[] $gasStations */
    public function update($gasStations)
    {
        foreach ($gasStations as $key => $gasStation) {
            foreach ($gasStation->getLastGasPrices() as $gasPriceKey => $gasPrice) {
                if (!array_key_exists($key, $this->lowGasPrices)) {
                    $this->updateLowGasPrices($gasStation, $key, $gasPriceKey, $gasPrice);
                    continue;
                }
                if (array_key_exists('gasPriceValue', $gasPrice) && array_key_exists('gasPriceValue', $this->lowGasPrices[$key]) && $gasPrice['gasPriceValue'] <= $this->lowGasPrices[$key]['gasPriceValue']) {
                    $this->updateLowGasPrices($gasStation, $key, $gasPriceKey, $gasPrice);
                    continue;
                }
            }
        }

        foreach ($this->lowGasPrices as $key => $lowGasPrice) {
            $gasStation = $gasStations[$lowGasPrice['gasStationIndex']];
            $gasStation->setHasLowPrices(true);

            $lastGasPrices = $gasStation->getLastGasPrices();

            if (array_key_exists($key, $lastGasPrices)) {
                $prices = $lastGasPrices[$key];
                $prices['isLowPrice'] = true;
                $lastGasPrices[$key] = $prices;
                $gasStation->addLastGasPrices($lastGasPrices);
            }

            $gasStations[$lowGasPrice['gasStationIndex']] = $gasStation;
        }

        return new GasStationsMapDto($gasStations);
    }

    private function updateLowGasPrices(GasStation $gasStation, int $key, string $gasPriceKey, array $gasPrice)
    {
        $this->lowGasPrices[$gasPriceKey] = [
            'id' => $gasPrice['id'],
            'gasStationId' => $gasStation->getId(),
            'gasStationIndex' => $key,
        ];
    }
}