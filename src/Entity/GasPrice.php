<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\GasPriceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GasPriceRepository::class)]
#[ApiResource]
class GasPrice
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
