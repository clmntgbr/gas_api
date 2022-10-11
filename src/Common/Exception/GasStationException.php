<?php

namespace App\Common\Exception;

use Exception;

class GasStationException extends Exception
{
    public const GAS_STATION_ID_EMPTY = 'Gas Station Id is empty.';
    public const GAS_STATION_INFORMATION_NOT_FOUND = 'Gas Station not found on gov map.';
}
