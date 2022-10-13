<?php

namespace App\Message;

use App\Common\EntityId\GasStationId;

final class CreateGooglePlaceDetailsMessage
{
    public function __construct(private GasStationId $gasStationId)
    {
    }

    public function getGasStationId(): GasStationId
    {
        return $this->gasStationId;
    }
}
