<?php

namespace Alibe\GeoCodes\Lib\DataObj;

use Alibe\GeoCodes\Lib\DataObj\Elements\GeoSet;

class GeoSets extends BaseDataObj
{
    /**
     * @return array<int, array<int, string>>
     */
    protected function getObjectStructureParser(): array
    {
        return [ [GeoSet::class] ];
    }
}
