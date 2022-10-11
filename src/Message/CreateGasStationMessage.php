<?php

namespace App\Message;

use App\Common\EntityId\GasStationId;

final class CreateGasStationMessage
{
    /**
     * @param array<mixed> $element
     */
    public function __construct(
        private GasStationId $gasStationId,
        private string $pop,
        private string $cp,
        private ?string $longitude,
        private ?string $latitude,
        private string $street,
        private string $city,
        private string $country,
        private array $element
    ) {
    }

    public function getGasStationId(): GasStationId
    {
        return $this->gasStationId;
    }

    public function getPop(): string
    {
        return $this->pop;
    }

    public function getCp(): string
    {
        return $this->cp;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return array<mixed>
     */
    public function getElement()
    {
        return $this->element;
    }
}
