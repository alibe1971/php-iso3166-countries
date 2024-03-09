<?php

namespace Alibe\GeoCodes\Lib;

use Alibe\GeoCodes\Lib\DataObj\Countries;
use Alibe\GeoCodes\Lib\Enums\DataSets\Access;
use Alibe\GeoCodes\Lib\Enums\DataSets\Index;
use Alibe\GeoCodes\Lib\Enums\DataSets\Source;
use Alibe\GeoCodes\Lib\Enums\DataSets\Type;

class CodesCountries extends Enquiries
{
    /**
     * @var string
     */
    protected string $dataSetName = 'countries';

    /**
     * @var string
     */
    protected string $instanceName = Countries::class;

    /**
     * @var array<string, array<string, bool|string|null>>
     */
    protected array $dataSetsStructure = [
        'alpha2' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::PRIMARY,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The ISO-3166-1 alpha-2 code (2 letters)'
        ],
        'alpha3' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::INDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The ISO-3166-1 alpha-3 code (3 letters)'
        ],
        'unM49' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::INDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The United Nations Statistics Division M49 code (numeric)'
        ],
        'name' => [
            'source' => Source::TRANSLATIONS,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The common name of the country'
        ],
        'completeName' => [
            'source' => Source::TRANSLATIONS,
            'type' => Type::STRING,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => true,
            'description' => 'The complete name of the country'
        ],
        'officialName' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The country\'s official name(s) in its administrative language(s).'
        ],
        'dependency' => [
            'source' => Source::DATA,
            'type' => Type::STRING,
            'nullable' => true,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'Territorial dependence (if any)'
        ],
        'mottos' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => true,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The mottos of the country'
        ],
        'currencies' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The currencies used in the country'
        ],
        'dialCodes' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The dial codes for phone call to the country'
        ],
        'timeZones' => [
            'source' => Source::DATA,
            'type' => Type::ARRAY,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The time zones present in the country'
        ],
        'languages' => [
            'source' => Source::DATA,
            'type' => Type::OBJECT,
            'nullable' => true,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PRIVATE,
            'search' => false,
            'description' => 'The languages used in the country'
        ],
        'locales' => [
            'source' => Source::DATA,
            'type' => Type::ARRAY,
            'nullable' => false,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The PHP locales used in the country'
        ],
        'demonyms' => [
            'source' => Source::TRANSLATIONS,
            'type' => Type::ARRAY,
            'nullable' => true,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PUBLIC,
            'search' => false,
            'description' => 'The demonysms; the names of the inhabitants'
        ],
        'keywords' => [
            'source' => Source::TRANSLATIONS,
            'type' => Type::ARRAY,
            'nullable' => true,
            'index' => Index::NOTINDEXABLE,
            'access' => Access::PRIVATE,
            'search' => true,
            'description' => null
        ]
    ];
}
