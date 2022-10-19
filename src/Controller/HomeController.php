<?php

namespace App\Controller;

use App\Repository\GasPriceRepository;
use App\Repository\GasStationRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('Home/index.html.twig', []);
    }

    #[Route('/test', name: 'app_test')]
    public function test(GasPriceRepository $gasPriceRepository): Response
    {
        $date = new DateTime('now');
        $date->setTimestamp(1262286000);

        $gg = $gasPriceRepository->findOneBy(['id' => 32173]);

        dd($date, $gg);

        return $this->render('Home/test.html.twig', []);
    }
}