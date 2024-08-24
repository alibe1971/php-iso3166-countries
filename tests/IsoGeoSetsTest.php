<?php

namespace Alibe\GeoCodes\Tests;

use Alibe\GeoCodes\Lib\DataObj\Elements\GeoSet;
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
    private static int $geoSetsTotalCount = 62;

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
        'GEOG-AS-SO',
        'GEOG-AS-WE'
    ];

    /**
     * @var array<string, array<string, string>> $expectedOrderByTest
     */
    private static array $expectedOrderByTest = [
        'internalCode' => [
            'ASC' => 'CONV-G20',
            'DESC' => 'ZONE-EZ',
        ],
        'name' => [
            'ASC' => 'Africa',
            'DESC' => 'World Trade Organization (WTO)',
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
     * @testdox Test `->get()` the list of geosets is object as instance of GeoSets.
     * @return void
     */
    public function testToGetListOfGeoSets(): void
    {
        /** @phpstan-ignore-next-line   The unique object type is needed for php 7.4 */
        self::$geoSetsList = self::$geoCodes->geoSets()->get();
        $this->assertIsObject(self::$geoSetsList);
        $this->assertInstanceOf(GeoSets::class, self::$geoSetsList);
    }

    /**
     * @test
     * @testdox Test the elements of the list of geosets are an instance of GeoSet.
     * @depends testToGetListOfGeoSets
     * @return void
     */
    public function testToGetElementListOfGeoSets(): void
    {
        $this->assertIsObject(self::$geoSetsList->{0});
        $this->assertInstanceOf(GeoSet::class, self::$geoSetsList->{0});
    }

    /**
     * @test
     * @testdox Test the `->get()->toJson()` feature.
     * @depends testToGetListOfGeoSets
     * @return void
     */
    public function testGetToJsonFeature(): void
    {
        $json = self::$geoSetsList->toJson();
        $this->assertIsString($json);
        $decodedJson = json_decode($json, true);
        $this->assertNotNull($decodedJson, 'Not a valid JSON');
        $this->assertIsArray($decodedJson, 'Not a valid JSON');

        $json = self::$geoSetsList->{0}->toJson();
        $this->assertIsString($json);
        $decodedJson = json_decode($json, true);
        $this->assertNotNull($decodedJson, 'Not a valid JSON');
        $this->assertIsArray($decodedJson, 'Not a valid JSON');
    }

    /**
     * @test
     * @testdox Test the `->get()->toArray()` feature.
     * @depends testToGetListOfGeoSets
     * @return void
     */
    public function testGetToArrayFeature(): void
    {
        $array = self::$geoSetsList->toArray();
        $this->assertIsArray($array, 'Not a valid Array');

        $array = self::$geoSetsList->{0}->toArray();
        $this->assertIsArray($array, 'Not a valid Array');
    }

    /**
     * @test
     * @testdox Test the `->get()->toFlatten()` feature (default separator `.`).
     * @depends testToGetListOfGeoSets
     * @return void
     */
    public function testGetToFlattenFeature(): void
    {
        $flatten = self::$geoSetsList->toFlatten();
        $this->assertIsArray($flatten, 'Not a valid Array');
        foreach (
            [
            mt_rand(0, (self::$geoSetsTotalCount - 1)),
            mt_rand(0, (self::$geoSetsTotalCount - 1)),
            mt_rand(0, (self::$geoSetsTotalCount - 1)),
            mt_rand(0, (self::$geoSetsTotalCount - 1)),
            mt_rand(0, (self::$geoSetsTotalCount - 1))
            ] as $key
        ) {
            $this->assertEquals(self::$geoSetsList->$key->internalCode, $flatten[$key . '.internalCode']);
            $this->assertEquals(self::$geoSetsList->$key->unM49, $flatten[$key . '.unM49']);
            $this->assertEquals(self::$geoSetsList->$key->name, $flatten[$key . '.name']);
        };
    }

    /**
     * @test
     * @testdox Test the `->get()->toFlatten('_')` feature, using custom separator.
     * @depends testToGetListOfGeoSets
     * @return void
     */
    public function testGetToFlattenFeatureCustomSeparator(): void
    {
        $flatten = self::$geoSetsList->toFlatten('_');
        $this->assertIsArray($flatten, 'Not a valid Array');
        foreach (
            [
                     mt_rand(0, (self::$geoSetsTotalCount - 1)),
                     mt_rand(0, (self::$geoSetsTotalCount - 1)),
                     mt_rand(0, (self::$geoSetsTotalCount - 1)),
                     mt_rand(0, (self::$geoSetsTotalCount - 1)),
                     mt_rand(0, (self::$geoSetsTotalCount - 1))
                 ] as $key
        ) {
            $this->assertEquals(self::$geoSetsList->$key->internalCode, $flatten[$key . '_internalCode']);
            $this->assertEquals(self::$geoSetsList->$key->unM49, $flatten[$key . '_unM49']);
            $this->assertEquals(self::$geoSetsList->$key->name, $flatten[$key . '_name']);
        };
    }

    /**
     * @test
     * @testdox Test the `->first()` feature as instance of GeoSet.
     * @return void
     * @throws QueryException
     */
    public function testFirstFeature(): void
    {
        /** @phpstan-ignore-next-line   The unique object type is needed for php 7.4 */
        self::$geoSet = self::$geoCodes->geoSets()->first();

        $this->assertIsObject(self::$geoSet);
        $this->assertInstanceOf(GeoSet::class, self::$geoSet);
    }

    /**
     * @test
     * @testdox Test the `->first()` feature when result is empty as instance of GeoSet.
     * @return void
     * @throws QueryException
     */
    public function testFirstFeatureOnEmpty(): void
    {
        $geoSets = self::$geoCodes->geoSets();
        $geoSets->offset(0)->limit(0);
        $geoSet = $geoSets->first();

        $this->assertIsObject($geoSet);
        $this->assertInstanceOf(GeoSet::class, $geoSet);
    }

    /**
     * @test
     * @testdox Test the `->first()->toJson()` feature.
     * @depends testFirstFeature
     * @return void
     */
    public function testFirstToJsonFeature(): void
    {
        $json = self::$geoSet->toJson();
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
        $array = self::$geoSet->toArray();
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
        $flatten = self::$geoSet->toFlatten();
        $this->assertIsArray($flatten, 'Not a valid Array');
        $regex = '/\./';
        foreach ($flatten as $key => $val) {
            if (preg_match('/^tags/', $key)) {
                $this->assertTrue(preg_match($regex, $key) === 1);
            }
            if (preg_match('/^countryCodes/', $key)) {
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
        $flatten = self::$geoSet->toFlatten('_');
        $this->assertIsArray($flatten, 'Not a valid Array');
        $regex = '/_/';
        foreach ($flatten as $key => $val) {
            if (preg_match('/^tags/', $key)) {
                $this->assertTrue(preg_match($regex, $key) === 1);
            }
            if (preg_match('/^countryCodes/', $key)) {
                $this->assertTrue(preg_match($regex, $key) === 1);
            }
        }
    }

    /**
     * @test
     * @testdox Test the `->count()` feature on the list of GeoSets.
     * @return void
     * @throws QueryException
     */
    public function testCountOfGeoSets(): void
    {
        $geoSets = self::$geoCodes->geoSets();
        $count = $geoSets->count();
        $this->assertEquals(
            self::$geoSetsTotalCount,
            $count,
            "The TOTAL number of the geosets doesn't match with " . self::$geoSetsTotalCount
        );

        foreach ([(self::$geoSetsTotalCount - 21), 27, 5, 32, 0] as $numberOfItems) {
            $geoSets->offset(21)->limit($numberOfItems);
            $count = $geoSets->count();
            $this->assertEquals(
                $numberOfItems,
                $count,
                "The number of the geosets doesn't match with " . $numberOfItems
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
        $geoSets = self::$geoCodes->geoSets();

        // Invalid - `offset` less than 0
        try {
            $geoSets->offset(-2)->limit(20);
            $this->fail('An invalid limit from has been accepted');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11004, $e->getCode());
        }

        // Invalid - `limit` less than 0
        try {
            $geoSets->offset(20)->limit(-5);
            $this->fail('An invalid limit numberOfItems has been accepted');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11003, $e->getCode());
        }

        // Valid input
        $geoSets->offset(22)->limit(2);
        $this->assertEquals(2, $geoSets->count());
        $get = $geoSets->get();
        $this->assertEquals(self::$expectedLimitTest[0], $get->{0}->internalCode);
        $this->assertEquals(self::$expectedLimitTest[1], $get->{1}->internalCode);

        // Alias input
        $geoSets->skip(22)->take(2);
        $this->assertEquals(2, $geoSets->count());
        $get = $geoSets->get();
        $this->assertEquals(self::$expectedLimitTest[0], $get->{0}->internalCode);
        $this->assertEquals(self::$expectedLimitTest[1], $get->{1}->internalCode);
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
        $geoSets = self::$geoCodes->useLanguage('en')->geoSets();
        $geoSets->orderBy('internalCode');
        $geoSet = $geoSets->first();
        $this->assertEquals(self::$expectedOrderByTest['internalCode']['ASC'], $geoSet->internalCode);
        $geoSets->orderBy('internalCode', 'desc');
        $geoSet = $geoSets->first();
        $this->assertEquals(self::$expectedOrderByTest['internalCode']['DESC'], $geoSet->internalCode);
    }
    /**
     * @dataProvider dataProviderIndexes
     * @testdox ==>  using $index as property
     * @throws QueryException
     */
    public function testOrderByWithDataProvider(string $index): void
    {
        $geoSets = self::$geoCodes->useLanguage('en')->geoSets();
        $asc = $geoSets->orderBy($index)->first();
        $this->assertEquals(self::$expectedOrderByTest[$index]['ASC'], $asc->{$index});
        $desc = $geoSets->orderBy($index, 'desc')->first();
        $this->assertEquals(self::$expectedOrderByTest[$index]['DESC'], $desc->{$index});
    }
    /**
     * @test
     * @testdox  ==>  using an invalid properties
     * @return void
     */
    public function testOrderByWithException(): void
    {
        $geoSets = self::$geoCodes->geoSets();

        // Invalid - `property` not indexable
        try {
            $geoSets->orderBy('notIndexable');
            $this->fail('An invalid orderBy property has been accepted');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11005, $e->getCode());
            $this->assertEquals(1, preg_match('/"notIndexable"/', $e->getMessage()));
        }

        // Invalid - `orderType` invalid
        try {
            $geoSets->orderBy('internalCode', 'invalid');
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
        $indexes = self::$geoCodes->geoSets()->getIndexes();
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
            self::$geoCodes->geoSets()->withIndex($index)->offset(0)->limit(1)->get()->toArray() as $key => $geoSet
        ) {
            $this->assertEquals($key, $geoSet[$index]);
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
            self::$geoCodes->geoSets()->withIndex('invalidField');
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
        $selectFields = self::$geoCodes->geoSets()->selectableFields();
        $this->assertIsArray($selectFields);
        $geoSets = self::$geoCodes->geoSets()->get();
        foreach ($geoSets->collect() as $geoSet) {
            foreach ($selectFields as $key => $description) {
                $prop = $key;
                $object = $geoSet;
                if (preg_match('/\./', $prop)) {
                    list($prop0, $prop) = explode('.', $prop);
                    $object = $geoSet->{$prop0};
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
                    'Key type `' . $key . '` for the geoset `' . $geoSet->name .
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
        $geoSet = self::$geoCodes->geoSets()->first();
        foreach ($selectFields as $key) {
            $prop = $key;
            $object = $geoSet;
            if (preg_match('/\./', $prop)) {
                list($prop0, $prop) = explode('.', $prop);
                $object = $geoSet->{$prop0};
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
        $geoSets = self::$geoCodes->geoSets();
        $selectFields = array_keys($geoSets->selectableFields());
        $geoSets->select(...$selectFields);
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
        $geoSets = self::$geoCodes->geoSets();
        $selectFields = array_keys($geoSets->selectableFields());
        foreach ($selectFields as $key) {
            $geoSets->select($key);
            $geoSets->select($key); // test also the redundancy
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
        $geoSets = self::$geoCodes->geoSets();
        $geoSets->select($select);
        $geoSet = $geoSets->first();
        if (preg_match('/\./', $select)) {
            list($prop0, $prop) = explode('.', $select);
            $geoSet = $geoSet->{$prop0};
        }
        $count = count(get_object_vars($geoSet));
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
            self::$geoCodes->geoSets()->select('invalidField');
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
     * @testdox ==> using - as valid - in input integer, string, array.
     * @return void
     * @throws QueryException
     */
    public function testFetchFeatureValidInput(): void
    {
        $cfr = [
            'CONV-G7'       => [ 'internalCode' => 'CONV-G7' ],
            'GEOG-AF-NO'    => [ 'internalCode' => 'GEOG-AF-NO'],
            'CONV-SCHENGEN' => [ 'internalCode' => 'CONV-SCHENGEN' ],
            'ZONE-EZ'       => [ 'internalCode' => 'ZONE-EZ' ]
        ];
        $geoSets = self::$geoCodes->geoSets();
        $geoSets->fetch('CONV-G7', 15, ['CONV-SCHENGEN', 'ZONE-EZ']);
        $result = $geoSets->withIndex('internalCode')->select('internalCode')->get()->toArray();
        $this->assertEquals($cfr, $result);
    }

    /**
     * @test
     * @testdox ==> with Exception (using array or arrays in input).
     * @return void
     */
    public function testFetchFeatureWithException(): void
    {
        $geoSets = self::$geoCodes->geoSets();
        try {
            $geoSets->fetch('CONV-G7', 15, [['CONV-SCHENGEN'], ['ZONE-EZ']]);
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
            'CONV-G7'       => [ 'internalCode' => 'CONV-G7' ],
            'GEOG-AF-NO'    => [ 'internalCode' => 'GEOG-AF-NO'],
            'CONV-SCHENGEN' => [ 'internalCode' => 'CONV-SCHENGEN' ],
            'ZONE-EZ'       => [ 'internalCode' => 'ZONE-EZ' ]
        ];
        $geoSets = self::$geoCodes->geoSets();
        $geoSets->fetch('CONV-G7', 15)->fetch(['CONV-SCHENGEN', 'ZONE-EZ']);
        $result = $geoSets->withIndex('internalCode')->select('internalCode')->get()->toArray();
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
        $geoSets = self::$geoCodes->geoSets();
        $fetchAll = $geoSets->fetchAll()->get();
        $fetchStar = $geoSets->fetch('*')->get();
        $fetchWithStar = $geoSets->fetch('CONV-G7', 15, ['CONV-SCHENGEN', 'ZONE-EZ'], '*')->get();
        $this->assertEquals($fetchAll, self::$geoSetsList);
        $this->assertEquals($fetchStar, self::$geoSetsList);
        $this->assertEquals($fetchWithStar, self::$geoSetsList);
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
            'CONV-G7'       => [ 'internalCode' => 'CONV-G7' ],
            'GEOG-AF-NO'    => [ 'internalCode' => 'GEOG-AF-NO'],
            'CONV-SCHENGEN' => [ 'internalCode' => 'CONV-SCHENGEN' ],
            'ZONE-EZ'       => [ 'internalCode' => 'ZONE-EZ' ]
        ];
        $geoSets = self::$geoCodes->geoSets();
        $geoSets->fetch('CONV-G7', 15)->fetch(['CONV-SCHENGEN', 'ZONE-EZ']);
        $geoSets->merge();
        $result = $geoSets->withIndex('internalCode')->select('internalCode')->get()->toArray();
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
            'CONV-G7'       => [ 'internalCode' => 'CONV-G7' ],
            'GEOG-AF-NO'    => [ 'internalCode' => 'GEOG-AF-NO'],
            'CONV-SCHENGEN' => [ 'internalCode' => 'CONV-SCHENGEN' ],
            'ZONE-EZ'       => [ 'internalCode' => 'ZONE-EZ' ]
        ];
        $geoSets = self::$geoCodes->geoSets();
        $geoSets->fetch(15)->fetch(['ZONE-EZ']);
        $geoSets->merge();
        $geoSets->fetch('CONV-G7')->fetch(['CONV-SCHENGEN']);
        $geoSets->merge();
        $result = $geoSets->withIndex('internalCode')->select('internalCode')->get()->toArray();
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
            'GEOG-AF-NO'    => [ 'internalCode' => 'GEOG-AF-NO']
        ];
        $geoSets = self::$geoCodes->geoSets();
        $geoSets->fetch('CONV-G7', 15)->fetch(['CONV-SCHENGEN', 'ZONE-EZ', 'GEOG-AF-NO']);
        $geoSets->intersect();
        $result = $geoSets->withIndex('internalCode')->select('internalCode')->get()->toArray();
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
            'GEOG-AF-NO'    => [ 'internalCode' => 'GEOG-AF-NO']
        ];
        $geoSets = self::$geoCodes->geoSets();
        $geoSets->fetch('CONV-G7', 15)->fetch(['CONV-SCHENGEN', 'ZONE-EZ', 'GEOG-AF-NO']);
        $geoSets->intersect();
        $geoSets->fetch('CONV-G7', 'GEOG-AF-NO')->fetch(['CONV-SCHENGEN', 'ZONE-EZ', 15]);
        $geoSets->intersect();
        $geoSets->intersect();
        $result = $geoSets->withIndex('internalCode')->select('internalCode')->get()->toArray();
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
        $geoSets = self::$geoCodes->geoSets();
        $geoSets->fetch('CONV-G7', 15);
        try {
            $geoSets->intersect();
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
            'CONV-G7'       => [ 'internalCode' => 'CONV-G7' ],
            'CONV-SCHENGEN' => [ 'internalCode' => 'CONV-SCHENGEN' ],
            'ZONE-EZ'       => [ 'internalCode' => 'ZONE-EZ' ]
        ];
        $geoSets = self::$geoCodes->geoSets();
        $geoSets->fetch('CONV-G7', 15)->fetch(['CONV-SCHENGEN', 'ZONE-EZ', 'GEOG-AF-NO']);
        $geoSets->complement();
        $result = $geoSets->withIndex('internalCode')->select('internalCode')->get()->toArray();
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
            'GEOG-AF-NO'       => [ 'internalCode' => 'GEOG-AF-NO' ],
            'GEOG-OC-MI'       => [ 'internalCode' => 'GEOG-OC-MI' ]
        ];
        $geoSets = self::$geoCodes->geoSets();
        $geoSets->fetch(15, 'GEOG-OC-MI')->fetch(['GEOG-AF-NO']);
        $geoSets->complement();
        $geoSets->fetch(15, 57)->fetch(['GEOG-OC-MI']);
        $geoSets->complement();
        $geoSets->complement();
        $result = $geoSets->withIndex('internalCode')->select('internalCode')->get()->toArray();
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
        $geoSets = self::$geoCodes->geoSets();
        $geoSets->fetch('CONV-G7', 15);
        try {
            $geoSets->complement();
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
        $geoSets = self::$geoCodes->geoSets();
        try {
            $geoSets->where(...$args);
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
            $geoSets->orWhere(...$args);
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
                "'inteRnalCode', 'CONV-G7'",
                ['inteRnalCode', 'CONV-G7'],
                11015,
                ['inteRnalCode']
            ],
            [
                "'internalCode.inexistent', 'CONV-G7'",
                ['internalCode.inexistent', 'CONV-G7'],
                11015,
                ['internalCode.inexistent']
            ],
            [
                "['internalCode.inexistent', 'CONV-G7']",
                [['internalCode.inexistent', 'CONV-G7']],
                11015,
                ['internalCode.inexistent']
            ],
            [
                "[['internalCode.inexistent', 'CONV-G7']]",
                [[['internalCode.inexistent', 'CONV-G7']]],
                11015,
                ['internalCode.inexistent']
            ],
            [
                "['internalCode', '=', ['CONV-G7', 'CONV-G20']]]",
                [['internalCode', '=', ['CONV-G7', 'CONV-G20']]],
                11014,
                ['=']
            ],
            [
                "[['internalCode', 'IN', 'CONV-G7']]",
                [[['internalCode', 'IN', 'CONV-G7']]],
                11013,
                ['IN']
            ]
        ];
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
        $geoSets = self::$geoCodes->geoSets();
        $geoSets->$method(...$args);
        $result = $geoSets->withIndex('internalCode')->select('internalCode')->get()->toArray();
        $this->assertEquals($matches, $result);
    }
    /**
     * @return array<int, array<int, array<int|string, array<int|string, array<int, int|string>|string>|string>|string>>
     */
    public function dataProviderValidConditions(): array
    {
        return [
            [
                "'internalCode', 'GEOG-EU'",
                ['internalCode', 'GEOG-EU'],
                'where',
                ['GEOG-EU' => [ 'internalCode' => 'GEOG-EU' ]]
            ],
            [
                "'internalCode', '=', 'GEOG-EU'",
                ['internalCode', '=', 'GEOG-EU'],
                'where',
                ['GEOG-EU' => [ 'internalCode' => 'GEOG-EU' ]]
            ],
            [
                "['internalCode', '=', 'GEOG-EU']",
                [['internalCode', '=', 'GEOG-EU']],
                'where',
                ['GEOG-EU' => [ 'internalCode' => 'GEOG-EU' ]]
            ],
            [
                "[['internalCode', '=', 'GEOG-EU']]",
                [[['internalCode', '=', 'GEOG-EU']]],
                'where',
                ['GEOG-EU' => [ 'internalCode' => 'GEOG-EU' ]]
            ],
            [
                "[['internalCode', '=', 'GEOG-EU'], ['internalCode', '=', 'GEOG-AS-WE']]",
                [[['internalCode', '=', 'GEOG-EU'], ['internalCode', '=', 'GEOG-AS-WE']]],
                'where',
                []
            ],
            [
                "[['internalCode', '=', 'GEOG-EU'], ['unM49', '=', '150']]",
                [[['internalCode', '=', 'GEOG-EU'], ['unM49', '=', '150']]],
                'where',
                ['GEOG-EU' => [ 'internalCode' => 'GEOG-EU' ]]
            ],
            [
                "[['internalCode', '=', 'GEOG-EU'], ['unM49', '=', '150'], ['tags', 'geography']]",
                [[['internalCode', '=', 'GEOG-EU'], ['unM49', '=', '150'], ['tags', 'geography']]],
                'where',
                ['GEOG-EU' => [ 'internalCode' => 'GEOG-EU' ]]
            ],
            [
                "[['internalCode', '=', 'GEOG-EU'],['unM49', '=', '150'],['tags', 'geography'],['countryCodes', 'ie']]",
                [
                    [
                        ['internalCode', '=', 'GEOG-EU'], ['unM49', '=', '150'], ['tags', 'geography'],
                        ['countryCodes', 'ie']
                    ]   ],
                'where',
                ['GEOG-EU' => [ 'internalCode' => 'GEOG-EU' ]]
            ],
            [
                "'tags', 'zone'",
                ['tags', 'zone'],
                'where',
                ['ZONE-EZ' => [ 'internalCode' => 'ZONE-EZ' ]]
            ],
            [
                "[['countryCodes', 'IE'], ['tags', 'zone']]",
                [[['countryCodes', 'IE'], ['tags', 'zone']]],
                'where',
                ['ZONE-EZ' => [ 'internalCode' => 'ZONE-EZ' ]]
            ],
            [
                "'name', 'like', 'Channel%'",
                ['name', 'like', 'Channel%'],
                'where',
                ['GEOG-EU-NO-CH' => [ 'internalCode' => 'GEOG-EU-NO-CH' ]]
            ],
            [
                "'name', 'like', '%Channel%'",
                ['name', 'like', '%Channel%'],
                'where',
                ['GEOG-EU-NO-CH' => [ 'internalCode' => 'GEOG-EU-NO-CH' ]]
            ],
            [
                "'name', 'like', '%Islands'",
                ['name', 'like', '%Islands'],
                'where',
                ['GEOG-EU-NO-CH' => [ 'internalCode' => 'GEOG-EU-NO-CH' ]]
            ],
            [
                "'name', 'like', '%Islands%'",
                ['name', 'like', '%Islands%'],
                'where',
                ['GEOG-EU-NO-CH' => [ 'internalCode' => 'GEOG-EU-NO-CH' ]]
            ],
            [
                "'name', 'like', '%anne%'",
                ['name', 'like', '%anne%'],
                'where',
                ['GEOG-EU-NO-CH' => [ 'internalCode' => 'GEOG-EU-NO-CH' ]]
            ],
            [
                "[['name', 'like', '%Euro%'], ['name', 'not like', '%Europe%']]",
                [[['name', 'like', '%Euro%'], ['name', 'not like', '%Europe%']]],
                'where',
                ['ZONE-EZ' => [ 'internalCode' => 'ZONE-EZ' ]]
            ],
            [
                "[['unM49', '<=', 2], ['unM49', 'is not null']]",
                [[['unM49', '<=', 2], ['unM49', 'is not null']]],
                'where',
                ['GEOG-AF' => [ 'internalCode' => 'GEOG-AF' ]]
            ],
            [
                "[['unM49', '>=', '2'], ['unM49', '<', '5']]",
                [[['unM49', '>=', '2'], ['unM49', '<', '5']]],
                'where',
                ['GEOG-AF' => [ 'internalCode' => 'GEOG-AF' ]]
            ],
            [
                "[['unM49', '<', 5], ['unM49', 'is not null']]",
                [[['unM49', '<', 5], ['unM49', 'is not null']]],
                'where',
                ['GEOG-AF' => [ 'internalCode' => 'GEOG-AF' ]]
            ],
            [
                "[['unM49', '>', '1'], ['unM49', '<', '5']]",
                [[['unM49', '>', '1'], ['unM49', '<', '5']]],
                'where',
                ['GEOG-AF' => [ 'internalCode' => 'GEOG-AF' ]]
            ],
            [
                "[['unM49', 'is NOT null'], ['internalCode', 'gEoG-aF']]",
                [[['unM49', 'is NOT null'], ['internalCode', 'gEoG-aF']]],
                'where',
                ['GEOG-AF' => [ 'internalCode' => 'GEOG-AF' ]]
            ],
        ];
    }


    public function testStica(): void
    {
        $geoSets = self::$geoCodes->geoSets();
        $geoSets->where([['unM49', '<=', '2'], ['unM49', 'is not null']]);
        $result = $geoSets->withIndex('internalCode')->select('internalCode')->get()->toArray();

        $elenaMyfile = fopen("/Users/aliberati/ALIBE/test.log", "a") or die("Unable to open file!");
        fwrite($elenaMyfile, print_r($result, true) . "\n");
        fclose($elenaMyfile);

        $this->assertTrue(true);
    }
}
