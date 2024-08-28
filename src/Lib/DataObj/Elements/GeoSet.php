<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;

class GeoSet extends BaseDataObj
{
    /**
     * @var string
     */
    protected string $xmlRootElement = "geoSet";

    /**
     * @return array<string, array<string, array<string, array<string, string>|string>>>
     */
    protected function getXmlMap(): array
    {
        return [
            'geoSet' => [
                "tags" => [
                    "@tag" => "tag"
                ],
                "countryCodes" => [
                    "@tag" => "cc"
                ],
            ]
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getObjectStructureParser(): array
    {
        return [
            'internalCode' => 'string',
            'unM49' => 'string',
            'name' => 'string',
            'tags' => GeoSetTags::class,
            'countryCodes' => GeoSetCountryCodes::class
        ];
    }
}
