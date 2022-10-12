<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
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
#[ApiResource]
#[Vich\Uploadable]
class GasStation
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(type: Types::STRING)]
    #[Groups(['read_gas_station'])]
    private string $id;

    #[ORM\Column(type: Types::STRING, length: 10)]
    #[Groups(['read_gas_station'])]
    private string $pop;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['read_gas_station'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['read_gas_station'])]
    private ?string $company = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Groups(['read_gas_station'])]
    private string $status;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['read_gas_station'])]
    private ?DateTimeImmutable $closedAt = null;

    #[ORM\OneToOne(targetEntity: Address::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read_gas_station'])]
    private Address $address;

    #[ORM\ManyToOne(targetEntity: GooglePlace::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read_gas_station'])]
    private GooglePlace $googlePlace;

    #[ORM\Column(type: Types::JSON)]
    private array $element = [];

    #[ORM\OneToMany(mappedBy: 'gasStation', targetEntity: GasPrice::class)]
    private Collection $gasPrices;

    #[ORM\ManyToMany(targetEntity: GasService::class, mappedBy: 'gasStations', cascade: ['persist'])]
    private Collection $gasServices;

    #[Vich\UploadableField(mapping: 'gas_station_image', fileNameProperty: 'image.name', size: 'image.size', mimeType: 'image.mimeType', originalName: 'image.originalName', dimensions: 'image.dimensions')]
    private ?File $imageFile = null;

    #[ORM\Embedded(class: 'Vich\UploaderBundle\Entity\File')]
    private EmbeddedFile $image;

    #[ORM\Column(type: Types::JSON)]
    private array $lastGasPrices = [];

    private array $lastGasPricesDecode = [];

    public function __construct()
    {
        $this->image = new EmbeddedFile();
        $this->gasPrices = new ArrayCollection();
        $this->gasServices = new ArrayCollection();
        $this->lastGasPrices = [];
        $this->lastGasPricesDecode = [];
    }

    #[Groups(['read_gas_station'])]
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
     * @return GasPrice[]
     */
    public function getLastGasPricesDecode()
    {
        return $this->lastGasPricesDecode;
    }

    public function setLastGasPricesDecode(GasType $gasType, GasPrice $gasPrice): self
    {
        $this->lastGasPricesDecode[$gasType->getId()] = $gasPrice;

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getLastGasPrices()
    {
        return $this->lastGasPrices;
    }

    public function setLastGasPrices(GasPrice $gasPrice): self
    {
        $this->lastGasPrices[$gasPrice->getGasType()->getId()] = $this->hydrateGasPrices($gasPrice);
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
