<?php

namespace Alibe\GeoCodes\Tests;

use PHPUnit\Framework\TestCase;
use Alibe\GeoCodes\GeoCodes;

final class IsoCurrenciesTest extends TestCase
{
    private static GeoCodes $geoCodes;

    public static function setUpBeforeClass(): void
    {
        self::$geoCodes = new GeoCodes();
    }


    /**
     * @test
     * @testdox GeoSets: stica.
     */
    public function testAvailableLanguages()
    {
        $countries = self::$geoCodes->useLanguage('it')->geoCurrencies()->getSelectables();
        $countries2 = self::$geoCodes->useLanguage('it')->geoCurrencies()->getIndexables();

        $elenaMyfile = fopen("/Users/aliberati/ALIBE/test.log", "a") or die("Unable to open file!");
        fwrite($elenaMyfile, print_r($countries, true) . "\n");
        fwrite($elenaMyfile, print_r($countries2, true) . "\n");
        fclose($elenaMyfile);


        self::$geoCodes->useLanguage('en');


        $this->assertTrue(true);
    }
}
