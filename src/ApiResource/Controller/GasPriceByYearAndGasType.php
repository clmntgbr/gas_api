<?php

namespace App\ApiResource\Controller;

use App\Entity\GasPrice;
use App\Repository\GasPriceRepository;
use App\Repository\GasStationRepository;
use App\Repository\GasTypeRepository;
use Exception;
use Safe\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GasPriceByYearAndGasType extends AbstractController
{
    public static string $operationName = 'get_gas_price_year_gas_type';

    public function __construct(
        private GasStationRepository $gasStationRepository,
        private GasPriceRepository $gasPriceRepository,
        private GasTypeRepository $gasTypeRepository
    ) {
    }

    public function __invoke(Request $request)
    {
        $gasStationId = $request->query->get('gas_station_id') ?? null;
        $gasTypeId = $request->query->get('gas_type_id') ?? null;
        $year = $request->query->get('year') ?? (new DateTime('now'))->format('Y');

        if (null === $gasStationId) {
            throw new Exception('Missing `gas_station_id` parameter.');
        }

        if (null === $gasTypeId) {
            throw new Exception('Missing `gas_type_id` parameter.');
        }

        $gasStation = $this->gasStationRepository->findOneBy(['id' => $gasStationId]);
        if (null === $gasStation) {
            throw new Exception('Wrong `gas_station_id` parameter.');
        }

        $gasType = $this->gasTypeRepository->findOneBy(['id' => $gasTypeId]);
        if (null === $gasType) {
            throw new Exception('Wrong `gas_type_id` parameter.');
        }

        $datas = [];

        $gasPrices = $this->gasPriceRepository->findGasPricesByYear($gasStation, $gasType, $year);

        foreach ($gasPrices as $gasPrice) {
            if (array_key_exists(0, $gasPrice) && $gasPrice[0] instanceof GasPrice) {
                $datas[] = $gasPrice[0];
            }
        }

        return $datas;
    }
}