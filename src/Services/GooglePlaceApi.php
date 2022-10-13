<?php

namespace App\Services;

use App\Entity\GasStation;
use GuzzleHttp\Client;

class GooglePlaceApi
{
    public function __construct(
        private string $googleApiKey,
        private string $placeTextsearchUrl,
        private string $placeDetailsUrl
    ) {
    }

    public function placeTextsearch(GasStation $gasStation)
    {
        $client = new Client();
        $response = $client->request('GET', sprintf($this->placeTextsearchUrl, $gasStation->getAddress()->getVicinity(), $this->googleApiKey));

        return $response;
    }

    public function placeDetails(GasStation $gasStation)
    {
        $client = new Client();
        $response = $client->request('GET', sprintf($this->placeDetailsUrl, $gasStation->getGooglePlace()->getPlaceId(), $this->googleApiKey));

        return $response;
    }
}
