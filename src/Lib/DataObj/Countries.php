<?php

namespace Alibe\GeoCodes\Lib\DataObj;

use Alibe\GeoCodes\Lib\DataObj\Elements\Country;

class Countries extends BaseDataObj
{
    /**
     * @var string
     */
    protected string $xmlRootElement = "countries";

    /**
     * @return  array<string, array<string, array<string, array<string, array<string, string>|string>>|string>>
     */
    protected function getXmlMap(): array
    {
        return [
            'countries' =>  array_merge(
                [
                    "@tag" => "country",
                    "@attribute" => "index"
                ],
                (new Country())->getXmlMap()
            )
        ];
    }

    /**
     * @return array<int, array<int, string>>
     */
    protected function getObjectStructureParser(): array
    {
        return [ [Country::class] ];
    }
}
