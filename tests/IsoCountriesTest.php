<?php

namespace Alibe\GeoCodes\Tests;

use PHPUnit\Framework\TestCase;
use Alibe\GeoCodes\GeoCodes;

/**
 * @testdox Countries
 */
final class IsoCountriesTest extends TestCase
{
    private static GeoCodes $geoCodes;

    public static function setUpBeforeClass(): void
    {
        self::$geoCodes = new GeoCodes();
    }


    /**
     * @test
     * @testdox => Tests on the selectable fields.
     */
    public function testSelectableFields()
    {
        $selectFileds = self::$geoCodes->countries()->getSelectables();
        $this->assertIsArray($selectFileds);


        foreach ($selectFileds as $key => $descr) {
            $this->assertArrayHasKey($key, $selectFileds, 'Key `' . $key . '` not present as selectable');
            // check the existence of the field
            // check the type of the key
        }
    }


    /**
     * @test
     * @testdox Countries: stica.
     */
    public function testAvailableLanguages()
    {
        $countries = self::$geoCodes->useLanguage('it')->countries()->getSelectables();
        $countries2 = self::$geoCodes->useLanguage('it')->countries()->getIndexables();

        $countries3 = self::$geoCodes->useLanguage('it')->countries()->limit(0, 1)->get();
//        $countries4 = $countries3->toJson();
//        $countries4 = $countries3->toArray();

        $elenaMyfile = fopen("/Users/aliberati/ALIBE/test.log", "a") or die("Unable to open file!");
        fwrite($elenaMyfile, print_r($countries3, true) . "\n");
        fclose($elenaMyfile);


        self::$geoCodes->useLanguage('en');


        $this->assertTrue(true);
    }
}
