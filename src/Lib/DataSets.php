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


    /**
     * Validate an SVG
     * @param $svgContent
     * @return bool
     */
    public static function isValidSVG(string $svgContent): bool
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadXML($svgContent, LIBXML_NOERROR | LIBXML_NOWARNING);
        $isValid = $dom->documentElement && $dom->documentElement->tagName === 'svg';
        libxml_clear_errors();
        libxml_use_internal_errors(false);

        return $isValid;
    }
}
