<?php

namespace App\Services;

use App\Entity\GasStation;
use GuzzleHttp\Client;
use Safe;
use Symfony\Component\HttpFoundation\Response;

class ApiAddressService
{
    public function __construct(
        private string $apiAddressUrl
    ) {
    }

    public function update(GasStation $gasStation): void
    {
        $client = new Client();

        $response = $client->request(
            'GET',
            sprintf($this->apiAddressUrl, trim(strtolower(str_replace([',', 'France'], '', $gasStation->getAddress()->getStreet()))))
        );

        $content = Safe\json_decode($response->getBody()->getContents(), true);

        if (Response::HTTP_OK !== $response->getStatusCode()) {
            return;
        }

        if (array_key_exists('features', $content) && count($content['features']) > 0) {
            $result = $content['features'][0];
            if (array_key_exists('properties', $result) && array_key_exists('score', $result['properties'])) {
                if ($result['properties']['score'] > 0.85) {
                    $this->updateAddress($gasStation, $result);
                }
            }
        }
    }

    /**
     * @param array<mixed> $data
     */
    private function updateAddress(GasStation $gasStation, array $data): void
    {
        $address = $gasStation->getAddress();

        if (array_key_exists('geometry', $data) && array_key_exists('coordinates', $data['geometry']) && count($data['geometry']['coordinates']) > 1) {
            $address
                ->setLongitude($data['geometry']['coordinates'][0])
                ->setLatitude($data['geometry']['coordinates'][1]);
        }
    }
}