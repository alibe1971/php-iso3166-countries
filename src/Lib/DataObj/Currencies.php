<?php

namespace Alibe\GeoCodes\Lib\DataObj;

use Alibe\GeoCodes\Lib\DataObj\Elements\Currency;

class Currencies extends BaseDataObj
{
    /**
     * @var string
     */
    protected string $xmlRootElement = "currencies";

    /**
     * @return array<string, array<string, array<string, array<string, array<string, string>|string>>|string>>
     */
    protected function getXmlMap(): array
    {
        return [
            'currencies' =>  array_merge(
                [
                    "@tag" => "currency",
                    "@attribute" => "index"
                ],
                (new Currency())->getXmlMap()
            )
        ];
    }

    /**
     * @return array<int, array<int, string>>
     */
    protected function getObjectStructureParser(): array
    {
        return [ [Currency::class] ];
    }
}
