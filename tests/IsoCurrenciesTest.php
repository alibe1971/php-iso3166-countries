<?php

namespace Alibe\GeoCodes\Tests;

use Alibe\GeoCodes\Lib\DataObj\Currencies;
use Alibe\GeoCodes\Lib\DataObj\Elements\Currency;
use Alibe\GeoCodes\Lib\Exceptions\GeneralException;
use Alibe\GeoCodes\Lib\Exceptions\QueryException;
use DOMDocument;
use PHPUnit\Framework\TestCase;
use Alibe\GeoCodes\GeoCodes;
use SimpleXMLElement;
use Symfony\Component\Yaml\Yaml;

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
     * @var string
     */
    private static string $xsdList;

    /**
     * @var string
     */
    private static string $xsdSingle;

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
     * @throws QueryException
     */
    public function testGetToJsonFeature(): void
    {
        $currencies = self::$geoCodes->currencies();
        foreach (
            [
            // Whole list
            self::$currenciesSetsList,
            // Whole list with index
            $currencies->withIndex('name')->get(),
            // Single element in list
            $currencies->take(1)->get(),
            // Empty
            $currencies->take(0)->get(),
            ] as $testCase
        ) {
            $json = $testCase->toJson();
            $expectedData = $testCase->toArray();
            $this->assertIsString($json);
            $decodedJson = json_decode($json, true);
            $this->assertNotNull($decodedJson, 'Not a valid JSON');
            $this->assertIsArray($decodedJson, 'Not a valid JSON');
            $this->assertEquals($expectedData, $decodedJson, 'Converted JSON does not match expected data');
        }
    }

    /**
     * @test
     * @testdox Test the `->get()->toYaml()` feature.
     * @depends testToGetListOfCurrencies
     * @return void
     * @throws QueryException
     */
    public function testGetToYamlFeature(): void
    {
        $currencies = self::$geoCodes->currencies();
        foreach (
            [
            // Whole list
            self::$currenciesSetsList,
            // Whole list with index
            $currencies->withIndex('name')->get(),
            // Single element in list
            $currencies->take(1)->get(),
            // Empty
            $currencies->take(0)->get(),
            ] as $testCase
        ) {
            $yaml = $testCase->toYaml();
            $expectedData = $testCase->toArray();
            $this->assertIsString($yaml);
            $decodedYaml = Yaml::parse($yaml);
            $this->assertNotNull($decodedYaml, 'Not a valid YAML');
            $this->assertIsArray($decodedYaml, 'Not a valid YAML');
            $this->assertEquals($expectedData, $decodedYaml, 'Converted YAML does not match expected data');
        }
    }

    /**
     * @test
     * @testdox Test the `->getXsd()` and the `->getXsdSingle()` features.
     * @depends testToGetListOfCurrencies
     * @return void
     * @throws GeneralException
     */
    public function testGetXsdFeatures(): void
    {
        self::$xsdList = self::$geoCodes->currencies()->getXsd();
        $this->assertIsString(self::$xsdList);

        self::$xsdSingle = self::$geoCodes->currencies()->getXsdSingle();
        $this->assertIsString(self::$xsdSingle);
    }

    /**
     * @test
     * @testdox Test the `->get()->toXml()` feature and validate it with external xsd.
     * @depends testToGetListOfCurrencies
     * @depends testGetXsdFeatures
     * @return void
     * @throws QueryException|GeneralException
     */
    public function testGetToXmlFeatureWithExternalValidation(): void
    {
        $currencies = self::$geoCodes->currencies();
        foreach (
            [
            // Whole list
            self::$currenciesSetsList,
            // Whole list with index
            $currencies->withIndex('name')->get(),
            // Single element in list
            $currencies->take(1)->get(),
            // Empty
            $currencies->take(0)->get(),
            ] as $testCase
        ) {
            $xml = $testCase->toXml();
            $this->assertIsString($xml);
            $decodedXml = simplexml_load_string($xml);
            $this->assertInstanceOf(SimpleXMLElement::class, $decodedXml, 'Not a valid XML');
            $dom = new DOMDocument();
            $dom->loadXML($xml);
            $this->assertTrue($dom->schemaValidateSource(self::$xsdList), 'Not a valid XML');
        }
    }

    /**
     * @test
     * @testdox Test the `->get()->toXmlAndValidate()` feature.
     * @depends testToGetListOfCurrencies
     * @return void
     * @throws QueryException|GeneralException
     */
    public function testGetToXmlAndValidateFeature(): void
    {
        $currencies = self::$geoCodes->currencies();
        foreach (
            [
            // Whole list
            self::$currenciesSetsList,
            // Whole list with index
            $currencies->withIndex('name')->get(),
            // Single element in list
            $currencies->take(1)->get(),
            // Empty
            $currencies->take(0)->get(),
            ] as $testCase
        ) {
            $xml = $testCase->toXmlAndValidate();
            $this->assertIsString($xml);
            $decodedXml = simplexml_load_string($xml);
            $this->assertInstanceOf(SimpleXMLElement::class, $decodedXml, 'Not a valid XML');
        }
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
        $currencies->offset(0)->limit(0);
        $currency = $currencies->first();

        $this->assertIsObject($currency);
        $this->assertInstanceOf(Currency::class, $currency);
    }

    /**
     * @test
     * @testdox Test the `->first()->toJson()` feature.
     * @depends testFirstFeature
     * @return void
     * @throws QueryException
     */
    public function testFirstToJsonFeature(): void
    {
        foreach (
            [
                // Single Existent Currency
                self::$currency,
                // Empty
                self::$geoCodes->currencies()->take(0)->first(),
            ] as $testCase
        ) {
            $json = $testCase->toJson();
            $expectedData = $testCase->toArray();
            $this->assertIsString($json);
            $decodedJson = json_decode($json, true);
            $this->assertNotNull($decodedJson, 'Not a valid JSON');
            $this->assertIsArray($decodedJson, 'Not a valid JSON');
            $this->assertEquals($expectedData, $decodedJson, 'Converted JSON does not match expected data');
        }
    }

    /**
     * @test
     * @testdox Test the `->first()->toYaml()` feature.
     * @depends testFirstFeature
     * @return void
     * @throws QueryException
     */
    public function testFirstToYamlFeature(): void
    {
        foreach (
            [
                // Single Existent Currency
                self::$currency,
                // Empty
                self::$geoCodes->currencies()->take(0)->first(),
            ] as $testCase
        ) {
            $yaml = $testCase->toYaml();
            $expectedData = $testCase->toArray();
            $this->assertIsString($yaml);
            $decodedYaml = Yaml::parse($yaml);
            $this->assertNotNull($decodedYaml, 'Not a valid YAML');
            $this->assertIsArray($decodedYaml, 'Not a valid YAML');
            $this->assertEquals($expectedData, $decodedYaml, 'Converted YAML does not match expected data');
        }
    }

    /**
     * @test
     * @testdox Test the `->first()->toXml()` feature and validate it with external xsd.
     * @depends testFirstFeature
     * @depends testGetXsdFeatures
     * @return void
     * @throws QueryException|GeneralException
     */
    public function testFirstToXmlFeatureWithExternalValidation(): void
    {
        foreach (
            [
                // Single Existent Currency
                self::$currency,
                // Empty
                self::$geoCodes->currencies()->take(0)->first(),
            ] as $testCase
        ) {
            $xml = $testCase->toXml();
            $this->assertIsString($xml);
            $decodedXml = simplexml_load_string($xml);
            $this->assertInstanceOf(SimpleXMLElement::class, $decodedXml, 'Not a valid XML');
            $dom = new DOMDocument();
            $dom->loadXML($xml);
            $this->assertTrue($dom->schemaValidateSource(self::$xsdSingle), 'Not a valid XML');
        }
    }

    /**
     * @test
     * @testdox Test the `->first()->toXmlAndValidate()` feature.
     * @depends testFirstFeature
     * @return void
     * @throws QueryException
     */
    public function testFirstToXmlAndValidateFeature(): void
    {
        foreach (
            [
            // Single Existent Currency
            self::$currency,
            // Empty
            self::$geoCodes->currencies()->take(0)->first(),
            ] as $testCase
        ) {
            $xml = $testCase->toXmlAndValidate();
            $this->assertIsString($xml);
            $decodedXml = simplexml_load_string($xml);
            $this->assertInstanceOf(SimpleXMLElement::class, $decodedXml, 'Not a valid XML');
        }
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
            $currencies->offset(21)->limit($numberOfItems);
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
     * @testdox Test the interval `->offset()->limit()` or the aliases `->skip()->take()` features.
     * @return void
     * @throws QueryException
     */
    public function testLimit(): void
    {
        $currencies = self::$geoCodes->currencies();

        // Invalid - `from` less than 0
        try {
            $currencies->offset(-5)->limit(20);
            $this->fail('An invalid limit from has been accepted');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11004, $e->getCode());
        }

        // Invalid - `numberOfItems` less than 0
        try {
            $currencies->offset(20)->limit(-5);
            $this->fail('An invalid limit numberOfItems has been accepted');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11003, $e->getCode());
        }

        // Valid input
        $currencies->offset(22)->limit(2);
        $this->assertEquals(2, $currencies->count());
        $get = $currencies->get();
        $this->assertEquals(self::$expectedLimitTest[0], $get->{0}->isoAlpha);
        $this->assertEquals(self::$expectedLimitTest[1], $get->{1}->isoAlpha);

        // Alias input
        $currencies->skip(22)->take(2);
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
            $this->fail('An invalid orderBy property has been accepted');
        } catch (QueryException $e) {
            $this->assertInstanceOf(QueryException::class, $e);
            $this->assertEquals(11005, $e->getCode());
            $this->assertEquals(1, preg_match('/"notIndexable"/', $e->getMessage()));
        }

        // Invalid - `orderType` invalid
        try {
            $currencies->orderBy('isoAlpha', 'invalid');
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
            self::$geoCodes->currencies()->withIndex($index)->offset(0)->limit(1)->get()->toArray() as $key => $currency
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
        try {
            self::$geoCodes->currencies()->withIndex('invalidField');
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
                    'Key `' . $key . '` not present in the currency object'
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
                'Key `' . $key . '` not present in the currency object'
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
        try {
            self::$geoCodes->currencies()->select('invalidField');
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
     * @testdox ==> using - as valid -  in input integer, string, array.
     * @return void
     * @throws QueryException
     */
    public function testFetchFeatureValidInput(): void
    {
        $cfr = [
            'EUR' => [ 'isoAlpha' => 'EUR' ],
            'AUD' => [ 'isoAlpha' => 'AUD' ],
            'USD' => [ 'isoAlpha' => 'USD' ],
            'BRL' => [ 'isoAlpha' => 'BRL' ]
        ];
        $currencies = self::$geoCodes->currencies();
        $currencies->fetch('EUR', 36, ['USD', 986]);
        $result = $currencies->withIndex('isoAlpha')->select('isoAlpha')->get()->toArray();
        $this->assertEquals($cfr, $result);
    }

    /**
     * @test
     * @testdox ==> with Exception (using array or arrays in input).
     * @return void
     */
    public function testFetchFeatureWithException(): void
    {
        $currencies = self::$geoCodes->currencies();
        try {
            $currencies->fetch('EUR', 36, [['USD'], [986]]);
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
            'EUR' => [ 'isoAlpha' => 'EUR' ],
            'AUD' => [ 'isoAlpha' => 'AUD' ],
            'USD' => [ 'isoAlpha' => 'USD' ],
            'BRL' => [ 'isoAlpha' => 'BRL' ]
        ];
        $currencies = self::$geoCodes->currencies();
        $currencies->fetch('EUR', 36)->fetch(['USD', 986]);
        $result = $currencies->withIndex('isoAlpha')->select('isoAlpha')->get()->toArray();
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
        $currencies = self::$geoCodes->currencies();
        $fetchAll = $currencies->fetchAll()->get();
        $fetchStar = $currencies->fetch('*')->get();
        $fetchWithStar = $currencies->fetch('EUR', 36, ['USD', '*'])->get();
        $this->assertEquals($fetchAll, self::$currenciesSetsList);
        $this->assertEquals($fetchStar, self::$currenciesSetsList);
        $this->assertEquals($fetchWithStar, self::$currenciesSetsList);
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
            'EUR' => [ 'isoAlpha' => 'EUR' ],
            'AUD' => [ 'isoAlpha' => 'AUD' ],
            'USD' => [ 'isoAlpha' => 'USD' ],
            'BRL' => [ 'isoAlpha' => 'BRL' ]
        ];
        $currencies = self::$geoCodes->currencies();
        $currencies->fetch('EUR', 36)->fetch(['USD', 986]);
        $currencies->merge();
        $result = $currencies->withIndex('isoAlpha')->select('isoAlpha')->get()->toArray();
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
            'EUR' => [ 'isoAlpha' => 'EUR' ],
            'AUD' => [ 'isoAlpha' => 'AUD' ],
            'USD' => [ 'isoAlpha' => 'USD' ],
            'BRL' => [ 'isoAlpha' => 'BRL' ]
        ];
        $currencies = self::$geoCodes->currencies();
        $currencies->fetch('EUR');
        $currencies->fetch(36);
        $currencies->merge();
        $currencies->fetch(['USD', 986]);
        $currencies->merge();
        $currencies->merge();
        $result = $currencies->withIndex('isoAlpha')->select('isoAlpha')->get()->toArray();
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
            'USD' => [ 'isoAlpha' => 'USD' ]
        ];
        $currencies = self::$geoCodes->currencies();
        $currencies->fetch('EUR', 36, 840)->fetch(['USD', 986]);
        $currencies->intersect();
        $result = $currencies->withIndex('isoAlpha')->select('isoAlpha')->get()->toArray();
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
            'USD' => [ 'isoAlpha' => 'USD' ]
        ];
        $currencies = self::$geoCodes->currencies();
        $currencies->fetch('EUR', 36, 840)->fetch(['USD', 986]);
        $currencies->intersect();
        $currencies->fetch(['USD', 986]);
        $currencies->fetch([840]);
        $currencies->intersect();
        $currencies->intersect();
        $result = $currencies->withIndex('isoAlpha')->select('isoAlpha')->get()->toArray();
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
        $currencies = self::$geoCodes->currencies();
        $currencies->fetch('EUR', 36, 840);
        try {
            $currencies->intersect();
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
            'EUR' => [ 'isoAlpha' => 'EUR' ],
            'AUD' => [ 'isoAlpha' => 'AUD' ],
            'BRL' => [ 'isoAlpha' => 'BRL' ]
        ];
        $currencies = self::$geoCodes->currencies();
        $currencies->fetch('EUR', 36, 840)->fetch(['USD', 986]);
        $currencies->complement();
        $result = $currencies->withIndex('isoAlpha')->select('isoAlpha')->get()->toArray();
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
            'EUR' => [ 'isoAlpha' => 'EUR' ],
            'USD' => [ 'isoAlpha' => 'USD' ]
        ];
        $currencies = self::$geoCodes->currencies();
        $currencies->fetch(840, 'EUR')->fetch(['USD']);
        $currencies->complement();
        $currencies->fetch(840, 978)->fetch(['EUR']);
        $currencies->complement();
        $currencies->complement();
        $result = $currencies->withIndex('isoAlpha')->select('isoAlpha')->get()->toArray();
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
        $currencies = self::$geoCodes->currencies();
        $currencies->fetch('EUR', 36, 840);
        try {
            $currencies->complement();
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
        $currencies = self::$geoCodes->currencies();
        try {
            $currencies->where(...$args);
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
            $currencies->orWhere(...$args);
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
                "'isoAlPha', 'EUR'",
                ['isoAlPha', 'EUR'],
                11015,
                ['isoAlPha']
            ],
            [
                "'isoAlpha.inexistent', 'EUR'",
                ['isoAlpha.inexistent', 'EUR'],
                11015,
                ['isoAlpha.inexistent']
            ],
            [
                "['isoAlpha.inexistent', 'EUR']",
                [['isoAlpha.inexistent', 'EUR']],
                11015,
                ['isoAlpha.inexistent']
            ],
            [
                "[['isoAlpha.inexistent', 'EUR']]",
                [[['isoAlpha.inexistent', 'EUR']]],
                11015,
                ['isoAlpha.inexistent']
            ],
            [
                "['isoAlpha', '=', ['EUR', 'USD']]]",
                [['isoAlpha', '=', ['EUR', 'USD']]],
                11014,
                ['=']
            ],
            [
                "[['isoAlpha', 'IN', 'EUR']]",
                [[['isoAlpha', 'IN', 'EUR']]],
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
        $currencies = self::$geoCodes->currencies();
        $currencies->$method(...$args);
        $result = $currencies->withIndex('isoAlpha')->select('isoAlpha')->get()->toArray();
        $this->assertEquals($matches, $result);
    }
    /**
     * @return array<int,
     *     array<int, array<int|string, array<int|string, array<int, int|string>|string>|int|string>|string>>
     */
    public function dataProviderValidConditions(): array
    {
        return [
            [
                "'isoAlpha', 'EUR'",
                ['isoAlpha', 'EUR'],
                'where',
                ['EUR' => [ 'isoAlpha' => 'EUR' ]]
            ],
            [
                "'isoNumber', 978",
                ['isoNumber', 978],
                'where',
                ['EUR' => [ 'isoAlpha' => 'EUR' ]]
            ],
            [
                "'symbol', '€'",
                ['symbol', '€'],
                'where',
                ['EUR' => [ 'isoAlpha' => 'EUR' ]]
            ],
            [
                "'isoAlpha', '=', 'EUR'",
                ['isoAlpha', '=', 'EUR'],
                'where',
                ['EUR' => [ 'isoAlpha' => 'EUR' ]]
            ],
            [
                "['isoAlpha', '=', 'EUR']",
                [['isoAlpha', '=', 'EUR']],
                'where',
                ['EUR' => [ 'isoAlpha' => 'EUR' ]]
            ],
            [
                "[['isoAlpha', '=', 'EUR']]",
                [[['isoAlpha', '=', 'EUR']]],
                'where',
                ['EUR' => [ 'isoAlpha' => 'EUR' ]]
            ],
            [
                "[['isoAlpha', '=', 'EUR'], ['isoAlpha', '=', 'USD']]",
                [[['isoAlpha', '=', 'EUR'], ['isoAlpha', '=', 'USD']]],
                'where',
                []
            ],
            [
                "[['isoAlpha', '=', 'EUR'], ['isoNumber', '=', '978']]",
                [[['isoAlpha', '=', 'EUR'], ['isoNumber', '=', '978']]],
                'where',
                ['EUR' => [ 'isoAlpha' => 'EUR' ]]
            ],
            [
                "'name', 'like', 'Netherlands%'",
                ['name', 'like', 'Netherlands%'],
                'where',
                ['ANG' => [ 'isoAlpha' => 'ANG' ]]
            ],
            [
                "'name', 'like', '%Netherlands%'",
                ['name', 'like', '%Netherlands%'],
                'where',
                ['ANG' => [ 'isoAlpha' => 'ANG' ]]
            ],
            [
                "'name', 'like', '%Guilder'",
                ['name', 'like', '%Guilder'],
                'where',
                ['ANG' => [ 'isoAlpha' => 'ANG' ], 'AWG' => [ 'isoAlpha' => 'AWG' ]]
            ],
            [
                "'name', 'like', '%Guilder%'",
                ['name', 'like', '%Guilder%'],
                'where',
                ['ANG' => [ 'isoAlpha' => 'ANG' ], 'AWG' => [ 'isoAlpha' => 'AWG' ]]
            ],
            [
                "'name', 'like', '%ntillea%'",
                ['name', 'like', '%ntillea%'],
                'where',
                ['ANG' => [ 'isoAlpha' => 'ANG' ]]
            ],
            [
                "[['name', 'like', '%Euro'], ['name', 'not like', '%WIR%']]",
                [[['name', 'like', '%Euro'], ['name', 'not like', '%WIR%']]],
                'where',
                ['EUR' => [ 'isoAlpha' => 'EUR' ]]
            ],
            [
                "[['decimal', '<=', 2], ['isoNumber', '978']]",
                [[['decimal', '<=', 2], ['isoNumber', '978']]],
                'where',
                ['EUR' => [ 'isoAlpha' => 'EUR' ]]
            ],
            [
                "[['decimal', '>=', '2'], ['decimal', '<', '3'], ['isoNumber', '978']]",
                [[['decimal', '>=', '2'], ['decimal', '<', '3'], ['isoNumber', '978']]],
                'where',
                ['EUR' => [ 'isoAlpha' => 'EUR' ]]
            ],
            [
                "[['decimal', '<', 3], ['isoNumber', '978']]",
                [[['decimal', '<', 3], ['isoNumber', '978']]],
                'where',
                ['EUR' => [ 'isoAlpha' => 'EUR' ]]
            ],
            [
                "[['decimal', '>', '1'], ['decimal', '<', '3'], ['isoNumber', '978']]",
                [[['decimal', '>', '1'], ['decimal', '<', '3'], ['isoNumber', '978']]],
                'where',
                ['EUR' => [ 'isoAlpha' => 'EUR' ]]
            ],
            [
                "[['symbol', 'is NOT null'], ['isoNumber', 978]]",
                [[['symbol', 'is NOT null'], ['isoNumber', 978]]],
                'where',
                ['EUR' => [ 'isoAlpha' => 'EUR' ]]
            ],
            [
                "[['symbol', 'is null'], ['isoNumber', 646]]",
                [[['symbol', 'is null'], ['isoNumber', 646]]],
                'where',
                ['RWF' => [ 'isoAlpha' => 'RWF' ]]
            ],
        ];
    }

    public function testStica(): void
    {
//        $currencies = self::$geoCodes->currencies();
        $this->assertTrue(true);
    }
}
