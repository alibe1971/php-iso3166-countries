<?php

namespace Alibe\GeoCodes\Lib\DataObj;

use Alibe\GeoCodes\Lib\DataObj\Elements\GeoSet;

class GeoSets extends BaseDataObj
{
    /**
     * @var string
     */
    protected string $xmlRootElement = "geoSets";

    /**
     * @return  array<string, array<string, array<string, array<string, array<string, string>|string>>|string>>
     */
    protected function getXmlMap(): array
    {
        return [
            'geoSets' =>  array_merge(
                [
                    "@tag" => "geoSet",
                    "@attribute" => "index"
                ],
                (new GeoSet())->getXmlMap()
            )
        ];
    }
    /**
     * @return array<int, array<int, string>>
     */
    protected function getObjectStructureParser(): array
    {
        return [ [GeoSet::class] ];
    }
}
