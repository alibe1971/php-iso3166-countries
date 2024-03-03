<?php

namespace Alibe\GeoCodes\Lib\DataObj\Elements;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;
use Alibe\GeoCodes\Lib\DataObj\Currencies;

class CountryCurrencies extends BaseDataObj
{
    protected function getObjectStructureParser(): array
    {
        return [
            'legalTenders' => Currencies::class,
            'widelyAccepted' => Currencies::class,
        ];
    }
}
