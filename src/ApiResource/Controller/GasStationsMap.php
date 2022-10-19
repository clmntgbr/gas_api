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
    ) {
    }

    public function __invoke(Request $request): ?GasStationsMapDto
    {
        $latitude = $request->query->get('latitude') ?? 48.856614;
        $longitude = $request->query->get('longitude') ?? 2.3522219;
        $radius = $request->query->get('radius') ?? 10000;

        $gasStations = $this->gasStationRepository->getGasStationsMap($longitude, $latitude, $radius, $request->query->all());

        return new GasStationsMapDto($gasStations);
    }
}
