<?php

namespace App\Message;

use App\Common\EntityId\GasStationId;
use App\Common\EntityId\GasTypeId;

final class CreateGasPriceMessage
{
    public function __construct(
        private GasStationId $gasStationId,
        private GasTypeId    $gasTypeId,
        private ?string       $date,
        private ?string       $value
    )
    {
    }

    public function getGasStationId(): GasStationId
    {
        return $this->gasStationId;
    }

    public function getGasTypeId(): GasTypeId
    {
        return $this->gasTypeId;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }
}
