<?php

namespace App\Controller;

use App\Repository\GasPriceRepository;
use App\Repository\GasStationRepository;
use App\Repository\GasTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private GasStationRepository $gasStationRepository,
        private GasPriceRepository $gasPriceRepository,
        private GasTypeRepository $gasTypeRepository
    ) {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('Home/index.html.twig', []);
    }

    #[Route('/test', name: 'app_test')]
    public function test(GasStationRepository $gasStationRepository, EntityManagerInterface $em): Response
    {
        $gg = $gasStationRepository->findOneBy(['id' => 94150006]);

        $gasType = $this->gasTypeRepository->findOneBy(['id' => 1]);
        if (null === $gasType) {
        }

        dd($this->gasPriceRepository->findGasPricesByYear($gg, $gasType, 2020));

        dd($gg);

        return $this->render('Home/test.html.twig', []);
    }
}