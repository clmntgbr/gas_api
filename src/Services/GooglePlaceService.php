<?php

namespace App\Services;

use App\Common\EntityId\GasStationId;
use App\Entity\GasStation;
use App\Lists\GasStationStatusReference;
use App\Message\CreateGooglePlaceAnomalyMessage;
use App\Message\CreateGooglePlaceDetailsMessage;
use Doctrine\ORM\EntityManagerInterface;
use Safe;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

final class GooglePlaceService
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private GooglePlaceApi $googlePlaceApi,
        private EntityManagerInterface $em
    ) {
    }

    public function createGooglePlaceDetails(GasStation $gasStation)
    {
        $this->messageBus->dispatch(new CreateGooglePlaceDetailsMessage(
            new GasStationId($gasStation->getId()),
        ), [new AmqpStamp('async-priority-low', 0, [])]);
    }

    public function findPlaceDetails(GasStation $gasStation): void
    {
        $response = $this->googlePlaceApi->placeDetails($gasStation);
        $content = Safe\json_decode($response->getBody()->getContents(), true);

        $gasStation->setPlaceDetailsApiResult($content);

        if ($this->checkStatus($gasStation, $content)) {
            $gasStation
                ->setName(htmlspecialchars_decode(ucwords(strtolower(trim($content['result']['name'] ?? null)))));
            $this->hydratePlaceDetails($gasStation, $content['result']);
        }

        $this->em->persist($gasStation);
        $this->em->flush();
    }

    private function checkStatus(GasStation $gasStation, array $response): bool
    {
        if (array_key_exists('status', $response) && 'OK' == $response['status'] && array_key_exists('result', $response)) {
            return true;
        }

        $gasStation->setStatus(GasStationStatusReference::NOT_FOUND_IN_DETAILS);

        return false;
    }

    private function hydratePlaceDetails(GasStation $gasStation, array $content): void
    {
        $gasStation->setStatus(GasStationStatusReference::FOUND_IN_DETAILS);
        $gasStation->setStatus(GasStationStatusReference::WAITING_VALIDATION);

        $this->updateGasStationAddressMessage($gasStation, $content);
        $this->updateGasStationGooglePlace($gasStation, $content);
    }

    /**
     * @param array<mixed> $details
     */
    private function updateGasStationGooglePlace(GasStation $gasStation, array $details): void
    {
        $gasStation->getGooglePlace()
            ->setGoogleId($details['id'] ?? null)
            ->setPlaceId($details['place_id'] ?? null)
            ->setBusinessStatus($details['business_status'] ?? null)
            ->setIcon($details['icon'] ?? null)
            ->setPhoneNumber($details['international_phone_number'] ?? null)
            ->setCompoundCode($details['plus_code']['compound_code'] ?? null)
            ->setGlobalCode($details['plus_code']['global_code'] ?? null)
            ->setGoogleRating($details['rating'] ?? null)
            ->setRating($details['rating'] ?? null)
            ->setReference($details['reference'] ?? null)
            ->setOpeningHours($details['opening_hours']['weekday_text'] ?? [])
            ->setUserRatingsTotal($details['user_ratings_total'] ?? null)
            ->setUrl($details['url'] ?? null)
            ->setWebsite($details['website'] ?? null);
    }

    /**
     * @param array<mixed> $details
     */
    private function updateGasStationAddressMessage(GasStation $gasStation, array $details): void
    {
        $address = $gasStation->getAddress();

        foreach ($details['address_components'] as $component) {
            foreach ($component['types'] as $type) {
                switch ($type) {
                    case 'street_number':
                        $address->setNumber($component['long_name']);
                        break;
                    case 'route':
                        $address->setStreet($component['long_name']);
                        break;
                    case 'locality':
                        $address->setCity($component['long_name']);
                        break;
                    case 'administrative_area_level_1':
                        $address->setRegion($component['long_name']);
                        break;
                    case 'country':
                        $address->setCountry($component['long_name']);
                        break;
                    case 'postal_code':
                        $address->setPostalCode($component['long_name']);
                        break;
                }
            }
        }

        $address
            ->setVicinity($details['vicinity'] ?? null)
            ->setLongitude($details['geometry']['location']['lng'] ?? null)
            ->setLatitude($details['geometry']['location']['lat'] ?? null);

        $this->em->persist($address);
    }

    /**
     * @param array<int, GasStation> $gasStations
     */
    public function createAnomalies(array $gasStations): void
    {
        foreach ($gasStations as $gasStationAnomaly) {
            $this->messageBus->dispatch(new CreateGooglePlaceAnomalyMessage(
                new GasStationId($gasStationAnomaly->getId())
            ), [new AmqpStamp('async-priority-high', 0, [])]);
        }
    }
}