<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\ApiResource\Controller\GasStationsMap;
use App\Repository\GasStationRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: GasStationRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => ['normalization_context' => ['skip_null_values' => false, 'groups' => ['read_gas_stations']]],
        'get_gas_stations_map' => [
            'method' => 'GET',
            'path' => '/gas_stations/map',
            'controller' => GasStationsMap::class,
            'pagination_enabled' => false,
            'deserialize' => false,
            'read' => false,
            'normalization_context' => ['skip_null_values' => false, 'groups' => ['read_gas_stations']],
        ],
    ],
    itemOperations: ['get'],
)]
#[ApiFilter(
    SearchFilter::class, properties: ['id' => 'exact', 'status' => 'exact']
)]
#[Vich\Uploadable]
class GasStation
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(type: Types::STRING)]
    #[Groups(['read_gas_stations'])]
    private string $id;

    #[ORM\Column(type: Types::STRING, length: 10)]
    #[Groups(['read_gas_stations'])]
    private string $pop;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private ?string $company = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private ?array $statuses;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private ?string $status;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['read_gas_station'])]
    private ?DateTimeImmutable $closedAt = null;

    #[ORM\OneToOne(targetEntity: Address::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read_gas_stations'])]
    private Address $address;

    #[ORM\ManyToOne(targetEntity: GooglePlace::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read_gas_stations'])]
    private GooglePlace $googlePlace;

    #[ORM\Column(type: Types::JSON)]
    private array $element = [];

    #[ORM\OneToMany(mappedBy: 'gasStation', targetEntity: GasPrice::class, cascade: ['persist', 'remove'])]
    private Collection $gasPrices;

    #[ORM\ManyToMany(targetEntity: GasService::class, mappedBy: 'gasStations', cascade: ['persist', 'remove'])]
    #[Groups(['read_gas_stations'])]
    private Collection $gasServices;

    #[Vich\UploadableField(mapping: 'gas_stations_image', fileNameProperty: 'image.name', size: 'image.size', mimeType: 'image.mimeType', originalName: 'image.originalName', dimensions: 'image.dimensions')]
    private ?File $imageFile = null;

    #[ORM\Embedded(class: 'Vich\UploaderBundle\Entity\File')]
    private EmbeddedFile $image;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private ?array $lastGasPrices;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['read_gas_stations'])]
    private ?array $previousGasPrices;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $textsearchApiResult;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $placeDetailsApiResult;

    public function __construct()
    {
        $this->image = new EmbeddedFile();
        $this->gasPrices = new ArrayCollection();
        $this->gasServices = new ArrayCollection();
        $this->lastGasPrices = [];
        $this->previousGasPrices = [];
    }

    #[Groups(['read_gas_stations'])]
    public function getImagePath(): string
    {
        return sprintf('/images/%s', $this->getImage()->getName());
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPop(): ?string
    {
        return $this->pop;
    }

    public function setPop(string $pop): self
    {
        $this->pop = $pop;

        return $this;
    }

    public function getImage(): EmbeddedFile
    {
        return $this->image;
    }

    public function setImage(EmbeddedFile $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): self
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, GasService>
     */
    public function getGasServices(): Collection
    {
        return $this->gasServices;
    }

    public function hasGasService(GasService $gasService): bool
    {
        return $this->gasServices->contains($gasService);
    }

    public function addGasService(GasService $gasService): self
    {
        if (!$this->gasServices->contains($gasService)) {
            $this->gasServices[] = $gasService;
            $gasService->addGasStation($this);
        }

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getLastGasPrices(): array
    {
        return $this->lastGasPrices;
    }

    public function setLastGasPrices(GasPrice $gasPrice): self
    {
        if (array_key_exists($gasPrice->getGasType()->getId(), $this->lastGasPrices) && $this->lastGasPrices[$gasPrice->getGasType()->getId()] !== null) {
            $this->previousGasPrices[$gasPrice->getGasType()->getId()] = $this->lastGasPrices[$gasPrice->getGasType()->getId()];
        }

        $this->lastGasPrices[$gasPrice->getGasType()->getId()] = $this->hydrateGasPrices($gasPrice);

        return $this;
    }

    /**
    * @return array<mixed>
    */
    public function getPreviouusGasPrices()
    {
        return $this->previousGasPrices;
    }

    public function setPreviousGasPrices(GasPrice $gasPrice): self
    {
        $this->previousGasPrices[$gasPrice->getGasType()->getId()] = $this->hydrateGasPrices($gasPrice);

        return $this;
    }

    public function removeGasService(GasService $gasService): self
    {
        if ($this->gasServices->removeElement($gasService)) {
            $gasService->removeGasStation($this);
        }

        return $this;
    }

    private function hydrateGasPrices(GasPrice $gasPrice)
    {
        return [
            'id' => $gasPrice->getId(),
            'datetimestamp' => $gasPrice->getDateTimestamp(),
            'gasPriceValue' => $gasPrice->getValue(),
            'gasTypeId' => $gasPrice->getGasType()->getId(),
            'gasTypeLabel' => $gasPrice->getGasType()->getLabel(),
            'currency' => $gasPrice->getCurrency()->getLabel(),
        ];
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getClosedAt(): ?DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function setClosedAt(?DateTimeImmutable $closedAt): self
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    public function getElement(): array
    {
        return $this->element;
    }

    public function setElement(array $element): self
    {
        $this->element = $element;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getGooglePlace(): ?GooglePlace
    {
        return $this->googlePlace;
    }

    public function setGooglePlace(?GooglePlace $googlePlace): self
    {
        $this->googlePlace = $googlePlace;

        return $this;
    }

    /**
     * @return Collection<int, GasPrice>
     */
    public function getGasPrices(): Collection
    {
        return $this->gasPrices;
    }

    public function addGasPrice(GasPrice $gasPrice): self
    {
        if (!$this->gasPrices->contains($gasPrice)) {
            $this->gasPrices->add($gasPrice);
            $gasPrice->setGasStation($this);
        }

        return $this;
    }

    public function removeGasPrice(GasPrice $gasPrice): self
    {
        if ($this->gasPrices->removeElement($gasPrice)) {
            // set the owning side to null (unless already changed)
            if ($gasPrice->getGasStation() === $this) {
                $gasPrice->setGasStation(null);
            }
        }

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        $this->setStatuses($status);

        return $this;
    }

    public function getStatuses(): ?array
    {
        return $this->statuses;
    }

    public function setStatuses(string $status): self
    {
        $this->statuses[] = $status;

        return $this;
    }

    public function getPreviousStatus(): ?string
    {
        if (count($this->statuses) <= 1) {
            return end($this->statuses);
        }

        return $this->status[count($this->statuses) - 2];
    }

    public function getLastGasPricesAdmin()
    {
        $string = '';
        foreach ($this->lastGasPrices as $gasPrice) {
            $gasPrice['date'] = (new DateTime())->setTimestamp($gasPrice['datetimestamp'])->format('Y-m-d h:s:i');
            unset($gasPrice['datetimestamp']);
            unset($gasPrice['gasTypeId']);
            unset($gasPrice['currency']);
            $string .= json_encode($gasPrice) . '<br>';
        }

        return $string;
    }

    public function getPreviousGasPricesAdmin()
    {
        $string = '';
        foreach ($this->previousGasPrices as $gasPrice) {
            $gasPrice['date'] = (new DateTime())->setTimestamp($gasPrice['datetimestamp'])->format('Y-m-d h:s:i');
            unset($gasPrice['datetimestamp']);
            unset($gasPrice['gasTypeId']);
            unset($gasPrice['currency']);
            $string .= json_encode($gasPrice) . '<br>';
        }

    return $string;
    }

    public function getPlaceDetailsApiResult(): ?array
    {
        return $this->placeDetailsApiResult;
    }

    public function setPlaceDetailsApiResult(?array $placeDetailsApiResult): self
    {
        $this->placeDetailsApiResult = $placeDetailsApiResult;

        return $this;
    }

    public function getTextsearchApiResult(): ?array
    {
        return $this->textsearchApiResult;
    }

    public function setTextsearchApiResult(?array $textsearchApiResult): self
    {
        $this->textsearchApiResult = $textsearchApiResult;

        return $this;
    }

    public function getGooglePlaceId(): ?string
    {
        return $this->googlePlace?->getPlaceId();
    }
}
