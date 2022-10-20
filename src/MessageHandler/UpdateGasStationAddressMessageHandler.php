<?php

namespace App\MessageHandler;

use App\Entity\GasStation;
use App\Lists\GasStationStatusReference;
use App\Message\UpdateGasStationAddressMessage;
use App\Repository\GasStationRepository;
use App\Services\ApiAddressService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UpdateGasStationAddressMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private ApiAddressService $apiAddressService,
        private GasStationRepository $gasStationRepository
    ) {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function __invoke(UpdateGasStationAddressMessage $message): void
    {
        if (!$this->em->isOpen()) {
            $this->em = EntityManager::create($this->em->getConnection(), $this->em->getConfiguration());
        }

        $gasStation = $this->gasStationRepository->findOneBy(['id' => $message->getGasStationId()->getId()]);

        if (null === $gasStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station is null (id: %s)', $message->getGasStationId()->getId()));
        }

        $this->getGasStationInformationFromGovernment($gasStation);
        $this->apiAddressService->update($gasStation);

        $this->em->persist($gasStation);
        $this->em->flush();
    }

    public function getGasStationInformationFromGovernment(GasStation $gasStation): void
    {
        $client = new Client();

        $options = [
            'headers' => [
                'authority' => 'www.prix-carburants.gouv.fr',
                'content-length' => '0',
                'accept' => 'text/javascript, text/html, application/xml, text/xml, */*',
                'x-prototype-version' => '1.7',
                'x-requested-with' => 'XMLHttpRequest',
                'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36',
                'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'origin' => 'https://www.prix-carburants.gouv.fr',
                'sec-fetch-site' => 'same-origin',
                'sec-fetch-mode' => 'cors',
                'sec-fetch-dest' => 'empty',
                'referer' => 'https://www.prix-carburants.gouv.fr/',
                'accept-language' => 'fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7,pt;q=0.6,de-DE;q=0.5,de;q=0.4,ru;q=0.3,vi;q=0.2,la;q=0.1,es;q=0.1',
                'cookie' => 'PHPSESSID=74qmi76d5k6vk4uhal69k0qhf6; device_view=full; cookie_law=true; device_view=full',
            ],
        ];

        try {
            $response = $client->request(
                'GET',
                sprintf('https://www.prix-carburants.gouv.fr/map/recuperer_infos_pdv/%s', $gasStation->getId()),
                $options
            );
        } catch (GuzzleException $e) {
            $gasStation->setStatus(GasStationStatusReference::FOUND_ON_GOV_MAP);
        }

        $content = $response->getBody()->getContents();

        if ('No route found' === $content) {
            $gasStation->setStatus(GasStationStatusReference::FOUND_ON_GOV_MAP);

            return;
        }

        $values = trim(strip_tags(str_replace("\n", '/break/', $content)));
        $values = explode('/break/', $values);
        $values = array_map('trim', $values);
        $values = array_filter($values);

        if (isset($values[5]) && isset($values[6]) && isset($values[7]) && isset($values[8])) {
            $gasStation
                ->setName(htmlspecialchars_decode(ucwords(strtolower(trim($values[5])))))
                ->setCompany(htmlspecialchars_decode(ucwords(strtolower(trim($values[6])))));

            $address = $gasStation->getAddress();
            $address
                ->setStreet(htmlspecialchars_decode(sprintf('%s, %s, France', trim($values[7]), trim($values[8]))))
                ->setVicinity(htmlspecialchars_decode(sprintf('%s, %s, France', trim($values[7]), trim($values[8]))));

            $gasStation->setStatus(GasStationStatusReference::FOUND_ON_GOV_MAP);
        }
    }
}