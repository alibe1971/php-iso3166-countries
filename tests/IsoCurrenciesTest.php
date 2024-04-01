<?php

namespace Alibe\GeoCodes\Tests;

use Alibe\GeoCodes\Lib\DataObj\Currencies;
use Alibe\GeoCodes\Lib\DataObj\Elements\Currency;
use Alibe\GeoCodes\Lib\Exceptions\QueryException;
use PHPUnit\Framework\TestCase;
use Alibe\GeoCodes\GeoCodes;

/**
 * @testdox GeoSets
 */
final class IsoCurrenciesTest extends TestCase
{
    /**
     * @var int
     */
    private static int $currenciesTotalCount = 180;

    /**
     * @var array<int|array<string>> $constants
     */
    private static array $constants = [
        'indexes' => [
            'isoAlpha',
            'isoNumber',
            'name'
        ],
        'selectables' => [
            'isoAlpha',
            'isoNumber',
            'name',
            'symbol',
            'decimal'
        ]
    ];

    /**
     * @var array<int, string> $expectedLimitTest
     */
    private static array $expectedLimitTest = [
        'BTN',
        'BWP'
    ];

    /**
     * @var array<string, array<string, string>> $expectedOrderByTest
     */
    private static array $expectedOrderByTest = [
        'isoAlpha' => [
            'ASC' => 'AED',
            'DESC' => 'ZWL',
        ],
        'isoNumber' => [
            'ASC' => '008',
            'DESC' => '999',
        ],
        'name' => [
            'ASC' => 'ADB Unit of Account',
            'DESC' => 'Zloty',
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
     * @var Currencies
     */
    private static Currencies $currenciesSetsList;


    /**
     * @var Currency
     */
    private static Currency $currency;

    /**
     * @test
     * @testdox Test `->get()` the list of currencies is object as instance of Currencies.
     * @return void
     */
    public function testToGetListOfCurrencies(): void
    {
        /** @phpstan-ignore-next-line   The unique object type is needed for php 7.4 */
        self::$currenciesSetsList = self::$geoCodes->currencies()->get();
        $this->assertIsObject(self::$currenciesSetsList);
        $this->assertInstanceOf(Currencies::class, self::$currenciesSetsList);
    }

    /**
     * @test
     * @testdox Test the elements of the list of currencies are an instance of Currency.
     * @depends testToGetListOfCurrencies
     * @return void
     */
    public function testToGetElementListOfCurrencies(): void
    {
        $this->assertIsObject(self::$currenciesSetsList->{0});
        $this->assertInstanceOf(Currency::class, self::$currenciesSetsList->{0});
    }

    /**
     * @test
     * @testdox Test the `->get()->toJson()` feature.
     * @depends testToGetListOfCurrencies
     * @return void
     */
    public function testGetToJsonFeature(): void
    {
        $json = self::$currenciesSetsList->toJson();
        $this->assertIsString($json);
        $decodedJson = json_decode($json, true);
        $this->assertNotNull($decodedJson, 'Not a valid JSON');
        $this->assertIsArray($decodedJson, 'Not a valid JSON');

        $json = self::$currenciesSetsList->{0}->toJson();
        $this->assertIsString($json);
        $decodedJson = json_decode($json, true);
        $this->assertNotNull($decodedJson, 'Not a valid JSON');
        $this->assertIsArray($decodedJson, 'Not a valid JSON');
    }

    /**
     * @test
     * @testdox Test the `->get()->toArray()` feature.
     * @depends testToGetListOfCurrencies
     * @return void
     */
    public function testGetToArrayFeature(): void
    {
        $array = self::$currenciesSetsList->toArray();
        $this->assertIsArray($array, 'Not a valid Array');

        $array = self::$currenciesSetsList->{0}->toArray();
        $this->assertIsArray($array, 'Not a valid Array');
    }

    /**
     * @test
     * @testdox Test the `->get()->toFlatten()` feature (default separator `.`).
     * @depends testToGetListOfCurrencies
     * @return void
     */
    public function testGetToFlattenFeature(): void
    {
        $flatten = self::$currenciesSetsList->toFlatten();
        $this->assertIsArray($flatten, 'Not a valid Array');
        foreach (
            [
            mt_rand(0, (self::$currenciesTotalCount - 1)),
            mt_rand(0, (self::$currenciesTotalCount - 1)),
            mt_rand(0, (self::$currenciesTotalCount - 1)),
            mt_rand(0, (self::$currenciesTotalCount - 1)),
            mt_rand(0, (self::$currenciesTotalCount - 1))
            ] as $key
        ) {
            $this->assertEquals(self::$currenciesSetsList->$key->isoAlpha, $flatten[$key . '.isoAlpha']);
            $this->assertEquals(self::$currenciesSetsList->$key->isoNumber, $flatten[$key . '.isoNumber']);
            $this->assertEquals(self::$currenciesSetsList->$key->name, $flatten[$key . '.name']);
        };
    }

    /**
     * @test
     * @testdox Test the `->get()->toFlatten('_')` feature, using custom separator.
     * @depends testToGetListOfCurrencies
     * @return void
     */
    public function testGetToFlattenFeatureCustomSeparator(): void
    {
        $flatten = self::$currenciesSetsList->toFlatten('_');
        $this->assertIsArray($flatten, 'Not a valid Array');
        foreach (
            [
                     mt_rand(0, (self::$currenciesTotalCount - 1)),
                     mt_rand(0, (self::$currenciesTotalCount - 1)),
                     mt_rand(0, (self::$currenciesTotalCount - 1)),
                     mt_rand(0, (self::$currenciesTotalCount - 1)),
                     mt_rand(0, (self::$currenciesTotalCount - 1))
                 ] as $key
        ) {
            $this->assertEquals(self::$currenciesSetsList->$key->isoAlpha, $flatten[$key . '_isoAlpha']);
            $this->assertEquals(self::$currenciesSetsList->$key->isoNumber, $flatten[$key . '_isoNumber']);
            $this->assertEquals(self::$currenciesSetsList->$key->name, $flatten[$key . '_name']);
        };
    }

    /**
     * @test
     * @testdox Test the `->first()` feature as instance of Currency.
     * @return void
     * @throws QueryException
     */
    public function testFirstFeature(): void
    {
        /** @phpstan-ignore-next-line   The unique object type is needed for php 7.4 */
        self::$currency = self::$geoCodes->currencies()->first();

        $this->assertIsObject(self::$currency);
        $this->assertInstanceOf(Currency::class, self::$currency);
    }

    /**
     * @test
     * @testdox Test the `->first()` feature when result is empty as instance of Currency.
     * @return void
     * @throws QueryException
     */
    public function testFirstFeatureOnEmpty(): void
    {
        $currencies = self::$geoCodes->currencies();
        $currencies->limit(0, 0);
        $currency = $currencies->first();

        $this->assertIsObject($currency);
        $this->assertInstanceOf(Currency::class, $currency);
    }

    /**
     * @test
     * @testdox Test the `->first()->toJson()` feature.
     * @depends testFirstFeature
     * @return void
     */
    public function testFirstToJsonFeature(): void
    {
        $json = self::$currency->toJson();
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
        $array = self::$currency->toArray();
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
        $flatten = self::$currency->toFlatten();
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
        $flatten = self::$currency->toFlatten('_');
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
        $currencies = self::$geoCodes->currencies();
        $count = $currencies->count();
        $this->assertEquals(
            self::$currenciesTotalCount,
            $count,
            "The TOTAL number of the currency doesn't match with " . self::$currenciesTotalCount
        );

        foreach ([(self::$currenciesTotalCount - 21), 27, 5, 32, 0] as $numberOfItems) {
            $currencies->limit(21, $numberOfItems);
            $count = $currencies->count();
            $this->assertEquals(
                $numberOfItems,
                $count,
                "The number of the currency doesn't match with " . $numberOfItems
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
        $currencies = self::$geoCodes->currencies();

        // Invalid - `from` less than 0
        try {
            $currencies->limit(-5, 20);
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
        }

        // Invalid - `numberOfItems` less than 0
        try {
            $currencies->limit(20, -5);
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
        }

        // Valid input
        $currencies->limit(22, 2);
        $this->assertEquals(2, $currencies->count());
        $get = $currencies->get();

        $this->assertEquals(self::$expectedLimitTest[0], $get->{0}->isoAlpha);
        $this->assertEquals(self::$expectedLimitTest[1], $get->{1}->isoAlpha);
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
        $currencies = self::$geoCodes->useLanguage('en')->currencies();
        $currencies->orderBy('isoAlpha');
        $currency = $currencies->first();
        $this->assertEquals(self::$expectedOrderByTest['isoAlpha']['ASC'], $currency->isoAlpha);
        $currencies->orderBy('isoAlpha', 'desc');
        $currency = $currencies->first();
        $this->assertEquals(self::$expectedOrderByTest['isoAlpha']['DESC'], $currency->isoAlpha);
    }
    /**
     * @dataProvider dataProviderIndexes
     * @testdox ==>  using $index as property
     * @throws QueryException
     */
    public function testOrderByWithDataProvider(string $index): void
    {
        $currencies = self::$geoCodes->useLanguage('en')->currencies();
        $asc = $currencies->orderBy($index)->first();
        $this->assertEquals(self::$expectedOrderByTest[$index]['ASC'], $asc->{$index});
        $desc = $currencies->orderBy($index, 'desc')->first();
        $this->assertEquals(self::$expectedOrderByTest[$index]['DESC'], $desc->{$index});
    }
    /**
     * @test
     * @testdox  ==>  using an invalid properties
     * @return void
     */
    public function testOrderByWithException(): void
    {
        $currencies = self::$geoCodes->currencies();

        // Invalid - `property` not indexable
        try {
            $currencies->orderBy('notIndexable');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
        }

        // Invalid - `orderType` invalid
        try {
            $currencies->orderBy('isoAlpha', 'invalid');
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
        $indexes = self::$geoCodes->currencies()->getIndexes();
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
            self::$geoCodes->currencies()->withIndex($index)->limit(0, 1)->get()->toArray() as $key => $currency
        ) {
            $this->assertEquals($key, $currency[$index]);
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
        self::$geoCodes->currencies()->withIndex('invalidField');
    }

    /**
     * @test
     * @testdox Tests on the selectable fields.
     * @return void
     */
    public function testSelectableFields(): void
    {
        $selectFields = self::$geoCodes->currencies()->selectableFields();
        $this->assertIsArray($selectFields);
        $currencies = self::$geoCodes->currencies()->get();
        foreach ($currencies->collect() as $currency) {
            foreach ($selectFields as $key => $description) {
                $prop = $key;
                $object = $currency;
                if (preg_match('/\./', $prop)) {
                    list($prop0, $prop) = explode('.', $prop);
                    $object = $currency->{$prop0};
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
                    'Key type `' . $key . '` for the currency `' . $currency->name .
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
        $currency = self::$geoCodes->currencies()->first();
        foreach ($selectFields as $key) {
            $prop = $key;
            $object = $currency;
            if (preg_match('/\./', $prop)) {
                list($prop0, $prop) = explode('.', $prop);
                $object = $currency->{$prop0};
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
        $currencies = self::$geoCodes->currencies();
        $selectFields = array_keys($currencies->selectableFields());
        $currencies->select(...$selectFields);
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
        $currencies = self::$geoCodes->currencies();
        $selectFields = array_keys($currencies->selectableFields());
        foreach ($selectFields as $key) {
            $currencies->select($key);
            $currencies->select($key); // test also the redundancy
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
        $currencies = self::$geoCodes->currencies();
        $currencies->select($select);
        $currency = $currencies->first();
        if (preg_match('/\./', $select)) {
            list($prop0, $prop) = explode('.', $select);
            $currency = $currency->{$prop0};
        }
        $count = count(get_object_vars($currency));
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
        self::$geoCodes->currencies()->select('invalidField');
    }
}
