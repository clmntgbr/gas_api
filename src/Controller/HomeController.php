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
        $gasStation = $gasStationRepository->findOneBy(['id' => 9100002]);
        $gasStations = $gasStationRepository->findAll();

        $gasStationsGrouped = [];
        foreach ($gasStations as $gasStation) {
            if (null !== $gasStation->getGooglePlace()->getPlaceId()) {
                $gasStationsGrouped[$gasStation->getGooglePlace()->getPlaceId()][] = '++';
            }
        }

        dd($gasStationsGrouped);
        dd($gasStation->getActualStatus(), $gasStation->getPreviousStatus());

        return $this->render('Home/test.html.twig', [
        ]);
    }
}
