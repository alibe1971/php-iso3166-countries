<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;
use Alibe\GeoCodes\Lib\DataObj\Locales;
use Alibe\GeoCodes\Lib\DataObj\TimeZones;

class Country extends BaseDataObj
{
    /**
     * @var string
     */
    protected string $xmlRootElement = "country";

    /**
     * @return array<string, array<string, array<string, array<string, string>|string>>>
     */
    protected function getXmlMap(): array
    {
        return [
            'country' => [
                "officialName" => [
                    "@attribute" => "lang",
                    "@tag" => "name"
                ],
                "flags" => [
                    "@type" => [
                        "svg" => "CDATA"
                    ]
                ],
                "mottos" => [
                    "official" => [
                        "@attribute" => "lang",
                        "@tag" => "motto"
                    ]
                ],
                "currencies" => [
                    "legalTenders" => [
                        "@tag" => "currency"
                    ],
                    "widelyAccepted" => [
                        "@tag" => "currency"
                    ]
                ],
                "dialCodes" => [
                    "main" => [
                        "@tag" => "dial"
                    ],
                    "exceptions" => [
                        "@tag" => "dial"
                    ]
                ],
                "demonyms" => [
                    "@tag" => "demonym"
                ],
                "timeZones" => [
                    "@tag" => "tz"
                ],
                "locales" => [
                    "@tag" => "locale"
                ]
            ]
        ];
    }

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
            'flags' => CountryFlags::class,
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
