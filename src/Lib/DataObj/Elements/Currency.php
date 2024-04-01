<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;

class Currency extends BaseDataObj
{
    /**
     * @return array<string, mixed>
     */
    protected function getObjectStructureParser(): array
    {
        return [
            'isoAlpha' => 'string',
            'isoNumber' => 'string',
            'name' => 'string',
            'symbol' => 'string',
            'decimal' => 'integer'
        ];
    }
}
