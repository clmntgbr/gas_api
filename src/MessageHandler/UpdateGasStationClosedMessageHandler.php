<?php

namespace App\MessageHandler;

use App\Lists\GasStationStatusReference;
use App\Message\UpdateGasStationClosedMessage;
use App\Repository\GasStationRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Safe\DateTimeImmutable;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UpdateGasStationClosedMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private GasStationRepository $gasStationRepository
    ) {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function __invoke(UpdateGasStationClosedMessage $message): void
    {
        if (!$this->em->isOpen()) {
            $this->em = EntityManager::create($this->em->getConnection(), $this->em->getConfiguration());
        }

        $gasStation = $this->gasStationRepository->findOneBy(['id' => $message->getGasStationId()->getId()]);

        if (null === $gasStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station is null (id: %s)', $message->getGasStationId()->getId()));
        }

        $gasStation->setClosedAt(new DateTimeImmutable());
        $gasStation->setStatus(GasStationStatusReference::CLOSED);

        $this->em->persist($gasStation);
        $this->em->flush();
    }
}
