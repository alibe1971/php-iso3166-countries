<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;

class CountryMottos extends BaseDataObj
{
    protected function getObjectStructureParser(): array
    {
        return [
            'official' => Languages::class
        ];
    }
}
