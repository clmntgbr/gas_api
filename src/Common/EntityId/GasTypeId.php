<?php

namespace App\Common\EntityId;

final class GasTypeId
{
    public function __construct(
        private int $id
    )
    {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
