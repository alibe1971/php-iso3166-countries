<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;
use Alibe\GeoCodes\Lib\DataObj\DialCodes;

class CountryDialCodes extends BaseDataObj
{
    protected function getObjectStructureParser(): array
    {
        return [
            'main' => DialCodes::class,
            'exceptions' => DialCodes::class,
        ];
    }
}
