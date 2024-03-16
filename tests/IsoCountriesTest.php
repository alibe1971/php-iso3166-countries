<?php

namespace Alibe\GeoCodes\Tests;

use Alibe\GeoCodes\Lib\DataObj\Countries;
use Alibe\GeoCodes\Lib\DataObj\Elements\Country;
use PHPUnit\Framework\TestCase;
use Alibe\GeoCodes\GeoCodes;

/**
 * @testdox Countries
 */
final class IsoCountriesTest extends TestCase
{
    /**
     * @var int
     */
    private static int $countriesTotalCount = 249;

    /**
     * @var array<int|array<string>> $constants
     */
    private static array $constants = [
        'indexes' => [
            'alpha2',
            'alpha3',
            'unM49',
            'name',
            'completeName'
        ]
    ];

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
     * @var Countries
     */
    private static Countries $countryList;


    /**
     * @var Country
     */
    private static Country $country;

    /**
     * @test
     * @testdox Test `->get()` the list of countries is object as instance of Countries.
     * @return void
     */
    public function testToGetListOfCountries(): void
    {
        /** @phpstan-ignore-next-line   The base object is needed for php 7.4 */
        self::$countryList = self::$geoCodes->countries()->get();
        $this->assertIsObject(self::$countryList);
        $this->assertEquals(
            self::$countriesTotalCount,
            count(get_object_vars(self::$countryList)),
            "The number of the countries doesn't match with 249"
        );
        $this->assertInstanceOf(Countries::class, self::$countryList);
    }


    /**
     * @test
     * @testdox Test the elements of the list of countries is an instance of Country.
     * @depends testToGetListOfCountries
     * @return void
     */
    public function testToGetElementListOfCountries(): void
    {
        $this->assertIsObject(self::$countryList->{0});
        $this->assertInstanceOf(Country::class, self::$countryList->{0});
    }

    /**
     * @test
     * @testdox Test the `->get()->toJson()` feature.
     * @depends testToGetListOfCountries
     * @return void
     */
    public function testGetToJsonFeature(): void
    {
        $json = self::$countryList->toJson();
        $this->assertIsString($json);
        $decodedJson = json_decode($json, true);
        $this->assertNotNull($decodedJson, 'Not a valid JSON');
        $this->assertIsArray($decodedJson, 'Not a valid JSON');

        $json = self::$countryList->{0}->toJson();
        $this->assertIsString($json);
        $decodedJson = json_decode($json, true);
        $this->assertNotNull($decodedJson, 'Not a valid JSON');
        $this->assertIsArray($decodedJson, 'Not a valid JSON');
    }


    /**
     * @test
     * @testdox Test the `->get()->toArray()` feature.
     * @depends testToGetListOfCountries
     * @return void
     */
    public function testGetToArrayFeature(): void
    {
        $array = self::$countryList->toArray();
        $this->assertIsArray($array, 'Not a valid Array');

        $array = self::$countryList->{0}->toArray();
        $this->assertIsArray($array, 'Not a valid Array');
    }

    /**
     * @test
     * @testdox Test the `->get()->toFlatten()` feature (default separator `.`).
     * @depends testToGetListOfCountries
     * @return void
     */
    public function testGetToFlattenFeature(): void
    {
        $flatten = self::$countryList->toFlatten();
        $this->assertIsArray($flatten, 'Not a valid Array');
        foreach (
            [
            mt_rand(0, (self::$countriesTotalCount - 1)),
            mt_rand(0, (self::$countriesTotalCount - 1)),
            mt_rand(0, (self::$countriesTotalCount - 1)),
            mt_rand(0, (self::$countriesTotalCount - 1)),
            mt_rand(0, (self::$countriesTotalCount - 1))
            ] as $key
        ) {
            $this->assertEquals(self::$countryList->$key->alpha2, $flatten[$key . '.alpha2']);
            $this->assertEquals(self::$countryList->$key->alpha3, $flatten[$key . '.alpha3']);
            $this->assertEquals(self::$countryList->$key->unM49, $flatten[$key . '.unM49']);
            $this->assertEquals(self::$countryList->$key->name, $flatten[$key . '.name']);
        };
    }

    /**
     * @test
     * @testdox Test the `->get()->toFlatten('_')` feature, using custom separator.
     * @depends testToGetListOfCountries
     * @return void
     */
    public function testGetToFlattenFeatureCustomSeparator(): void
    {
        $flatten = self::$countryList->toFlatten('_');
        $this->assertIsArray($flatten, 'Not a valid Array');
        foreach (
            [
                     mt_rand(0, (self::$countriesTotalCount - 1)),
                     mt_rand(0, (self::$countriesTotalCount - 1)),
                     mt_rand(0, (self::$countriesTotalCount - 1)),
                     mt_rand(0, (self::$countriesTotalCount - 1)),
                     mt_rand(0, (self::$countriesTotalCount - 1))
                 ] as $key
        ) {
            $this->assertEquals(self::$countryList->$key->alpha2, $flatten[$key . '_alpha2']);
            $this->assertEquals(self::$countryList->$key->alpha3, $flatten[$key . '_alpha3']);
            $this->assertEquals(self::$countryList->$key->unM49, $flatten[$key . '_unM49']);
            $this->assertEquals(self::$countryList->$key->name, $flatten[$key . '_name']);
        };
    }

    /**
     * @test
     * @testdox Test the `->first()` feature as instance of Country.
     * @return void
     */
    public function testFirstFeature(): void
    {
        /** @phpstan-ignore-next-line   The simple object is needed for php 7.4 */
        self::$country = self::$geoCodes->countries()->first();

        $this->assertIsObject(self::$country);
        $this->assertInstanceOf(Country::class, self::$country);
    }


    /**
     * @test
     * @testdox Test the `->first()->toJson()` feature.
     * @depends testFirstFeature
     * @return void
     */
    public function testFirstToJsonFeature(): void
    {
        $json = self::$country->toJson();
        $this->assertIsString($json);
        $decodedJson = json_decode($json, true);
        $this->assertNotNull($decodedJson, 'Not a valid JSON');
        $this->assertIsArray($decodedJson, 'Not a valid JSON');
    }


    /**
     * @test
     * @testdox Test the `->first()->toArray()` feature.
     * @depends testFirstFeature
     * @return void
     */
    public function testFirstToArrayFeature(): void
    {
        $array = self::$country->toArray();
        $this->assertIsArray($array, 'Not a valid Array');
    }


    /**
     * @test
     * @testdox Test the indexes
     * @return void
     */
    public function testIndexes(): void
    {
        $indexes = self::$geoCodes->countries()->getIndexes();
        $this->assertIsArray($indexes);
        $indexes = array_keys($indexes);
        $this->assertEquals($indexes, self::$constants['indexes']);
    }
    /**
     * @dataProvider dataProviderIndexes
     * @testdox ==>  using $index as index
     */
    public function testIndexesWithDataProvider(string $index): void
    {
        foreach (
            /** @phpstan-ignore-next-line   The simple object is needed for php 7.4 */
            self::$geoCodes->countries()->withIndex($index)->limit(0, 1)->get()->toArray() as $key => $country
        ) {
            $this->assertEquals($key, $country[$index]);
        }

        $this->assertTrue(true);
    }
    /**
     * @phpstan-ignore-next-line
     */
    public function dataProviderIndexes(): array
    {
        return array_map(
            /** @phpstan-ignore-next-line */
            function (string $index) {
                return [$index];
            },
            (array) self::$constants['indexes']
        );
    }
}
