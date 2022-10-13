<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AddressRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
#[ApiResource(
    collectionOperations: [],
    itemOperations: ['get']
)]
class Address
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['read_gas_stations'])]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private ?string $vicinity = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Groups(['read_gas_stations'])]
    private string $street;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private ?string $number = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Groups(['read_gas_stations'])]
    private string $city;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private ?string $region = null;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Groups(['read_gas_stations'])]
    private string $postalCode;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Groups(['read_gas_stations'])]
    private string $country;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Groups(['read_gas_stations'])]
    private string $longitude;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Groups(['read_gas_stations'])]
    private string $latitude;

    public function __construct()
    {
        $this->id = rand();
    }

    public function __toString(): string
    {
        return (string) $this->vicinity;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVicinity(): ?string
    {
        return $this->vicinity;
    }

    public function setVicinity(?string $vicinity): self
    {
        $this->vicinity = $vicinity;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }
}
