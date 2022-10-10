<?php

namespace App\Common\Exception;

use Exception;

class GasStationException extends Exception
{
    const GAS_STATION_ID_EMPTY = 'Gas Station Id is empty.';
    const GAS_STATION_INFORMATION_NOT_FOUND = 'Gas Station not found on gov map.';
}
