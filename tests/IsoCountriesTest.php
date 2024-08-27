<?php

namespace Alibe\GeoCodes\Tests;

use Alibe\GeoCodes\Lib\DataObj\Countries;
use Alibe\GeoCodes\Lib\DataObj\Elements\Country;
use Alibe\GeoCodes\Lib\Exceptions\QueryException;
use PHPUnit\Framework\TestCase;
use Alibe\GeoCodes\GeoCodes;
use Symfony\Component\Yaml\Yaml;

/**
 * @testdox Countries
 */
final class IsoCountriesTest extends TestCase
{
    /**
     * @var int
     */
    private static int $countriesTotalCount = 250;

    /**
     * @var array<int|array<string>> $constants
     */
    private static array $constants = [
        'indexes' => [
            'alpha2',
            'alpha3',
            'unM49',
            'name',
            'fullName'
        ],
        'selectables' => [
            'alpha2',
            'alpha3',
            'unM49',
            'name',
            'fullName',
            'officialName',
            'dependency',
            'mottos',
            'currencies',
            'currencies.legalTenders',
            'currencies.widelyAccepted',
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
        'XK'
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
        'fullName' => [
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
        $expectedData = self::$countryList->toArray();
        $this->assertEquals($expectedData, $decodedJson, 'Converted JSON does not match expected data');

        $json = self::$countryList->{0}->toJson();
        $this->assertIsString($json);
        $decodedJson = json_decode($json, true);
        $this->assertNotNull($decodedJson, 'Not a valid JSON');
        $this->assertIsArray($decodedJson, 'Not a valid JSON');
        $expectedData = reset($expectedData);
        $this->assertEquals($expectedData, $decodedJson, 'Converted JSON does not match expected data');
    }

    /**
     * @test
     * @testdox Test the `->get()->toYaml()` feature.
     * @depends testToGetListOfCountries
     * @return void
     */
    public function testGetToYamlFeature(): void
    {
        $yaml = self::$countryList->toYaml();
        $this->assertIsString($yaml);
        $decodedYaml = Yaml::parse($yaml);
        $this->assertNotNull($decodedYaml, 'Not a valid YAML');
        $this->assertIsArray($decodedYaml, 'Not a valid YAML');
        $expectedData = self::$countryList->toArray();
        $this->assertEquals($expectedData, $decodedYaml, 'Converted YAML does not match expected data');

        $yaml = self::$countryList->{0}->toJson();
        $this->assertIsString($yaml);
        $decodedYaml = Yaml::parse($yaml);
        $this->assertNotNull($decodedYaml, 'Not a valid YAML');
        $this->assertIsArray($decodedYaml, 'Not a valid YAML');
        $expectedData = reset($expectedData);
        $this->assertEquals($expectedData, $decodedYaml, 'Converted YAML does not match expected data');
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
        $countries->offset(0)->limit(0);
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
            $countries->offset(52)->limit($numberOfItems);
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
     * @testdox Test the interval `->offset()->limit()` or the aliases `->skip()->take()` features.
     * @return void
     * @throws QueryException
     */
    public function testLimit(): void
    {
        $countries = self::$geoCodes->countries();

        // Invalid - `offset` less than 0
        try {
            $countries->offset(-5)->limit(20);
            $this->fail('An invalid limit from has been accepted');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11004, $e->getCode());
        }

        // Invalid - `limit` less than 0
        try {
            $countries->offset(20)->limit(-5);
            $this->fail('An invalid limit numberOfItems has been accepted');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11003, $e->getCode());
        }


        // Valid input
        $countries->offset(243)->limit(2);
        $this->assertEquals(2, $countries->count());
        $get = $countries->get();
        $this->assertEquals(self::$expectedLimitTest[0], $get->{0}->alpha2);
        $this->assertEquals(self::$expectedLimitTest[1], $get->{1}->alpha2);

        // Alias input
        $countries->skip(243)->take(2);
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
     */
    public function testOrderByWithException(): void
    {
        $countries = self::$geoCodes->countries();

        // Invalid - `property` not indexable
        try {
            $countries->orderBy('notIndexable');
            $this->fail('An invalid orderBy property has been accepted');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11005, $e->getCode());
            $this->assertEquals(1, preg_match('/"notIndexable"/', $e->getMessage()));
        }

        // Invalid - `orderType` invalid
        try {
            $countries->orderBy('alpha2', 'invalid');
            $this->fail('An invalid orderBy type has been accepted');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11006, $e->getCode());
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
            self::$geoCodes->countries()->withIndex($index)->offset(0)->limit(1)->get()->toArray() as $key => $country
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
        try {
            self::$geoCodes->countries()->withIndex('invalidField');
            $this->fail('The index is considered valid');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11001, $e->getCode());
            $this->assertEquals(1, preg_match('/"invalidField"/', $e->getMessage()));
        }
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
        try {
            self::$geoCodes->countries()->select('invalidField');
            $this->fail('The select is considered valid');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11002, $e->getCode());
            $this->assertEquals(1, preg_match('/"invalidField"/', $e->getMessage()));
        }
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
        try {
            $countries->fetch('it', 4, [['fr'], ['de']]);
            $this->fail('The fetch is considered valid');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11007, $e->getCode());
        }
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
            'LU' => [ 'alpha2' => 'LU' ], 'NL' => [ 'alpha2' => 'NL' ], 'GR' => [ 'alpha2' => 'GR' ]
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
        try {
            $countries->intersect();
            $this->fail('The intersect has been allowed');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11008, $e->getCode());
        }
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
            'CH' => [ 'alpha2' => 'CH' ], 'CY' => [ 'alpha2' => 'CY' ], 'BY' => [ 'alpha2' => 'BY' ],
            'XK' => [ 'alpha2' => 'XK' ]
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
        try {
            $countries->complement();
            $this->fail('The symmetric complement has been allowed');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11009, $e->getCode());
        }
    }

    /**
     * @test
     * @testdox Test the conditions ->where() and ->orWhere()
     * @return void
     */
    public function testConditions(): void
    {
        $this->assertTrue(true);
    }
    /**
     * @test
     * @testdox ==> invalid conditions
     * @return void
     */
    public function testInvalidConditions(): void
    {
        $this->assertTrue(true);
    }

    /**
     * @dataProvider dataProviderInvalidConditions
     * @testdox ====>  is invalid using ->where($txt) or ->orWhere($txt)
     *
     * @param string $txt
     * @param array<array<int, int|string>> $args
     * @param int $errorCode
     * @param array<string> $matches
     */
    public function testConditionsWithDataProviderInvalid(
        string $txt,
        array $args,
        int $errorCode,
        array $matches = []
    ): void {
        $countries = self::$geoCodes->countries();
        try {
            $countries->where(...$args);
            $this->fail('The condition is considered valid');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals($errorCode, $e->getCode());
            if (!empty($matches)) {
                foreach ($matches as $match) {
                    $this->assertEquals(1, preg_match('/"' . $match . '"/i', $e->getMessage()));
                }
            }
        }
        try {
            $countries->orWhere(...$args);
            $this->fail('The condition is considered valid');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals($errorCode, $e->getCode());
            if (!empty($matches)) {
                foreach ($matches as $match) {
                    $this->assertEquals(1, preg_match('/"' . $match . '"/i', $e->getMessage()));
                }
            }
        }
    }
    /**
     * @return array<
     *     int,
     *     array<int,
     *      array<int, array<int, array<int, array<int, string>|string>|int|string>|bool|int|string>|int|string>>
     */
    public function dataProviderInvalidConditions(): array
    {
        return [
            [
                "'sss'",
                ['sss'],
                11011
            ],
            [
                "'sss', '5', 'aaa', 'aaa'",
                ['sss', '5', 'aaa', 'aaa'],
                11010
            ],
            [
                "['field', 'operator', 'term'], ['field', 'operator', 'term']",
                [['field', 'operator', 'term'], ['field', 'operator', 'term']],
                11010
            ],
            [
                "5, '5', 'aaa'",
                [5, '5', 'aaa'],
                11010
            ],
            [
                "['5'], '5', 'aaa'",
                [['5'], '5', 'aaa'],
                11010
            ],
            [
                "[5], ['5'], ['aaa']",
                [[5], ['5'], ['aaa']],
                11010
            ],
            [
                "['5'], ['5'], ['aaa']",
                [['5'], ['5'], ['aaa']],
                11010
            ],
            [
                "true, '5', 'aaa'",
                [true, '5', 'aaa'],
                11010
            ],
            [
                "['field', 'operator', 'term']",
                [['field', 'operator', 'term']],
                11012,
                ['operator']
            ],
            [
                "['field', '=', 'term'],['field', 'operator', 'term'],['field', 'operator', 'term']",
                [['field', '=', 'term'],['field', 'operator', 'term'],['field', 'operator', 'term']],
                11010
            ],
            [
                "[['field', '=', 'term'],['field', 'operator', 'term'],['field', 'operator', 'term']]",
                [[['field', '=', 'term'],['field', 'operator', 'term'],['field', 'operator', 'term']]],
                11015,
                ['field']
            ],
            [
                "[['field', '=', 'term'],['field', 'operator', 'term'],['field', 'operator', 'term']]",
                [[['field', '=', 'term'],['field', '=', 'term']], ['field', '=', 'term']],
                11010
            ],
            [
                "[[]]",
                [[[]]],
                11011
            ],
            [
                "[[['field'], 'operator', 'term']]",
                [[[['field'], 'operator', 'term']]],
                11011
            ],
            [
                "[['field', ['operator'], 'term']]",
                [[['field', ['operator'], 'term']]],
                11011
            ],
            [
                "'alPha2', 'IE'",
                ['alPha2', 'IE'],
                11015,
                ['alPha2']
            ],
            [
                "'alpha2.inexistent', 'IE'",
                ['alpha2.inexistent', 'IE'],
                11015,
                ['alpha2.inexistent']
            ],
            [
                "['alpha2.inexistent', 'IE']",
                [['alpha2.inexistent', 'IE']],
                11015,
                ['alpha2.inexistent']
            ],
            [
                "[['alpha2.inexistent', 'IE']]",
                [[['alpha2.inexistent', 'IE']]],
                11015,
                ['alpha2.inexistent']
            ],
            [
                "['alpha2', '=', ['IE', 'IT']]]",
                [['alpha2', '=', ['IE', 'IT']]],
                11014,
                ['=']
            ],
            [
                "[['alpha2', 'IN', 'IE']]",
                [[['alpha2', 'IN', 'IE']]],
                11013,
                ['IN']
            ]
        ];
    }

    /**
     * @test
     * @testdox ==> valid conditions results
     * @return void
     */
    public function testValidConditions(): void
    {
        $this->assertTrue(true);
    }

    /**
     * @dataProvider dataProviderValidConditions
     * @testdox ====>  has valid result using ->$method($txt)
     *
     * @param string $txt
     * @param array<array<int, int|string>> $args
     * @param string $method
     * @param array<string> $matches
     * @throws QueryException
     */
    public function testConditionsWithDataProviderValid(
        string $txt,
        array $args,
        string $method,
        array $matches = []
    ): void {
        $countries = self::$geoCodes->countries();
        $countries->$method(...$args);
        $result = $countries->withIndex('alpha2')->select('alpha2')->get()->toArray();
        $this->assertEquals($matches, $result);
    }
    /**
     * @return array<int, array<int, array<int|string, array<int|string, array<int, string>|string>|string>|string>>
     */
    public function dataProviderValidConditions(): array
    {
        return [
            [
                "'alpha2', 'IE'",
                ['alpha2', 'IE'],
                'where',
                ['IE' => [ 'alpha2' => 'IE' ]]
            ],
            [
                "'alpha2', '=', 'IE'",
                ['alpha2', '=', 'IE'],
                'where',
                ['IE' => [ 'alpha2' => 'IE' ]]
            ],
            [
                "['alpha2', '=', 'IE']",
                [['alpha2', '=', 'IE']],
                'where',
                ['IE' => [ 'alpha2' => 'IE' ]]
            ],
            [
                "[['alpha2', '=', 'IE']]",
                [[['alpha2', '=', 'IE']]],
                'where',
                ['IE' => [ 'alpha2' => 'IE' ]]
            ],
            [
                "[['alpha2', '=', 'IE'], ['alpha2', '=', 'IT']]",
                [[['alpha2', '=', 'IE'], ['alpha2', '=', 'IT']]],
                'where',
                []
            ],
            [
                "[['alpha2', '=', 'IE'], ['alpha3', '=', 'IRL'], ['unM49', '=', '372']]",
                [[['alpha2', '=', 'IE'], ['alpha3', '=', 'IRL'], ['unM49', '=', '372']]],
                'where',
                ['IE' => [ 'alpha2' => 'IE' ]]
            ],
            [
                "'currencies', 'idr'",
                ['currencies', 'idr'],
                'where',
                ['ID' => [ 'alpha2' => 'ID' ]]
            ],
            [
                "'dialCodes.main', '+62'",
                ['dialCodes.main', '+62'],
                'where',
                ['ID' => [ 'alpha2' => 'ID' ]]
            ],
            [
                "'dialCodes', '+62'",
                ['dialCodes', '+62'],
                'where',
                ['ID' => [ 'alpha2' => 'ID' ]]
            ],
            [
                "'locales', 'jv-id'",
                ['locales', 'jv-id'],
                'where',
                ['ID' => [ 'alpha2' => 'ID' ]]
            ],
            [
                "'locales', 'like', 'jv-id'",
                ['locales', 'like', 'jv-id'],
                'where',
                ['ID' => [ 'alpha2' => 'ID' ]]
            ],
            [
                "'name', 'like', '%Ireland%'",
                ['name', 'like', '%Ireland%'],
                'where',
                ['IE' => [ 'alpha2' => 'IE' ]]
            ],
            [
                "'officialName', 'like', 'Poblacht%'",
                ['officialName', 'like', 'Poblacht%'],
                'where',
                ['IE' => [ 'alpha2' => 'IE' ]]
            ],
            [
                "'officialName', 'like', '%Poblacht%'",
                ['officialName', 'like', '%Poblacht%'],
                'where',
                ['IE' => [ 'alpha2' => 'IE' ]]
            ],
            [
                "'officialName', 'like', '%hÉireann'",
                ['officialName', 'like', '%hÉireann'],
                'where',
                ['IE' => [ 'alpha2' => 'IE' ]]
            ],
            [
                "'officialName', 'like', '%hÉirean%'",
                ['officialName', 'like', '%hÉirean%'],
                'where',
                ['IE' => [ 'alpha2' => 'IE' ]]
            ],
            [
                "'officialName', 'like', '%hÉireann%'",
                ['officialName', 'like', '%hÉireann%'],
                'where',
                ['IE' => [ 'alpha2' => 'IE' ]]
            ],
            [
                "'officialName', 'like', '%ינת%'",
                ['officialName', 'like', '%ינת%'],
                'where',
                ['IL' => [ 'alpha2' => 'IL' ]]
            ],
            [
                "'officialName', 'like', '%णराज्%'",
                ['officialName', 'like', '%णराज्%'],
                'where',
                ['IN' => [ 'alpha2' => 'IN' ]]
            ],
            [
                "'officialName', 'like', '%日本%'",
                ['officialName', 'like', '%日本%'],
                'where',
                ['JP' => [ 'alpha2' => 'JP' ]]
            ],
            [
                "'officialName', 'like', '%人民共和%'",
                ['officialName', 'like', '%人民共和%'],
                'where',
                ['CN' => [ 'alpha2' => 'CN' ], 'HK' => [ 'alpha2' => 'HK' ], 'MO' => [ 'alpha2' => 'MO' ]]
            ],
            [
                "[['officialName', 'like', '%人民共和%'], ['officialName', 'not like', '%港特別行政%']]",
                [[['officialName', 'like', '%人民共和%'], ['officialName', 'not like', '%港特別行政%']]],
                'where',
                ['CN' => [ 'alpha2' => 'CN' ], 'MO' => [ 'alpha2' => 'MO' ]]
            ],
            [
                "'officialName', 'like', '%민주주의인민%'",
                ['officialName', 'like', '%민주주의인민%'],
                'where',
                ['KP' => [ 'alpha2' => 'KP' ]]
            ],
            [
                "'officialName', 'like', '%عربية الس%'",
                ['officialName', 'like', '%عربية الس%'],
                'where',
                ['SA' => [ 'alpha2' => 'SA' ]]
            ],
            [
                "'dialCodes', '<=', '+1'",
                ['dialCodes', '<=', '+1'],
                'where',
                [
                    'CA' => [ 'alpha2' => 'CA' ], 'DO' => [ 'alpha2' => 'DO' ], 'UM' => [ 'alpha2' => 'UM' ],
                    'US' => [ 'alpha2' => 'US' ]
                ]
            ],
            [
                "[['dialCodes', '>=', '+1'], ['dialCodes', '<', '+12']]",
                [[['dialCodes', '>=', '+1'], ['dialCodes', '<', '+12']]],
                 'where',
                [
                    'CA' => [ 'alpha2' => 'CA' ], 'DO' => [ 'alpha2' => 'DO' ], 'UM' => [ 'alpha2' => 'UM' ],
                    'US' => [ 'alpha2' => 'US' ]
                ]
            ],
            [
                "'dialCodes', '<', '+12'",
                ['dialCodes', '<', '+12'],
                'where',
                [
                    'CA' => [ 'alpha2' => 'CA' ], 'DO' => [ 'alpha2' => 'DO' ], 'UM' => [ 'alpha2' => 'UM' ],
                    'US' => [ 'alpha2' => 'US' ]
                ]
            ],
            [
                "[['dialCodes', '>', '+1'], ['dialCodes', '<', '+13']]",
                [[['dialCodes', '>', '+1'], ['dialCodes', '<', '+13']]],
                'where',
                [
                    'AG' => [ 'alpha2' => 'AG' ], 'AI' => [ 'alpha2' => 'AI' ], 'BB' => [ 'alpha2' => 'BB' ],
                    'BS' => [ 'alpha2' => 'BS' ], 'VG' => [ 'alpha2' => 'VG' ]
                ]
            ],
            [
                "[['dependency', 'is null'], ['alpha2', 'ie']]",
                [[['dependency', 'is null'], ['alpha2', 'ie']]],
                'where',
                ['IE' => [ 'alpha2' => 'IE' ]]
            ],
            [
                "[['dependency', 'is NOT null'], ['alpha2', 'vg']]",
                [[['dependency', 'is NOT null'], ['alpha2', 'vg']]],
                'where',
                ['VG' => [ 'alpha2' => 'VG' ]]
            ],
            [
                "'ccTld', 'like', '%it%'",
                ['ccTld', 'like', '%it%'],
                'where',
                ['IT' => [ 'alpha2' => 'IT' ]]
            ],
            [
                "'otherAppsIds', 'like', '%3175395%'",
                ['otherAppsIds', 'like', '%3175395%'],
                'where',
                ['IT' => [ 'alpha2' => 'IT' ]]
            ],
        ];
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
     * @throws QueryException
     */
    public function testStica(): void
    {
//        $countries = self::$geoCodes->countries()->withIndex();
        $countries = self::$geoCodes->countries();
        $xml = $countries->select('alpha2', 'alpha3', 'currencies.legalTenders')->withIndex('fullName')
            ->skip(500)->take(1)->first()->toXmlAndValidate();
//        $elenaMyfile = fopen("/Users/aliberati/ALIBE/test.log", "a") or die("Unable to open file!");
//        fwrite($elenaMyfile, print_r(
//            $xml,
//            true
//        ) . "\n");
//        fclose($elenaMyfile);

        print_r($xml);

//        $countries->where([['officialName', 'like', '%人民共和%'], ['officialName', 'not like', '%港特別行政%']]);
//        $countries->orWhere('alpha2', 'IN', ['IT']);
//        $countries->fetch('IT')->where([['alpha2', 'IN', ['IE']]]);
//        $countries->get();

//        $elenaMyfile = fopen("/Users/aliberati/ALIBE/test.log", "a") or die("Unable to open file!");
//        fwrite($elenaMyfile, print_r(
//           $countries->toJson(),
//            true
//        ) . "\n");
//        fclose($elenaMyfile);


//        $countries->where([
//            ['alpha2', '=', 'term'],
//            [['field'], '=', 'term']
//        ]);


        $this->assertTrue(true);
    }
}
