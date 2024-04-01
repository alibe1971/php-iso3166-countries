<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;

class GeoSet extends BaseDataObj
{
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
