<?php

namespace App\MessageHandler;

use App\Entity\GasStation;
use App\Message\CreateGasStationMessage;
use App\Repository\GasStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreateGasStationMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface    $messageBus,
        private GasStationRepository   $gasStationRepository
    ) {
    }

    public function __invoke(CreateGasStationMessage $message)
    {
        if (!$this->em->isOpen()) {
            $this->em = EntityManager::create($this->em->getConnection(), $this->em->getConfiguration());
        }

        $gasStation = $this->gasStationRepository->findOneBy(['id' => $message->getGasStationId()->getId()]);

        if ($gasStation instanceof GasStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station already exist (id : %s)', $message->getGasStationId()->getId()));
        }

        if ("" === $message->getLatitude() || "" === $message->getLongitude()) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station longitude/latitude is empty (id : %s)', $message->getGasStationId()->getId()));
        }
    }
}