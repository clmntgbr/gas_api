<?php

namespace App\MessageHandler;

use App\Entity\GasService;
use App\Message\CreateGasServiceMessage;
use App\Repository\GasServiceRepository;
use App\Repository\GasStationRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class CreateGasServiceMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private GasStationRepository   $gasStationRepository,
        private GasServiceRepository   $gasServiceRepository
    ) {
    }

    public function __invoke(CreateGasServiceMessage $message): void
    {
        if (!$this->em->isOpen()) {
            $this->em = EntityManager::create($this->em->getConnection(), $this->em->getConfiguration());
        }

        $gasStation = $this->gasStationRepository->findOneBy(['id' => $message->getGasStationId()->getId()]);

        if (null === $gasStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station is null (id: %s)', $message->getGasStationId()->getId()));
        }

        $gasService = $this->gasServiceRepository->findOneBy(['label' => $message->getLabel()]);

        if ($gasService instanceof GasService) {
            if ($gasStation->hasGasService($gasService)) {
                throw new UnrecoverableMessageHandlingException(
                    sprintf('Gas Service is already linked to this Gas Station (Gas Service Label : %s, Gas Station id : %s)', $message->getLabel(), $message->getGasStationId()->getId())
                );
            }
        }

        if (null === $gasService) {
            $gasService = new GasService();
            $gasService
                ->setLabel($message->getLabel())
                ->setReference((new Slugify())->slugify($message->getLabel(), '_'));
        }

        $gasStation->addGasService($gasService);

        $this->em->persist($gasStation);
        $this->em->flush();
    }
}