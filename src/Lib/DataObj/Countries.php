<?php

namespace Alibe\GeoCodes\Lib\DataObj;

use Alibe\GeoCodes\Lib\DataObj\Elements\Country;

class Countries extends BaseDataObj
{
    protected function getObjectStructureParser(): array
    {
        return [ [Country::class] ];
    }
}
