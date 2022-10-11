<?php

namespace App\Message;

use App\Common\EntityId\GasStationId;

final class CreateGasServiceMessage
{
    public function __construct(
        private GasStationId $gasStationId,
        private string       $label
    )
    {
    }

    public function getGasStationId(): GasStationId
    {
        return $this->gasStationId;
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}
