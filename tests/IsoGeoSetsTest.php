<?php

namespace Alibe\GeoCodes\Tests;

use Alibe\GeoCodes\Lib\DataObj\GeoSets;
use Alibe\GeoCodes\Lib\Exceptions\QueryException;
use PHPUnit\Framework\TestCase;
use Alibe\GeoCodes\GeoCodes;

/**
 * @testdox GeoSets
 */
final class IsoGeoSetsTest extends TestCase
{
    /**
     * @var int
     */
    private static int $geoSetsTotalCount = 249;

    /**
     * @var array<int|array<string>> $constants
     */
    private static array $constants = [
        'indexes' => [
            'internalCode',
            'name'
        ],
        'selectables' => [
            'internalCode',
            'unM49',
            'name',
            'tags',
            'countryCodes'
        ]
    ];

    /**
     * @var array<int, string> $expectedLimitTest
     */
    private static array $expectedLimitTest = [
        'WS',
        'YE'
    ];

    /**
     * @var array<string, array<string, string>> $expectedOrderByTest
     */
    private static array $expectedOrderByTest = [
        'internalCode' => [
            'ASC' => 'AD',
            'DESC' => 'ZW',
        ],
        'name' => [
            'ASC' => 'ABW',
            'DESC' => 'ZWE',
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
     * @var GeoSets
     */
    private static GeoSets $geoSetsList;


    /**
     * @var Geoset
     */
    private static GeoSet $geoSet;

    /**
     * @test
     * @testdox Test `->get()` the list of geoset is object as instance of GeoSets.
     * @return void
     */
    public function testToGetListOfCountries(): void
    {
        /** @phpstan-ignore-next-line   The unique object type is needed for php 7.4 */
        self::$geoSetsList = self::$geoCodes->geoSets()->get();
        $this->assertIsObject(self::$geoSetsList);
        $this->assertInstanceOf(GeoSets::class, self::$geoSetsList);
    }

//
//    /**
//     * @test
//     * @testdox Test the elements of the list of countries is an instance of Country.
//     * @depends testToGetListOfCountries
//     * @return void
//     */
//    public function testToGetElementListOfCountries(): void
//    {
//        $this->assertIsObject(self::$countryList->{0});
//        $this->assertInstanceOf(Country::class, self::$countryList->{0});
//    }
//
//    /**
//     * @test
//     * @testdox Test the `->get()->toJson()` feature.
//     * @depends testToGetListOfCountries
//     * @return void
//     */
//    public function testGetToJsonFeature(): void
//    {
//        $json = self::$countryList->toJson();
//        $this->assertIsString($json);
//        $decodedJson = json_decode($json, true);
//        $this->assertNotNull($decodedJson, 'Not a valid JSON');
//        $this->assertIsArray($decodedJson, 'Not a valid JSON');
//
//        $json = self::$countryList->{0}->toJson();
//        $this->assertIsString($json);
//        $decodedJson = json_decode($json, true);
//        $this->assertNotNull($decodedJson, 'Not a valid JSON');
//        $this->assertIsArray($decodedJson, 'Not a valid JSON');
//    }
//
//
//    /**
//     * @test
//     * @testdox Test the `->get()->toArray()` feature.
//     * @depends testToGetListOfCountries
//     * @return void
//     */
//    public function testGetToArrayFeature(): void
//    {
//        $array = self::$countryList->toArray();
//        $this->assertIsArray($array, 'Not a valid Array');
//
//        $array = self::$countryList->{0}->toArray();
//        $this->assertIsArray($array, 'Not a valid Array');
//    }
//
//    /**
//     * @test
//     * @testdox Test the `->get()->toFlatten()` feature (default separator `.`).
//     * @depends testToGetListOfCountries
//     * @return void
//     */
//    public function testGetToFlattenFeature(): void
//    {
//        $flatten = self::$countryList->toFlatten();
//        $this->assertIsArray($flatten, 'Not a valid Array');
//        foreach (
//            [
//            mt_rand(0, (self::$countriesTotalCount - 1)),
//            mt_rand(0, (self::$countriesTotalCount - 1)),
//            mt_rand(0, (self::$countriesTotalCount - 1)),
//            mt_rand(0, (self::$countriesTotalCount - 1)),
//            mt_rand(0, (self::$countriesTotalCount - 1))
//            ] as $key
//        ) {
//            $this->assertEquals(self::$countryList->$key->alpha2, $flatten[$key . '.alpha2']);
//            $this->assertEquals(self::$countryList->$key->alpha3, $flatten[$key . '.alpha3']);
//            $this->assertEquals(self::$countryList->$key->unM49, $flatten[$key . '.unM49']);
//            $this->assertEquals(self::$countryList->$key->name, $flatten[$key . '.name']);
//        };
//    }
//
//    /**
//     * @test
//     * @testdox Test the `->get()->toFlatten('_')` feature, using custom separator.
//     * @depends testToGetListOfCountries
//     * @return void
//     */
//    public function testGetToFlattenFeatureCustomSeparator(): void
//    {
//        $flatten = self::$countryList->toFlatten('_');
//        $this->assertIsArray($flatten, 'Not a valid Array');
//        foreach (
//            [
//                     mt_rand(0, (self::$countriesTotalCount - 1)),
//                     mt_rand(0, (self::$countriesTotalCount - 1)),
//                     mt_rand(0, (self::$countriesTotalCount - 1)),
//                     mt_rand(0, (self::$countriesTotalCount - 1)),
//                     mt_rand(0, (self::$countriesTotalCount - 1))
//                 ] as $key
//        ) {
//            $this->assertEquals(self::$countryList->$key->alpha2, $flatten[$key . '_alpha2']);
//            $this->assertEquals(self::$countryList->$key->alpha3, $flatten[$key . '_alpha3']);
//            $this->assertEquals(self::$countryList->$key->unM49, $flatten[$key . '_unM49']);
//            $this->assertEquals(self::$countryList->$key->name, $flatten[$key . '_name']);
//        };
//    }
//
//    /**
//     * @test
//     * @testdox Test the `->first()` feature as instance of Country.
//     * @return void
//     * @throws QueryException
//     */
//    public function testFirstFeature(): void
//    {
//        /** @phpstan-ignore-next-line   The unique object type is needed for php 7.4 */
//        self::$country = self::$geoCodes->countries()->first();
//
//        $this->assertIsObject(self::$country);
//        $this->assertInstanceOf(Country::class, self::$country);
//    }
//
//    /**
//     * @test
//     * @testdox Test the `->first()` feature when result is empty as instance of Country.
//     * @return void
//     * @throws QueryException
//     */
//    public function testFirstFeatureOnEmpty(): void
//    {
//        $countries = self::$geoCodes->countries();
//        $countries->limit(0, 0);
//        $country = $countries->first();
//
//        $this->assertIsObject($country);
//        $this->assertInstanceOf(Country::class, $country);
//    }
//
//
//    /**
//     * @test
//     * @testdox Test the `->first()->toJson()` feature.
//     * @depends testFirstFeature
//     * @return void
//     */
//    public function testFirstToJsonFeature(): void
//    {
//        $json = self::$country->toJson();
//        $this->assertIsString($json);
//        $decodedJson = json_decode($json, true);
//        $this->assertNotNull($decodedJson, 'Not a valid JSON');
//        $this->assertIsArray($decodedJson, 'Not a valid JSON');
//    }
//
//
//    /**
//     * @test
//     * @testdox Test the `->first()->toArray()` feature.
//     * @depends testFirstFeature
//     * @return void
//     */
//    public function testFirstToArrayFeature(): void
//    {
//        $array = self::$country->toArray();
//        $this->assertIsArray($array, 'Not a valid Array');
//    }
//
//
//    /**
//     * @test
//     * @testdox Test the `->first()->toFlatten()` feature (default separator `.`).
//     * @depends testFirstFeature
//     * @return void
//     */
//    public function testFirstToFlattenFeature(): void
//    {
//        $flatten = self::$country->toFlatten();
//        $this->assertIsArray($flatten, 'Not a valid Array');
//        $regex = '/\./';
//        foreach ($flatten as $key => $val) {
//            if (preg_match('/^officialName/', $key)) {
//                $this->assertTrue(preg_match($regex, $key) === 1);
//            }
//            if (preg_match('/^currencies/', $key)) {
//                $this->assertTrue(preg_match($regex, $key) === 1);
//            }
//            if (preg_match('/^dialCodes/', $key)) {
//                $this->assertTrue(preg_match($regex, $key) === 1);
//            }
//        }
//    }
//
//    /**
//     * @test
//     * @testdox Test the `->first()->toFlatten('_')` feature, using custom separator.
//     * @depends testFirstFeature
//     * @return void
//     */
//    public function testFirstToFlattenFeatureCustomSeparator(): void
//    {
//        $flatten = self::$country->toFlatten('_');
//        $this->assertIsArray($flatten, 'Not a valid Array');
//        $regex = '/_/';
//        foreach ($flatten as $key => $val) {
//            if (preg_match('/^officialName/', $key)) {
//                $this->assertTrue(preg_match($regex, $key) === 1);
//            }
//            if (preg_match('/^currencies/', $key)) {
//                $this->assertTrue(preg_match($regex, $key) === 1);
//            }
//            if (preg_match('/^dialCodes/', $key)) {
//                $this->assertTrue(preg_match($regex, $key) === 1);
//            }
//        }
//    }
//
//    /**
//     * @test
//     * @testdox Test the `->count()` feature on the list of Countries.
//     * @return void
//     * @throws QueryException
//     */
//    public function testCountOfCountries(): void
//    {
//        $countries = self::$geoCodes->countries();
//        $count = $countries->count();
//        $this->assertEquals(
//            self::$countriesTotalCount,
//            $count,
//            "The TOTAL number of the countries doesn't match with " . self::$countriesTotalCount
//        );
//
//        foreach ([(self::$countriesTotalCount - 52), 27, 5, 32, 0] as $numberOfItems) {
//            $countries->limit(52, $numberOfItems);
//            $count = $countries->count();
//            $this->assertEquals(
//                $numberOfItems,
//                $count,
//                "The number of the countries doesn't match with " . $numberOfItems
//            );
//        }
//    }
//
//
//    /**
//     * @test
//     * @testdox Test the `->limit()` feature.
//     * @return void
//     * @throws QueryException
//     */
//    public function testLimit(): void
//    {
//        $countries = self::$geoCodes->countries();
//
//        // Invalid - `from` less than 0
//        $this->expectException(QueryException::class);
//        $countries->limit(-5, 20);
//
//        // Invalid - `numberOfItems` less than 0
//        $this->expectException(QueryException::class);
//        $countries->limit(20, -5);
//
//        // Valid input
//        $countries->limit(243, 2);
//        $this->assertEquals(2, $countries->count());
//        $get = $countries->get();
//        $this->assertEquals(self::$expectedLimitTest[0], $get->{0}->alpha2);
//        $this->assertEquals(self::$expectedLimitTest[1], $get->{1}->alpha2);
//    }
//
//    /**
//     * @test
//     * @testdox Test the `->orderBy()` feature.
//     * @return void
//     * @throws QueryException
//     */
//    public function testOrderBy(): void
//    {
//        // Test multiple calls.
//        $countries = self::$geoCodes->useLanguage('en')->countries();
//        $countries->orderBy('alpha2');
//        $countries->orderBy('alpha2', 'desc');
//        $country = $countries->first();
//        $this->assertEquals(self::$expectedOrderByTest['alpha2']['DESC'], $country->alpha2);
//    }
//    /**
//     * @dataProvider dataProviderIndexes
//     * @testdox ==>  using $index as property
//     * @throws QueryException
//     */
//    public function testOrderByWithDataProvider(string $index): void
//    {
//        $countries = self::$geoCodes->useLanguage('en')->countries();
//        $asc = $countries->orderBy($index)->first();
//        $this->assertEquals(self::$expectedOrderByTest[$index]['ASC'], $asc->{$index});
//        $desc = $countries->orderBy($index, 'desc')->first();
//        $this->assertEquals(self::$expectedOrderByTest[$index]['DESC'], $desc->{$index});
//    }
//    /**
//     * @test
//     * @testdox  ==>  using an invalid properties
//     * @return void
//     * @throws QueryException
//     */
//    public function testOrderByWithException(): void
//    {
//        $countries = self::$geoCodes->countries();
//
//        // Invalid - `property` less than 0
//        $this->expectException(QueryException::class);
//        $countries->orderBy('notIndexable');
//
//        // Invalid - `orderType` less than 0
//        $this->expectException(QueryException::class);
//        $countries->orderBy('alpha2', 'invalid');
//    }
////    /**
////     * @return array<array<int, int|string>>
////     */
////    public function dataProviderIndexes(): array
////    {
////        return array_map(
////            function ($index) {
////                return [$index];
////            },
////            (array) self::$constants['indexes']
////        );
////    }
///** ---------------------------------- */
//    /**
//     * @test
//     * @testdox Test the indexes
//     * @return void
//     */
//    public function testIndexes(): void
//    {
//        $indexes = self::$geoCodes->countries()->getIndexes();
//        $this->assertIsArray($indexes);
//        $indexes = array_keys($indexes);
//        $this->assertEquals($indexes, self::$constants['indexes']);
//    }
//
//    /**
//     * @dataProvider dataProviderIndexes
//     * @testdox ==>  using $index as index
//     * @throws QueryException
//     */
//    public function testIndexesWithDataProvider(string $index): void
//    {
//        foreach (
//            self::$geoCodes->countries()->withIndex($index)->limit(0, 1)->get()->toArray() as $key => $country
//        ) {
//            $this->assertEquals($key, $country[$index]);
//        }
//    }
//    /**
//     * @return array<array<int, int|string>>
//     */
//    public function dataProviderIndexes(): array
//    {
//        return array_map(
//            function ($index) {
//                return [$index];
//            },
//            (array) self::$constants['indexes']
//        );
//    }
//    /**
//     * @test
//     * @testdox ==>  using an invalid index
//     * @return void
//     */
//    public function testIndexFeatureWithException(): void
//    {
//        $this->expectException(QueryException::class);
//        self::$geoCodes->countries()->withIndex('invalidField');
//    }
//
//
//    /**
//     * @test
//     * @testdox Tests on the selectable fields.
//     * @return void
//     */
//    public function testSelectableFields(): void
//    {
//        $selectFields = self::$geoCodes->countries()->selectableFields();
//        $this->assertIsArray($selectFields);
//        $countries = self::$geoCodes->countries()->get();
//        foreach ($countries->collect() as $country) {
//            foreach ($selectFields as $key => $description) {
//                $prop = $key;
//                $object = $country;
//                if (preg_match('/\./', $prop)) {
//                    list($prop0, $prop) = explode('.', $prop);
//                    $object = $country->{$prop0};
//                }
//
//                // check the existence of the field
//                $this->assertTrue(
//                    property_exists($object, $prop),
//                    'Key `' . $key . '` not present in the country object'
//                );
//
//                // check the type of the key
//                preg_match('/\[(.*?)\]/', $description, $matches);
//                $type = $matches[1];
//                if (strpos($type, '?') === 0) {
//                    $type = substr($type, 1);
//                    $assert = gettype($object->{$prop}) == $type || gettype($object->{$prop}) == 'NULL';
//                } else {
//                    $assert = gettype($object->{$prop}) == $type;
//                }
//                $this->assertTrue(
//                    $assert,
//                    'Key `' . $key . '` for the country `' . $country->name . '`does not match with the declared type'
//                );
//            }
//        }
//    }
//
//    /**
//     * @param array<string> $selectFields
//     * @throws QueryException
//     */
//    private function checkThePropertyAfterSelect(array $selectFields): void
//    {
//        $country = self::$geoCodes->countries()->first();
//        foreach ($selectFields as $key) {
//            $prop = $key;
//            $object = $country;
//            if (preg_match('/\./', $prop)) {
//                list($prop0, $prop) = explode('.', $prop);
//                $object = $country->{$prop0};
//            }
//            // check the existence of the field
//            $this->assertTrue(
//                property_exists($object, $prop),
//                'Key `' . $key . '` not present in the country object'
//            );
//        }
//    }
//    /**
//     * @test
//     * @testdox ==>  all the selectable properties with a single ->select() call
//     * @return void
//     * @throws QueryException
//     */
//    public function testAllPropertiesWithSingleSelectCall(): void
//    {
//        $countries = self::$geoCodes->countries();
//        $selectFields = array_keys($countries->selectableFields());
//        $countries->select(...$selectFields);
//        $this->checkThePropertyAfterSelect($selectFields);
//    }
//    /**
//     * @test
//     * @testdox ==>  all the selectable properties with multiple ->select() calls
//     * @return void
//     * @throws QueryException
//     */
//    public function testMultipleSelectCalls(): void
//    {
//        $countries = self::$geoCodes->countries();
//        $selectFields = array_keys($countries->selectableFields());
//        foreach ($selectFields as $key) {
//            $countries->select($key);
//            $countries->select($key); // test also the redundancy
//        }
//        $this->checkThePropertyAfterSelect($selectFields);
//    }
//    /**
//     * @test
//     * @testdox ==>  the sigle property in a single ->select() call
//     * @return void
//     */
//    public function testSingleSelect(): void
//    {
//        $this->assertTrue(true);
//    }
//    /**
//     * @dataProvider dataProviderSelect
//     * @testdox ====>  using ->select('$select')
//     * @throws QueryException
//     */
//    public function testSelectWithDataProvider(string $select): void
//    {
//        $countries = self::$geoCodes->countries();
//        $countries->select($select);
//        $country = $countries->first();
//        if (preg_match('/\./', $select)) {
//            list($prop0, $prop) = explode('.', $select);
//            $country = $country->{$prop0};
//        }
//        $count = count(get_object_vars($country));
//        $this->assertEquals(1, $count);
//    }
//    /**
//     * @return array<array<int, int|string>>
//     */
//    public function dataProviderSelect(): array
//    {
//        return array_map(
//            function ($select) {
//                return [$select];
//            },
//            (array) self::$constants['selectables']
//        );
//    }
//    /**
//     * @test
//     * @testdox ====>  using an invalid property
//     * @return void
//     */
//    public function testSelectFeatureWithException(): void
//    {
//        $this->expectException(QueryException::class);
//        self::$geoCodes->countries()->select('invalidField');
//    }
//
//    /** ELIBE */
//    /**
//     * [TODO] ALTRI DATABASE
//     * [TODO] check the collection on the sub objects
//     * [TODO] After completion  ->useLanguage() feature
//     */
//
//
//
//    /**
//     * @test
//     * @testdox Countries: ELIBE.
//     * @return void
//     */
//    public function stica(): void
//    {
//
////        $countries = self::$geoCodes->useLanguage('it')->countries()->selectableFields();
////
////        $countries2 = self::$geoCodes->useLanguage('it')->countries()->getIndexes();
////
////        $countries3 = self::$geoCodes->useLanguage('it')->countries()->limit(0, 1)->get();
////
////        $countries4 = self::$geoCodes->countries()->withIndex('alpha2')->limit(0, 1)->get();
//
////        $elenaMyfile = fopen("/Users/aliberati/ALIBE/test.log", "a") or die("Unable to open file!");
////        fwrite($elenaMyfile, print_r($countries4, true) . "\n");
////        fclose($elenaMyfile);
//
////        $countries4 = $countries3->toJson();
////        $countries4 = $countries3->toArray();
//
//        self::$geoCodes->useLanguage('en');
//
//        $countries = self::$geoCodes->countries()->count();
////        $countries->fetch('it');
//        $elenaMyfile = fopen("/Users/aliberati/ALIBE/test.log", "a") or die("Unable to open file!");
//        fwrite($elenaMyfile, print_r($countries, true) . "\n");
//        fclose($elenaMyfile);
//
//        $this->assertTrue(true);
//    }
}
