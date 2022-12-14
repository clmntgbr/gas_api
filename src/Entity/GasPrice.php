<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\ApiResource\Controller\GasPriceByYearAndGasType;
use App\Repository\GasPriceRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: GasPriceRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get',
        'get_gas_price_year_gas_type' => [
            'method' => 'GET',
            'path' => '/gas_prices/year',
            'controller' => GasPriceByYearAndGasType::class,
            'pagination_enabled' => false,
            'deserialize' => false,
            'read' => false,
            'normalization_context' => ['skip_null_values' => false, 'groups' => ['read_gas_prices']],
        ],
    ],
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
    #[Groups(['read_gas_prices'])]
    private int $value;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['read_gas_prices'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd/m/Y h:i:s'])]
    private DateTimeImmutable $date;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['read_gas_prices'])]
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

    #[Groups(['read_gas_prices'])]
    public function getMonth(): int
    {
        return (int)$this->date->format('m');
    }

    #[Groups(['read_gas_prices'])]
    public function getDay(): int
    {
        return (int)$this->date->format('d');
    }

    #[Groups(['read_gas_prices'])]
    public function getYear(): int
    {
        return (int)$this->date->format('Y');
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