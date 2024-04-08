<?php

namespace Alibe\GeoCodes\Tests;

use Alibe\GeoCodes\Lib\DataObj\Countries;
use Alibe\GeoCodes\Lib\DataObj\Elements\Country;
use Alibe\GeoCodes\Lib\Exceptions\QueryException;
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
        ],
        'selectables' => [
            'alpha2',
            'alpha3',
            'unM49',
            'name',
            'completeName',
            'officialName',
            'dependency',
            'mottos',
            'currencies',
            'dialCodes',
            'dialCodes.main',
            'dialCodes.exceptions',
            'timeZones',
            'locales',
            'demonyms'
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
        'alpha2' => [
            'ASC' => 'AD',
            'DESC' => 'ZW',
        ],
        'alpha3' => [
            'ASC' => 'ABW',
            'DESC' => 'ZWE',
        ],
        'unM49' => [
            'ASC' => '004',
            'DESC' => '894',
        ],
        'name' => [
            'ASC' => 'Afghanistan',
            'DESC' => 'Zimbabwe',
        ],
        'completeName' => [
            'ASC' => 'American Samoa',
            'DESC' => 'Vatican City State',
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
        /** @phpstan-ignore-next-line   The unique object type is needed for php 7.4 */
        self::$countryList = self::$geoCodes->countries()->get();
        $this->assertIsObject(self::$countryList);
        $this->assertInstanceOf(Countries::class, self::$countryList);
    }


    /**
     * @test
     * @testdox Test the elements of the list of countries are an instance of Country.
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
     * @throws QueryException
     */
    public function testFirstFeature(): void
    {
        /** @phpstan-ignore-next-line   The unique object type is needed for php 7.4 */
        self::$country = self::$geoCodes->countries()->first();

        $this->assertIsObject(self::$country);
        $this->assertInstanceOf(Country::class, self::$country);
    }

    /**
     * @test
     * @testdox Test the `->first()` feature when result is empty as instance of Country.
     * @return void
     * @throws QueryException
     */
    public function testFirstFeatureOnEmpty(): void
    {
        $countries = self::$geoCodes->countries();
        $countries->limit(0, 0);
        $country = $countries->first();

        $this->assertIsObject($country);
        $this->assertInstanceOf(Country::class, $country);
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
     * @testdox Test the `->first()->toFlatten()` feature (default separator `.`).
     * @depends testFirstFeature
     * @return void
     */
    public function testFirstToFlattenFeature(): void
    {
        $flatten = self::$country->toFlatten();
        $this->assertIsArray($flatten, 'Not a valid Array');
        $regex = '/\./';
        foreach ($flatten as $key => $val) {
            if (preg_match('/^officialName/', $key)) {
                $this->assertTrue(preg_match($regex, $key) === 1);
            }
            if (preg_match('/^currencies/', $key)) {
                $this->assertTrue(preg_match($regex, $key) === 1);
            }
            if (preg_match('/^dialCodes/', $key)) {
                $this->assertTrue(preg_match($regex, $key) === 1);
            }
        }
    }

    /**
     * @test
     * @testdox Test the `->first()->toFlatten('_')` feature, using custom separator.
     * @depends testFirstFeature
     * @return void
     */
    public function testFirstToFlattenFeatureCustomSeparator(): void
    {
        $flatten = self::$country->toFlatten('_');
        $this->assertIsArray($flatten, 'Not a valid Array');
        $regex = '/_/';
        foreach ($flatten as $key => $val) {
            if (preg_match('/^officialName/', $key)) {
                $this->assertTrue(preg_match($regex, $key) === 1);
            }
            if (preg_match('/^currencies/', $key)) {
                $this->assertTrue(preg_match($regex, $key) === 1);
            }
            if (preg_match('/^dialCodes/', $key)) {
                $this->assertTrue(preg_match($regex, $key) === 1);
            }
        }
    }

    /**
     * @test
     * @testdox Test the `->count()` feature on the list of Countries.
     * @return void
     * @throws QueryException
     */
    public function testCountOfCountries(): void
    {
        $countries = self::$geoCodes->countries();
        $count = $countries->count();
        $this->assertEquals(
            self::$countriesTotalCount,
            $count,
            "The TOTAL number of the countries doesn't match with " . self::$countriesTotalCount
        );

        foreach ([(self::$countriesTotalCount - 52), 27, 5, 32, 0] as $numberOfItems) {
            $countries->limit(52, $numberOfItems);
            $count = $countries->count();
            $this->assertEquals(
                $numberOfItems,
                $count,
                "The number of the countries doesn't match with " . $numberOfItems
            );
        }
    }


    /**
     * @test
     * @testdox Test the `->limit()` feature.
     * @return void
     * @throws QueryException
     */
    public function testLimit(): void
    {
        $countries = self::$geoCodes->countries();

        // Invalid - `from` less than 0
        try {
            $countries->limit(-5, 20);
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
        }

        // Invalid - `numberOfItems` less than 0
        try {
            $countries->limit(20, -5);
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
        }


        // Valid input
        $countries->limit(243, 2);
        $this->assertEquals(2, $countries->count());
        $get = $countries->get();
        $this->assertEquals(self::$expectedLimitTest[0], $get->{0}->alpha2);
        $this->assertEquals(self::$expectedLimitTest[1], $get->{1}->alpha2);
    }

    /**
     * @test
     * @testdox Test the `->orderBy()` feature.
     * @return void
     * @throws QueryException
     */
    public function testOrderBy(): void
    {
        // Test multiple calls.
        $countries = self::$geoCodes->useLanguage('en')->countries();
        $countries->orderBy('alpha2');
        $country = $countries->first();
        $this->assertEquals(self::$expectedOrderByTest['alpha2']['ASC'], $country->alpha2);
        $countries->orderBy('alpha2', 'desc');
        $country = $countries->first();
        $this->assertEquals(self::$expectedOrderByTest['alpha2']['DESC'], $country->alpha2);
    }
    /**
     * @dataProvider dataProviderIndexes
     * @testdox ==>  using $index as property
     * @throws QueryException
     */
    public function testOrderByWithDataProvider(string $index): void
    {
        $countries = self::$geoCodes->useLanguage('en')->countries();
        $asc = $countries->orderBy($index)->first();
        $this->assertEquals(self::$expectedOrderByTest[$index]['ASC'], $asc->{$index});
        $desc = $countries->orderBy($index, 'desc')->first();
        $this->assertEquals(self::$expectedOrderByTest[$index]['DESC'], $desc->{$index});
    }
    /**
     * @test
     * @testdox  ==>  using an invalid properties
     * @return void
     * @throws QueryException
     */
    public function testOrderByWithException(): void
    {
        $countries = self::$geoCodes->countries();

        // Invalid - `property` not indexable
        try {
            $countries->orderBy('notIndexable');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
        }

        // Invalid - `orderType` invalid
        try {
            $countries->orderBy('alpha2', 'invalid');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
        }
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
     * @throws QueryException
     */
    public function testIndexesWithDataProvider(string $index): void
    {
        foreach (
            self::$geoCodes->countries()->withIndex($index)->limit(0, 1)->get()->toArray() as $key => $country
        ) {
            $this->assertEquals($key, $country[$index]);
        }
    }
    /**
     * @return array<array<int, int|string>>
     */
    public function dataProviderIndexes(): array
    {
        return array_map(
            function ($index) {
                return [$index];
            },
            (array) self::$constants['indexes']
        );
    }
    /**
     * @test
     * @testdox ==>  using an invalid index
     * @return void
     */
    public function testIndexFeatureWithException(): void
    {
        $this->expectException(QueryException::class);
        self::$geoCodes->countries()->withIndex('invalidField');
    }


    /**
     * @test
     * @testdox Tests on the selectable fields.
     * @return void
     */
    public function testSelectableFields(): void
    {
        $selectFields = self::$geoCodes->countries()->selectableFields();
        $this->assertIsArray($selectFields);
        $countries = self::$geoCodes->countries()->get();
        foreach ($countries->collect() as $country) {
            foreach ($selectFields as $key => $description) {
                $prop = $key;
                $object = $country;
                if (preg_match('/\./', $prop)) {
                    list($prop0, $prop) = explode('.', $prop);
                    $object = $country->{$prop0};
                }

                // check the existence of the field
                $this->assertTrue(
                    property_exists($object, $prop),
                    'Key `' . $key . '` not present in the country object'
                );

                // check the type of the key
                preg_match('/\[(.*?)\]/', $description, $matches);
                $type = $matches[1];
                if (strpos($type, '?') === 0) {
                    $type = substr($type, 1);
                    $assert = gettype($object->{$prop}) == $type || gettype($object->{$prop}) == 'NULL';
                } else {
                    $assert = gettype($object->{$prop}) == $type;
                }
                $this->assertTrue(
                    $assert,
                    'Key type `' . $key . '` for the country `' . $country->name .
                        '`does not match with the declared type'
                );
            }
        }
    }

    /**
     * @param array<string> $selectFields
     * @throws QueryException
     */
    private function checkThePropertyAfterSelect(array $selectFields): void
    {
        $country = self::$geoCodes->countries()->first();
        foreach ($selectFields as $key) {
            $prop = $key;
            $object = $country;
            if (preg_match('/\./', $prop)) {
                list($prop0, $prop) = explode('.', $prop);
                $object = $country->{$prop0};
            }
            // check the existence of the field
            $this->assertTrue(
                property_exists($object, $prop),
                'Key `' . $key . '` not present in the country object'
            );
        }
    }
    /**
     * @test
     * @testdox ==>  all the selectable properties with a single ->select() call
     * @return void
     * @throws QueryException
     */
    public function testAllPropertiesWithSingleSelectCall(): void
    {
        $countries = self::$geoCodes->countries();
        $selectFields = array_keys($countries->selectableFields());
        $countries->select(...$selectFields);
        $this->checkThePropertyAfterSelect($selectFields);
    }
    /**
     * @test
     * @testdox ==>  all the selectable properties with multiple ->select() calls
     * @return void
     * @throws QueryException
     */
    public function testMultipleSelectCalls(): void
    {
        $countries = self::$geoCodes->countries();
        $selectFields = array_keys($countries->selectableFields());
        foreach ($selectFields as $key) {
            $countries->select($key);
            $countries->select($key); // test also the redundancy
        }
        $this->checkThePropertyAfterSelect($selectFields);
    }
    /**
     * @test
     * @testdox ==>  the sigle property in a single ->select() call
     * @return void
     */
    public function testSingleSelect(): void
    {
        $this->assertTrue(true);
    }
    /**
     * @dataProvider dataProviderSelect
     * @testdox ====>  using ->select('$select')
     * @throws QueryException
     */
    public function testSelectWithDataProvider(string $select): void
    {
        $countries = self::$geoCodes->countries();
        $countries->select($select);
        $country = $countries->first();
        if (preg_match('/\./', $select)) {
            list($prop0, $prop) = explode('.', $select);
            $country = $country->{$prop0};
        }
        $count = count(get_object_vars($country));
        $this->assertEquals(1, $count);
    }
    /**
     * @return array<array<int, int|string>>
     */
    public function dataProviderSelect(): array
    {
        return array_map(
            function ($select) {
                return [$select];
            },
            (array) self::$constants['selectables']
        );
    }
    /**
     * @test
     * @testdox ====>  using an invalid property
     * @return void
     */
    public function testSelectFeatureWithException(): void
    {
        $this->expectException(QueryException::class);
        self::$geoCodes->countries()->select('invalidField');
    }

    /**
     * @test
     * @testdox Tests on the fetching feature.
     * @return void
     */
    public function testFetchFeature(): void
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     * @testdox ==> using - as valid -  in input integer, string, array and geoSets group.
     * @return void
     * @throws QueryException
     */
    public function testFetchFeatureValidInput(): void
    {
        $cfr = [
            'IT' => [ 'alpha2' => 'IT' ], 'AF' => [ 'alpha2' => 'AF' ], 'FR' => [ 'alpha2' => 'FR' ],
            'DE' => [ 'alpha2' => 'DE' ], 'DZ' => [ 'alpha2' => 'DZ' ], 'EG' => [ 'alpha2' => 'EG' ],
            'LY' => [ 'alpha2' => 'LY' ], 'MA' => [ 'alpha2' => 'MA' ], 'SD' => [ 'alpha2' => 'SD' ],
            'TN' => [ 'alpha2' => 'TN' ], 'EH' => [ 'alpha2' => 'EH' ]
        ];
        $countries = self::$geoCodes->countries();
        $countries->fetch('it', 4, ['fr', 'de'], 'GEOG-AF-NO');
        $result = $countries->withIndex('alpha2')->select('alpha2')->get()->toArray();
        $this->assertEquals($cfr, $result);
    }

    /**
     * @test
     * @testdox ==> with Exception (using array or arrays in input).
     * @return void
     */
    public function testFetchFeatureWithException(): void
    {
        $countries = self::$geoCodes->countries();
        $this->expectException(QueryException::class);
        $countries->fetch('it', 4, [['fr'], ['de']]);
    }


    /**
     * @test
     * @testdox ==> with multiple calls ->fetch(...)->fetch(...)
     * @return void
     * @throws QueryException
     */
    public function testMultipleFetchFeature(): void
    {
        $cfr = [
            'IT' => [ 'alpha2' => 'IT' ], 'AF' => [ 'alpha2' => 'AF' ], 'FR' => [ 'alpha2' => 'FR' ],
            'DE' => [ 'alpha2' => 'DE' ], 'DZ' => [ 'alpha2' => 'DZ' ], 'EG' => [ 'alpha2' => 'EG' ],
            'LY' => [ 'alpha2' => 'LY' ], 'MA' => [ 'alpha2' => 'MA' ], 'SD' => [ 'alpha2' => 'SD' ],
            'TN' => [ 'alpha2' => 'TN' ], 'EH' => [ 'alpha2' => 'EH' ]
        ];
        $countries = self::$geoCodes->countries();
        $countries->fetch('it', 4, ['fr', 'de'])->fetch('GEOG-AF-NO');
        $result = $countries->withIndex('alpha2')->select('alpha2')->get()->toArray();
        $this->assertEquals($cfr, $result);
    }

    /**
     * @test
     * @testdox ==> with the ->fetchAll() or the ->fetch('*') or the ->fetch(..., '*') features
     * @return void
     * @throws QueryException
     */
    public function testFetchAllFeature(): void
    {
        $countries = self::$geoCodes->countries();
        $fetchAll = $countries->fetchAll()->get();
        $fetchStar = $countries->fetch('*')->get();
        $fetchWithStar = $countries->fetch('it', 4, ['fr', 'de'], 'GEOG-AF-NO', '*')->get();
        $this->assertEquals($fetchAll, self::$countryList);
        $this->assertEquals($fetchStar, self::$countryList);
        $this->assertEquals($fetchWithStar, self::$countryList);
    }

    /**
     * @test
     * @testdox Test operations on the fetched groups
     * @return void
     */
    public function testOperationsOnFetchedGroup(): void
    {
        $this->assertTrue(true);
    }


    /**
     * @test
     * @testdox ==> with the ->merge() command
     * @return void
     * @throws QueryException
     */
    public function testMerge(): void
    {
        $cfr = [
            'IT' => [ 'alpha2' => 'IT' ], 'AF' => [ 'alpha2' => 'AF' ], 'FR' => [ 'alpha2' => 'FR' ],
            'DE' => [ 'alpha2' => 'DE' ], 'DZ' => [ 'alpha2' => 'DZ' ], 'EG' => [ 'alpha2' => 'EG' ],
            'LY' => [ 'alpha2' => 'LY' ], 'MA' => [ 'alpha2' => 'MA' ], 'SD' => [ 'alpha2' => 'SD' ],
            'TN' => [ 'alpha2' => 'TN' ], 'EH' => [ 'alpha2' => 'EH' ]
        ];
        $countries = self::$geoCodes->countries();
        $countries->fetch('it', 4, ['fr', 'de'])->fetch('GEOG-AF-NO');
        $countries->merge();
        $result = $countries->withIndex('alpha2')->select('alpha2')->get()->toArray();
        $this->assertEquals($cfr, $result);
    }

    /**
     * @test
     * @testdox ====> after multiple operations
     * @return void
     * @throws QueryException
     */
    public function testMergeMultiples(): void
    {
        $cfr = [
            'IT' => [ 'alpha2' => 'IT' ], 'AF' => [ 'alpha2' => 'AF' ], 'FR' => [ 'alpha2' => 'FR' ],
            'DE' => [ 'alpha2' => 'DE' ], 'DZ' => [ 'alpha2' => 'DZ' ], 'EG' => [ 'alpha2' => 'EG' ],
            'LY' => [ 'alpha2' => 'LY' ], 'MA' => [ 'alpha2' => 'MA' ], 'SD' => [ 'alpha2' => 'SD' ],
            'TN' => [ 'alpha2' => 'TN' ], 'EH' => [ 'alpha2' => 'EH' ]
        ];
        $countries = self::$geoCodes->countries();
        $countries->fetch('it')->fetch('GEOG-AF-NO');
        $countries->merge();
        $countries->fetch(4)->fetch(['fr', 'de']);
        $countries->merge();
        $countries->merge();
        $result = $countries->withIndex('alpha2')->select('alpha2')->get()->toArray();
        $this->assertEquals($cfr, $result);
    }

    /**
     * @test
     * @testdox ==> with the ->intersect() command
     * @return void
     * @throws QueryException
     */
    public function testIntersect(): void
    {
        $cfr = [
            'BG' => [ 'alpha2' => 'BG' ], 'CZ' => [ 'alpha2' => 'CZ' ], 'HU' => [ 'alpha2' => 'HU' ],
            'PL' => [ 'alpha2' => 'PL' ], 'RO' => [ 'alpha2' => 'RO' ], 'SK' => [ 'alpha2' => 'SK' ],
            'DK' => [ 'alpha2' => 'DK' ], 'EE' => [ 'alpha2' => 'EE' ], 'FI' => [ 'alpha2' => 'FI' ],
            'IS' => [ 'alpha2' => 'IS' ], 'LV' => [ 'alpha2' => 'LV' ], 'LT' => [ 'alpha2' => 'LT' ],
            'NO' => [ 'alpha2' => 'NO' ], 'GB' => [ 'alpha2' => 'GB' ], 'AL' => [ 'alpha2' => 'AL' ],
            'HR' => [ 'alpha2' => 'HR' ], 'IT' => [ 'alpha2' => 'IT' ], 'MK' => [ 'alpha2' => 'MK' ],
            'PT' => [ 'alpha2' => 'PT' ], 'SI' => [ 'alpha2' => 'SI' ], 'ES' => [ 'alpha2' => 'ES' ],
            'BE' => [ 'alpha2' => 'BE' ], 'DE' => [ 'alpha2' => 'DE' ], 'FR' => [ 'alpha2' => 'FR' ],
            'LU' => [ 'alpha2' => 'LU' ], 'NL' => [ 'alpha2' => 'NL' ]
        ];
        /** European countries that are part of the NATO  */
        $countries = self::$geoCodes->countries();
        $countries->fetch(150)->fetch('ORGS-NATO');
        $countries->intersect();
        $result = $countries->withIndex('alpha2')->select('alpha2')->get()->toArray();
        $this->assertEquals($cfr, $result);
    }

    /**
     * @test
     * @testdox ====> after multiple operations
     * @return void
     * @throws QueryException
     */
    public function testIntersectMultiples(): void
    {
        $cfr = [
            'GB' => [ 'alpha2' => 'GB' ]
        ];
        /**
         * Intersect
         * - European countries that are part of the NATO and
         * - Countries that are part of International Criminal Court the International Criminal Police Organization
         */
        $countries = self::$geoCodes->countries();
        $countries->fetch(150)->fetch('ORGS-NATO');
        $countries->intersect();
        $countries->fetch('ORGS-CWNAT')->fetch('ORGS-CWRLM');
        $countries->intersect();
        $countries->intersect();
        $result = $countries->withIndex('alpha2')->select('alpha2')->get()->toArray();
        $this->assertEquals($cfr, $result);
    }

    /**
     * @test
     * @testdox ====> with thrown exception
     * @return void
     * @throws QueryException
     */
    public function testIntersectException(): void
    {
        $countries = self::$geoCodes->countries();
        $countries->fetch(150);
        $this->expectException(QueryException::class);
        $countries->intersect();
    }

    /**
     * @test
     * @testdox ==> with the ->complement() (simmetric complement) command
     * @return void
     * @throws QueryException
     */
    public function testComplement(): void
    {
        $cfr = [
            'BG' => [ 'alpha2' => 'BG' ], 'CZ' => [ 'alpha2' => 'CZ' ], 'DK' => [ 'alpha2' => 'DK' ],
            'HU' => [ 'alpha2' => 'HU' ], 'PL' => [ 'alpha2' => 'PL' ], 'RO' => [ 'alpha2' => 'RO' ],
            'SE' => [ 'alpha2' => 'SE' ]
        ];
        /** Countries of the European Union that are not part of the eurozone  */
        $countries = self::$geoCodes->countries();
        $countries->fetch('ORGS-EU')->fetch('ZONE-EZ');
        $countries->complement();
        $result = $countries->withIndex('alpha2')->select('alpha2')->get()->toArray();
        $this->assertEquals($cfr, $result);
    }

    /**
     * @test
     * @testdox ====> after multiple operations
     * @return void
     * @throws QueryException
     */
    public function testComplementMultiples(): void
    {
        $cfr = [
            'BG' => [ 'alpha2' => 'BG' ], 'CZ' => [ 'alpha2' => 'CZ' ], 'DK' => [ 'alpha2' => 'DK' ],
            'HU' => [ 'alpha2' => 'HU' ], 'PL' => [ 'alpha2' => 'PL' ], 'RO' => [ 'alpha2' => 'RO' ],
            'SE' => [ 'alpha2' => 'SE' ], 'MD' => [ 'alpha2' => 'MD' ], 'RU' => [ 'alpha2' => 'RU' ],
            'UA' => [ 'alpha2' => 'UA' ], 'AX' => [ 'alpha2' => 'AX' ], 'GG' => [ 'alpha2' => 'GG' ],
            'JE' => [ 'alpha2' => 'JE' ], 'FO' => [ 'alpha2' => 'FO' ], 'IS' => [ 'alpha2' => 'IS' ],
            'IM' => [ 'alpha2' => 'IM' ], 'NO' => [ 'alpha2' => 'NO' ], 'SJ' => [ 'alpha2' => 'SJ' ],
            'GB' => [ 'alpha2' => 'GB' ], 'AL' => [ 'alpha2' => 'AL' ], 'AD' => [ 'alpha2' => 'AD' ],
            'BA' => [ 'alpha2' => 'BA' ], 'GI' => [ 'alpha2' => 'GI' ], 'VA' => [ 'alpha2' => 'VA' ],
            'ME' => [ 'alpha2' => 'ME' ], 'MK' => [ 'alpha2' => 'MK' ], 'SM' => [ 'alpha2' => 'SM' ],
            'RS' => [ 'alpha2' => 'RS' ], 'LI' => [ 'alpha2' => 'LI' ], 'MC' => [ 'alpha2' => 'MC' ],
            'CH' => [ 'alpha2' => 'CH' ], 'CY' => [ 'alpha2' => 'CY' ], 'BY' => [ 'alpha2' => 'BY' ]
        ];
        /**
         * Make the simmetric complement between
         * - Countries of the European Union that are not part of the eurozone and
         * - European countries that are not part of the European Union
         */
        $countries = self::$geoCodes->countries();
        $countries->fetch('ORGS-EU')->fetch('ZONE-EZ');
        $countries->complement();
        $countries->fetch(150)->fetch('ORGS-EU');
        $countries->complement();
        $countries->complement();
        $result = $countries->withIndex('alpha2')->select('alpha2')->get()->toArray();
        $this->assertEquals($cfr, $result);
    }

    /**
     * @test
     * @testdox ====> with thrown exception
     * @return void
     * @throws QueryException
     */
    public function testComplementException(): void
    {
        $countries = self::$geoCodes->countries();
        $countries->fetch(150);
        $this->expectException(QueryException::class);
        $countries->complement();
    }

    /** ELIBE */
    /**
     * [TODO] check the collection on the sub objects
     * [TODO] After completion  ->useLanguage() feature
     */



    /**
     * @test
     * @testdox Countries: ELIBE.
     * @return void
     */
    public function testStica(): void
    {

//        $countries = self::$geoCodes->useLanguage('it')->countries()->selectableFields();
//
//        $countries2 = self::$geoCodes->useLanguage('it')->countries()->getIndexes();
//
//        $countries3 = self::$geoCodes->useLanguage('it')->countries()->limit(0, 1)->get();
//
//        $countries4 = self::$geoCodes->countries()->withIndex('alpha2')->limit(0, 1)->get();

//        $elenaMyfile = fopen("/Users/aliberati/ALIBE/test.log", "a") or die("Unable to open file!");
//        fwrite($elenaMyfile, print_r($countries4, true) . "\n");
//        fclose($elenaMyfile);

//        $countries4 = $countries3->toJson();
//        $countries4 = $countries3->toArray();

        self::$geoCodes->useLanguage('en');

//        $countries = self::$geoCodes->countries()->withIndex('alpha2')->get()->toArray();
//        $countries = self::$geoCodes->countries();
//        $countries->fetch('it', 4, ['fr', 'de']);
//        $elenaMyfile = fopen("/Users/aliberati/ALIBE/test.log", "a") or die("Unable to open file!");
//        fwrite($elenaMyfile, print_r($countries, true) . "\n");
//        fclose($elenaMyfile);

        $this->assertTrue(true);
    }
}
