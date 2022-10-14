<?php

namespace App\ApiResource\Controller;

use App\Dto\GasStationsMapDto;
use App\Repository\GasStationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GasStationsMap extends AbstractController
{
    public static string $operationName = 'get_gas_stations_map';

    public function __construct(
        private GasStationRepository $gasStationRepository
    )
    {
    }

    public function __invoke(Request $request): ?GasStationsMapDto
    {
        $gasStations = $this->gasStationRepository->getGasStationsMap(2.358192, 48.764977, 5000);

        return new GasStationsMapDto($gasStations);
    }
}
