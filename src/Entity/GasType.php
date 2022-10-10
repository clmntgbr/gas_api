<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\GasTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GasTypeRepository::class)]
#[ApiResource]
class GasType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
