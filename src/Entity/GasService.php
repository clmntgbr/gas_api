<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\GasServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GasServiceRepository::class)]
#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: ['get'],
    normalizationContext: ['groups' => ['read']]
)]
class GasService
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(["read"])]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 150)]
    #[Groups(["read"])]
    private string $reference;

    #[ORM\Column(type: Types::STRING, length: 150)]
    #[Groups(["read"])]
    private string $label;

    #[ORM\ManyToMany(targetEntity: GasStation::class, inversedBy: 'gasServices', fetch: 'EXTRA_LAZY')]
    #[ORM\JoinTable(name: 'gas_stations_services')]
    private Collection $gasStations;

    public function __construct()
    {
        $this->id = rand();
        $this->gasStations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->label;
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

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection<int, GasStation>
     */
    public function getGasStations(): Collection
    {
        return $this->gasStations;
    }

    public function addGasStation(GasStation $gasStation): self
    {
        if (!$this->gasStations->contains($gasStation)) {
            $this->gasStations[] = $gasStation;
        }

        return $this;
    }

    public function removeGasStation(GasStation $gasStation): self
    {
        $this->gasStations->removeElement($gasStation);

        return $this;
    }
}
