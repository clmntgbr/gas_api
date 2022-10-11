<?php

namespace App\Common\Exception;

use Exception;

class GasPriceUpdateServiceException extends Exception
{
    public const GAS_PRICE_INSTANT_EMPTY = 'Gas Price Instant file is empty.';
}
