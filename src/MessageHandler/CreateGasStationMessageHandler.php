<?php

namespace App\MessageHandler;

use App\Common\EntityId\GasStationId;
use App\Entity\Address;
use App\Entity\GasStation;
use App\Entity\GooglePlace;
use App\Lists\GasStationStatusReference;
use App\Message\CreateGasStationMessage;
use App\Message\UpdateGasStationAddressMessage;
use App\Repository\GasStationRepository;
use App\Services\Uuid;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreateGasStationMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private MessageBusInterface $messageBus,
        private GasStationRepository $gasStationRepository
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

        if ('' === $message->getLatitude() || '' === $message->getLongitude()) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station longitude/latitude is empty (id : %s)', $message->getGasStationId()->getId()));
        }

        $address = new Address();
        $address
            ->setCity($message->getCity())
            ->setPostalCode($message->getCp())
            ->setLongitude($message->getLongitude() ? strval(floatval($message->getLongitude()) / 100000) : null)
            ->setLatitude($message->getLatitude() ? strval(floatval($message->getLatitude()) / 100000) : null)
            ->setCountry($message->getCountry())
            ->setStreet($message->getStreet())
            ->setVicinity(sprintf('%s, %s %s, %s', $message->getStreet(), $message->getCp(), $message->getCity(), $message->getCountry()));

        $gasStation = new GasStation();
        $gasStation
            ->setId($message->getGasStationId()->getId())
            ->setPop($message->getPop())
            ->setElement($message->getElement())
            ->setAddress($address)
            ->setGooglePlace(new GooglePlace())
            ->setStatus(GasStationStatusReference::CREATED);

        $filename = sprintf('%s.jpg', Uuid::v4());
        copy('public/images/75d481da-5dd4-497e-a426-f6367685c042.jpg', sprintf('public/images/gas_stations/%s', $filename));

        $gasStation->getImage()->setName($filename);
        $gasStation->getImage()->setOriginalName($filename);
        $gasStation->getImage()->setDimensions([660, 440]);
        $gasStation->getImage()->setMimeType('jpg');
        $gasStation->getImage()->setSize(86110);

        $this->isGasStationClosed($message->getElement(), $gasStation);

        if (null !== $gasStation->getClosedAt()) {
            $gasStation->setStatus(GasStationStatusReference::CLOSED);
        }

        $this->em->persist($gasStation);
        $this->em->flush();

        $this->messageBus->dispatch(new UpdateGasStationAddressMessage(
            new GasStationId($gasStation->getId())
        ), [new AmqpStamp('async-priority-low', 0, [])]);
    }

    /**
     * @param array<mixed> $element
     */
    public function isGasStationClosed(array $element, GasStation $gasStation): void
    {
        if (isset($element['fermeture']['attributes']['type']) && 'D' == $element['fermeture']['attributes']['type']) {
            $gasStation
                ->setClosedAt(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', str_replace('T', ' ', substr($element['fermeture']['attributes']['debut'], 0, 19))));
        }
    }
}
