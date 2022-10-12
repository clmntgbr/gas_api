<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\GooglePlaceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GooglePlaceRepository::class)]
#[ApiResource(
    collectionOperations: [],
    itemOperations: ['get']
)]
class GooglePlace
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['read_gas_stations'])]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 15, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private ?string $googleId = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private ?string $url = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private ?string $website = null;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private ?string $phoneNumber = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private ?string $placeId = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private ?string $compoundCode = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private ?string $globalCode = null;

    #[ORM\Column(type: Types::STRING, length: 10, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private ?string $googleRating = null;

    #[ORM\Column(type: Types::STRING, length: 10, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private ?string $rating = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private ?string $reference = null;

    #[ORM\Column(type: Types::STRING, length: 10, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private ?string $userRatingsTotal = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private ?string $icon = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private ?string $businessStatus = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private array $openingHours = [];

    public function __construct()
    {
        $this->id = rand();
    }

    public function __toString(): string
    {
        return $this->placeId ?? $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): self
    {
        $this->googleId = $googleId;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getPlaceId(): ?string
    {
        return $this->placeId;
    }

    public function setPlaceId(?string $placeId): self
    {
        $this->placeId = $placeId;

        return $this;
    }

    public function getCompoundCode(): ?string
    {
        return $this->compoundCode;
    }

    public function setCompoundCode(?string $compoundCode): self
    {
        $this->compoundCode = $compoundCode;

        return $this;
    }

    public function getGlobalCode(): ?string
    {
        return $this->globalCode;
    }

    public function setGlobalCode(?string $globalCode): self
    {
        $this->globalCode = $globalCode;

        return $this;
    }

    public function getGoogleRating(): ?string
    {
        return $this->googleRating;
    }

    public function setGoogleRating(?string $googleRating): self
    {
        $this->googleRating = $googleRating;

        return $this;
    }

    public function getRating(): ?string
    {
        return $this->rating;
    }

    public function setRating(?string $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getUserRatingsTotal(): ?string
    {
        return $this->userRatingsTotal;
    }

    public function setUserRatingsTotal(?string $userRatingsTotal): self
    {
        $this->userRatingsTotal = $userRatingsTotal;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getBusinessStatus(): ?string
    {
        return $this->businessStatus;
    }

    public function setBusinessStatus(?string $businessStatus): self
    {
        $this->businessStatus = $businessStatus;

        return $this;
    }

    /**
     * @return array<mixed>|null
     */
    public function getOpeningHours(): ?array
    {
        return $this->openingHours;
    }

    /**
     * @param array<mixed>|null $openingHours
     */
    public function setOpeningHours(?array $openingHours): self
    {
        $this->openingHours = $openingHours;

        return $this;
    }
}
