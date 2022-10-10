<?php

namespace App\Common\EntityId;

final class GasStationId
{
    public function __construct(
        private string $id
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }
}
