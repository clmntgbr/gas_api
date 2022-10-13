<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\GasPriceRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: GasPriceRepository::class)]
#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: ['get'],
)]
class GasPrice
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $value;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $date;

    #[ORM\Column(type: Types::INTEGER)]
    private int $dateTimestamp;

    #[ORM\ManyToOne(targetEntity: GasStation::class, inversedBy: 'gasPrices')]
    #[ORM\JoinColumn(nullable: false)]
    private GasStation $gasStation;

    #[ORM\ManyToOne(targetEntity: GasType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private GasType $gasType;

    #[ORM\ManyToOne(targetEntity: Currency::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Currency $currency;

    public function __toString(): string
    {
        return (string) $this->getId();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getDate(): ?DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDateTimestamp(): ?int
    {
        return $this->dateTimestamp;
    }

    public function setDateTimestamp(int $dateTimestamp): self
    {
        $this->dateTimestamp = $dateTimestamp;

        return $this;
    }

    public function getGasStation(): ?GasStation
    {
        return $this->gasStation;
    }

    public function setGasStation(?GasStation $gasStation): self
    {
        $this->gasStation = $gasStation;

        return $this;
    }

    public function getGasType(): GasType
    {
        return $this->gasType;
    }

    public function setGasType(GasType $gasType): self
    {
        $this->gasType = $gasType;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }
}
