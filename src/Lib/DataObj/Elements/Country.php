<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;
use Alibe\GeoCodes\Lib\DataObj\Locales;
use Alibe\GeoCodes\Lib\DataObj\TimeZones;

class Country extends BaseDataObj
{
    protected function getObjectStructureParser(): array
    {
        return [
            'officialName' => Languages::class,
            'alpha2' => 'string',
            'alpha3' => 'string',
            'unM49' => 'string',
            'dependency' => 'string',
            'mottos' => CountryMottos::class,
            'currencies' => CountryCurrencies::class,
            'dialCodes' => CountryDialCodes::class,
            'timeZones' => TimeZones::class,
            'languages' => 'string', //'string',
            'locales' => Locales::class
        ];
    }
}
