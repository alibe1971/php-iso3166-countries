<?php

namespace Alibe\GeoCodes\Lib;

use Alibe\GeoCodes\Lib\Enums\DataSets\Source;

class DataSets
{
    /**
     * @var array<string, mixed>
     */
    public static array $dataSets = [
        Source::DATA => [],
        Source::TRANSLATIONS => []
    ];


    /**
     * Get the data from the database.
     * @param string $file
     * @return array<string, mixed>
     */
    public static function getData(string $file): array
    {
        return include(dirname(__DIR__) . '/Data/' . $file . '.php');
    }
}
