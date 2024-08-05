<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;
use Alibe\GeoCodes\Lib\DataObj\Locales;
use Alibe\GeoCodes\Lib\DataObj\TimeZones;

class Country extends BaseDataObj
{
    /**
     * @return array<string, mixed>
     */
    protected function getObjectStructureParser(): array
    {
        return [
            'alpha2' => 'string',
            'alpha3' => 'string',
            'unM49' => 'string',
            'name' => 'string',
            'fullName' => 'string',
            'officialName' => Languages::class,
            'dependency' => 'string',
            'mottos' => CountryMottos::class,
            'currencies' => CountryCurrencies::class,
            'dialCodes' => CountryDialCodes::class,
            'ccTld' => 'string',
            'timeZones' => TimeZones::class,
            'languages' => 'string', //'string',
            'locales' => Locales::class,
            'demonyms' => Demonysm::class,
            'otherAppsIds' => CountryOtherAppsIds::class,

        ];
    }
}
