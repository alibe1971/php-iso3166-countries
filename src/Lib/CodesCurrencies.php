<?php

namespace Alibe\GeoCodes\Lib;

use Alibe\GeoCodes\Lib\DataObj\Currencies;
use Alibe\GeoCodes\Lib\DataObj\Elements\Currency;
use Alibe\GeoCodes\Lib\Enums\DataSets\Access;
use Alibe\GeoCodes\Lib\Enums\DataSets\Index;
use Alibe\GeoCodes\Lib\Enums\DataSets\Source;
use Alibe\GeoCodes\Lib\Enums\DataSets\Type;

class CodesCurrencies extends Enquiries
{
    /**
     * @var string
     */
    protected string $dataSetName = 'currencies';

    /**
     * @var string
     */
    protected string $instanceName = Currencies::class;

    /**
     * @var string
     */
    protected string $singleItemInstanceName = Currency::class;

    protected array $dataSetsStructure = [
        'isoAlpha' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::PRIMARY,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The ISO-4217 three letters code'
        ],
        'isoNumber' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::INDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The ISO-4217 numeric code'
        ],
        'name' => [
            'source' => Source::TRANSLATIONS,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::INDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The name of the currency'
        ],
        'symbol' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => true,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The symbol of the currency'
        ],
        'decimal' => [
            'source' => Source::DATA,
            'type' => Type::INTEGER,
            'nullable' => true,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The decimals of the currency'
        ]
    ];
}
