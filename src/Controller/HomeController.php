<?php

namespace App\Controller;

use App\Repository\GasStationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('Home/index.html.twig', [
        ]);
    }

    #[Route('/test', name: 'app_test')]
    public function test(GasStationRepository $gasStationRepository): Response
    {
        $gasStations = $gasStationRepository->getGasStationsMap(2.358192, 48.764977, 5000);
        dd($gasStations);

        return $this->render('Home/test.html.twig', [
        ]);
    }
}
