<?php

namespace App\Dto;

use App\Entity\GasStation;
use Symfony\Component\Serializer\Annotation\Groups;

class GasStationsMapDto
{
    #[Groups(["read_gas_stations"])]
    /** @var GasStation[] */
    public $gasStations;

    public function __construct(array $gasStations)
    {
        $this->gasStations = $gasStations;
    }

    public function addGasStations(GasStation $gasStation)
    {
        $this->gasStations[] = $gasStation;
        return $this;
    }

    public function setGasStations(array $gasStations)
    {
        $this->gasStations = $gasStations;
        return $this;
    }

    public function getGasStations()
    {
        return $this->gasStations;
    }
}