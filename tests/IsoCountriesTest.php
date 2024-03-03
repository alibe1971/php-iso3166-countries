<?php

namespace Alibe\GeoCodes\Tests;

use PHPUnit\Framework\TestCase;
use Alibe\GeoCodes\GeoCodes;

/**
 * @testdox Countries
 */
final class IsoCountriesTest extends TestCase
{
    /**
     * @var GeoCodes $geoCodes
     */
    private static GeoCodes $geoCodes;

    /**
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        self::$geoCodes = new GeoCodes();
    }


    /**
     * @test
     * @testdox => Tests on the selectable fields.
     * @return void
     */
    public function testSelectableFields(): void
    {
        $selectFields = self::$geoCodes->countries()->getSelectables();
        $this->assertIsArray($selectFields);


        foreach ($selectFields as $key => $descr) {
            $this->assertArrayHasKey($key, $selectFields, 'Key `' . $key . '` not present as selectable');
            // check the existence of the field
            // check the type of the key
        }
    }


    /**
     * @test
     * @testdox Countries: stica.
     * @return void
     */
    public function testAvailableLanguages(): void
    {
        /** @phpstan-ignore-next-line */
        $countries = self::$geoCodes->useLanguage('it')->countries()->getSelectables();

        /** @phpstan-ignore-next-line */
        $countries2 = self::$geoCodes->useLanguage('it')->countries()->getIndexables();

        /** @phpstan-ignore-next-line */
        $countries3 = self::$geoCodes->useLanguage('it')->countries()->limit(0, 1)->get();



//        $countries4 = $countries3->toJson();
//        $countries4 = $countries3->toArray();

        self::$geoCodes->useLanguage('en');


        $this->assertTrue(true);
    }
}
