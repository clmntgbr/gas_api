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
    public function test(GasPriceRepository $gasPriceRepository, GasStationRepository $gasStationRepository): Response
    {
        $gg = $gasStationRepository->findOneBy(['id' => 94400003]);

        dump($gg);
        $gg->setName(htmlspecialchars_decode($gg->getName()));
        dump($gg);
        die;
        return $this->render('Home/test.html.twig', []);
    }
}