<?php

namespace App\Lists;

use App\Common\Traits\ListTrait;

class GasStationStatusReference
{
    use ListTrait;

    public const CREATED = 'created';

    public const FOUND_ON_GOV_MAP = 'found_on_gov_map';
    public const NOT_FOUND_ON_GOV_MAP = 'not_found_on_gov_map';
    public const FOUND_IN_TEXTSEARCH = 'found_in_textSearch';
    public const NOT_FOUND_IN_TEXTSEARCH = 'not_found_in_textSearch';
    public const NOT_FOUND_IN_DETAILS = 'not_found_in_details';
    public const FOUND_IN_DETAILS = 'found_in_details';
    public const PLACE_ID_ANOMALY = 'place_id_anomaly';
    public const WAITING_VALIDATION = 'waiting_validation';
    public const OPEN = 'open';
    public const CLOSED = 'closed';
}
