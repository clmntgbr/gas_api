<?php

namespace App\Services;

use App\Common\EntityId\GasStationId;
use App\Entity\GasStation;
use App\Lists\GasStationStatusReference;
use App\Message\CreateGooglePlaceTextsearchMessage;
use App\Repository\GasStationRepository;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

final class GasStationGooglePlaceService
{
    public function __construct(
        private GasStationRepository $gasStationRepository,
        private MessageBusInterface $messageBus
    ) {
    }

    public function update(): void
    {
        $gasStations = $this->gasStationRepository->findGasStationNotClosed();

        foreach ($gasStations as $gasStation) {
            if (GasStationStatusReference::FOUND_ON_GOV_MAP === $gasStation->getActualStatus()) {
                $this->createGooglePlaceTextsearch($gasStation);
            }
        }
    }

    public function createGooglePlaceTextsearch(GasStation $gasStation)
    {
        $this->messageBus->dispatch(new CreateGooglePlaceTextsearchMessage(
            new GasStationId($gasStation->getId()),
        ), [new AmqpStamp('async-priority-medium', 0, [])]);
    }
}
