<?php

namespace Alibe\GeoCodes\Tests;

use Alibe\GeoCodes\Lib\DataSets;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

/**
 * @testdox Config Data Structure
 */
final class BaseDataStructureTest extends TestCase
{
    /**
     * @var string
     */
    private static string $dataDir;


    /**
     * @var string
     */
    private static string $defaultLanguage;

    /**
     * @var array<string, mixed>
     */
    private static array $Config = [];


    /**
     * @var array<string, mixed>
     */
    private static array $countriesData = [
        'alpha2' => [],
        'alpha3' => [],
        'unM49' => [],
        'officialName' => [],
        'ln' => [],
        'currencies' => [],
        'setsInternal' => [],
        'translations' => [
            'countries' => [],
            'geosets' => [],
            'currencies' => [],
            'languages' => []
        ]
    ];

    public static function setUpBeforeClass(): void
    {
        self::$dataDir = dirname(__DIR__) . '/src/Data';
    }


    /**
     * @test
     * @return void
     */
    public function testDataStructureExists(): void
    {
        $finder = new Finder();
        $finder->files()->in(self::$dataDir)->ignoreDotFiles(true);
        $dataFiles = [];
        foreach ($finder as $file) {
            $dataFiles[] = 1;
        }
        $this->assertNotEmpty($dataFiles);
    }

    /**
     * @test
     * @depends testDataStructureExists
     * @return void
     */
    public function testValidationConfigFile(): void
    {

        $config = self::$dataDir . '/config.php';

        $this->assertFileExists($config, 'The config file is missing');
        $config = include($config);

        $this->assertNotEmpty($config);

        $this->assertArrayHasKey(
            'settings',
            $config,
            'The section `settings` is not present in the config file'
        );
        $this->assertNotEmpty($config['settings'], 'The section `settings` is empty');

        $this->assertArrayHasKey(
            'languages',
            $config['settings'],
            'The section `languages` is not present inside the `settings`'
        );
        $this->assertNotEmpty($config['settings']['languages'], 'The section `languages` is empty');

        $this->assertArrayHasKey(
            'default',
            $config['settings']['languages'],
            'The property `default` is not present inside the `languages`'
        );
        $this->assertNotEmpty(
            $config['settings']['languages']['default'],
            'The property `default` is empty'
        );
        self::$defaultLanguage = $config['settings']['languages']['default'];


        $this->assertArrayHasKey(
            'inPackage',
            $config['settings']['languages'],
            'The property `inPackage` is not present inside the `languages`'
        );
        $this->assertIsArray(
            $config['settings']['languages']['inPackage'],
            'The property `inPackage` is not an array'
        );
        $this->assertNotEmpty(
            $config['settings']['languages']['inPackage'],
            'The property `inPackage` is empty'
        );
        $this->assertArrayHasKey(
            self::$defaultLanguage,
            $config['settings']['languages']['inPackage'],
            'The default language `'
                . self::$defaultLanguage .
                '` is not present inside the configuration set of the `languages` packages'
        );

        self::$Config = $config;
    }


    /**
     * @test
     * @depends testValidationConfigFile
     * @return void
     */
    public function testValidationCurrencyData(): void
    {
        $currencies = self::$dataDir . '/currencies.php';

        $this->assertFileExists($currencies, 'The `currencies` file is missing');
        $currencies = require_once $currencies;

        $this->assertNotEmpty($currencies);

        foreach ($currencies as $idx => $cur) {
            $this->assertArrayHasKey(
                'isoAlpha',
                $cur,
                'The property `isoAlpha` is not present inside the `currencies` data ' .
                'for the index `' . $idx . '`'
            );
            $this->assertNotContains(
                $cur['isoAlpha'],
                self::$countriesData['currencies'],
                'The currencies with `isoAlpha` `'
                . $cur['isoAlpha'] .
                '` is a duplicated key in the `currencies` data'
            );
            $this->assertMatchesRegularExpression(
                '/^[A-Z]{3}$/',
                $cur['isoAlpha'],
                'The country code `isoAlpha` `'
                . $cur['isoAlpha'] .
                '` must be three characters long.'
            );
            self::$countriesData['currencies'][] = $cur['isoAlpha'];

            $isoNumber = [];
            $this->assertArrayHasKey(
                'isoNumber',
                $cur,
                'The property `isoNumber` is not present inside the `currencies` data ' .
                'for the isoAlpha `' . $cur['isoAlpha'] . '`'
            );
            $this->assertNotContains(
                $cur['isoNumber'],
                $isoNumber,
                'The currency with `isoNumber` `'
                . $cur['isoAlpha'] .
                '` is a duplicated key in the `currencies` data'
            );
            $this->assertMatchesRegularExpression(
                '/^\d\d\d$/',
                $cur['isoNumber'],
                'The currency `isoNumber` `'
                . $cur['isoNumber'] .
                '` must be three digits long.'
            );
            $isoNumber[] = $cur['isoNumber'];

            $this->assertArrayHasKey(
                'symbol',
                $cur,
                'The property `symbol` is not present inside the `currencies` data ' .
                'for the isoAlpha `' . $cur['isoAlpha'] . '`'
            );
            $is_string = is_string($cur['symbol']);
            $this->assertTrue(
                $is_string || is_null($cur['symbol']),
                'The currency property `symbol` ' .
                ' for the isoAlpha `' . $cur['isoAlpha'] . '` must be "string" or "null" ' .
                '(`' . gettype($cur['symbol']) . '` returned)'
            );
            if ($is_string) {
                $this->assertNotEmpty(
                    trim(preg_replace('/\s+/u', '', $cur['symbol'])),
                    'The currency property `dependency` cannot be an empty string ' .
                    'for the isoAlpha `' . $cur['isoAlpha'] . '`'
                );
            }


            $this->assertArrayHasKey(
                'decimal',
                $cur,
                'The property `decimal` is not present inside the `currencies` data ' .
                'for the isoAlpha `' . $cur['isoAlpha'] . '`'
            );
            $is_int = is_int($cur['decimal']);
            $this->assertTrue(
                $is_int || is_null($cur['decimal']),
                'The currency property `decimal` ' .
                ' for the isoAlpha `' . $cur['isoAlpha'] . '` must be "integer" or "null" ' .
                '(`' . gettype($cur['decimal']) . '` returned)'
            );
        }
    }

    /**
     * @test
     * @depends testValidationConfigFile
     * @return void
     */
    public function testValidationGeoSetsData(): void
    {
        $geosets = self::$dataDir . '/geoSets.php';

        $this->assertFileExists($geosets, 'The `geoSets` file is missing');
        $geosets = require_once $geosets;

        $this->assertNotEmpty($geosets);

        self::$countriesData['setsInternal'] = [];
        $m49 = [];
        $aq = false;
        $geo = [];
        $geoLv0 = [];
        $geoLv1 = [
            'AQ'        // Exception for Antartica (AQ - 010) that is also a continent
        ];

        foreach ($geosets as $idx => $gs) {
            $this->assertArrayHasKey(
                'internalCode',
                $gs,
                'The property `internalCode` is not present inside the `geoSets` data ' .
                'for the index `' . $idx . '`'
            );
            $this->assertNotContains(
                $gs['internalCode'],
                self::$countriesData['setsInternal'],
                'The geoSets with `internalCode` `'
                . $gs['internalCode'] .
                '` is a duplicated key in the `geoSets` data'
            );
            $this->assertMatchesRegularExpression(
                '/^[A-Z0-9-]+$/',
                $gs['internalCode'],
                'The geoSets code `internalCode` `'
                . $gs['internalCode'] .
                '` must contains only uppercase characters, numbers and hyphens'
            );
            self::$countriesData['setsInternal'][] = $gs['internalCode'];

            $this->assertArrayHasKey(
                'tags',
                $gs,
                'The property `tags` is not present inside the `geoSets` data ' .
                'for the internalCode `' . $gs['internalCode'] . '`'
            );
            $this->assertIsArray(
                $gs['tags'],
                'The property `tags` is not an array in the `geoSets` data ' .
                'for the internalCode `' . $gs['internalCode'] . '`'
            );
            $this->assertNotEmpty(
                $gs['tags'],
                'The property `tags` is an empty array in the `geoSets` data ' .
                'for the internalCode `' . $gs['internalCode'] . '`'
            );

            $this->assertArrayHasKey(
                'countryCodes',
                $gs,
                'The property `countryCodes` is not present inside the `geoSets` data ' .
                'for the internalCode `' . $gs['internalCode'] . '`'
            );
            $this->assertIsArray(
                $gs['countryCodes'],
                'The property `countryCodes` is not an array in the `geoSets` data ' .
                'for the internalCode `' . $gs['internalCode'] . '`'
            );
            $this->assertNotEmpty(
                $gs['countryCodes'],
                'The property `countryCodes` is an empty array in the `geoSets` data ' .
                'for the internalCode `' . $gs['internalCode'] . '`'
            );


            if (preg_match('/^GEOG-/', $gs['internalCode'])) {
                $gArr = explode('-', $gs['internalCode']);
                $Lv = count($gArr) - 2;
                array_pop($gArr);
                $parent = implode('-', $gArr);
                if (!array_key_exists($parent, $geo)) {
                    $geo[$parent] = [];
                }
                foreach ($gs['countryCodes'] as $cc) {
                    if ($Lv != 0) {
                        $this->assertContains(
                            $cc,
                            $geo[$parent],
                            'Inside the country set in the geoSets data the value `'
                            . $cc .
                            '` in ' . $gs['internalCode'] .
                            ' has no correspondence in the parent group `' . $parent . '`'
                        );
                    }
                    if ($Lv < 2) {
                        $this->assertNotContains(
                            $cc,
                            ${'geoLv' . $Lv},
                            'Inside the country set in the geoSets data the value `'
                            . $cc .
                            '` in ' . $gs['internalCode'] . ' is a duplicated key, because already present ' .
                            'used in this or in another ' . $Lv . ' region'
                        );
                        $geo[$gs['internalCode']][] = $cc;
                        array_push(${'geoLv' . $Lv}, $cc);
                    }
                }
                $this->assertArrayHasKey(
                    'unM49',
                    $gs,
                    'The property `unM49` is not present inside the `geoSets` data ' .
                    'for the internal code `' . $gs['internalCode'] . '`'
                );
                $this->assertNotContains(
                    $gs['unM49'],
                    $m49,
                    'The geoSets with `unM49` `'
                    . $gs['internalCode'] .
                    '` is a duplicated key in the `geoSets` data'
                );
                $this->assertMatchesRegularExpression(
                    '/^[0-9]+$/',
                    $gs['unM49'],
                    'The geoSets code `unM49` `'
                    . $gs['internalCode'] .
                    '` must be three digits long'
                );
                $m49[] = $gs['unM49'];
                if ($gs['internalCode'] != 'GEOG-AQ') {  // Exception for Antartica (AQ - 010) that is also a continent
                    self::$countriesData['unM49'][] = $gs['unM49'];
                } else {
                    $aq = true;
                }
            }
        }

        $this->assertTrue($aq, 'It seems someone destroyed the continent of Antartica');

        $diff = array_diff($geoLv0, $geoLv1);
        $this->assertEmpty(
            $diff,
            'The following country codes are present in the grouped level 0 geo-region, ' .
            'but not in the grouped level 1 geo-region, ' .
            '[' . implode(', ', $diff) . ']'
        );

        $diff = array_diff($geoLv1, $geoLv0);
        $this->assertEmpty(
            $diff,
            'The following country codes are present in the grouped level 1 geo-region, ' .
            'but not in the grouped level 0 geo-region, ' .
            '[' . implode(', ', $diff) . ']'
        );
    }


    /**
     * @test
     * @depends testValidationCurrencyData
     * @depends testValidationGeoSetsData
     * @return void
     */
    public function testValidationCountryData(): void
    {
        $countries = self::$dataDir . '/countries.php';

        $this->assertFileExists($countries, 'The `countries` file is missing');
        $countries = require_once $countries;

        $this->assertNotEmpty($countries);

        $alpha2Values = array_column($countries, 'alpha2');

        foreach ($countries as $idx => $cc) {
            $this->assertArrayHasKey(
                'alpha2',
                $cc,
                'The property `alpha2` is not present inside the `countries` data ' .
                'for the index `' . $idx . '`'
            );
            $this->assertNotContains(
                $cc['alpha2'],
                self::$countriesData['alpha2'],
                'The country code with `alpha2` `'
                . $cc['alpha2'] .
                '` is a duplicated key in the `countries` data'
            );
            $this->assertMatchesRegularExpression(
                '/^[A-Z]{2}$/',
                $cc['alpha2'],
                'The country code `alpha2` `'
                . $cc['alpha2'] .
                '` must be two [UPPERCASE] characters long.'
            );
            self::$countriesData['alpha2'][] = $cc['alpha2'];

            $this->assertArrayHasKey(
                'alpha3',
                $cc,
                'The property `alpha3` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertNotContains(
                $cc['alpha3'],
                self::$countriesData['alpha3'],
                'The country code with `alpha3` `'
                . $cc['alpha3'] .
                '` is a duplicated key in the `countries` data'
            );
            $this->assertMatchesRegularExpression(
                '/^[A-Z]{3}$/',
                $cc['alpha3'],
                'The country code `alpha3` `'
                . $cc['alpha3'] .
                '` must be three [UPPERCASE] characters long.'
            );
            self::$countriesData['alpha3'][] = $cc['alpha3'];


            $this->assertArrayHasKey(
                'unM49',
                $cc,
                'The property `unM49` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertNotContains(
                $cc['unM49'],
                self::$countriesData['unM49'],
                'The country code with `unM49` `'
                . $cc['unM49'] .
                '` is a duplicated key in the group of `countries` and `geoSets` data'
            );
            $this->assertMatchesRegularExpression(
                '/^\d\d\d$/',
                $cc['unM49'],
                'The country code `unM49` `'
                . $cc['unM49'] .
                '` must be three digits long.'
            );
            self::$countriesData['unM49'][] = $cc['unM49'];


            $this->assertArrayHasKey(
                'flags',
                $cc,
                'The property `flags` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsArray(
                $cc['flags'],
                'The property `flags` is not an array ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertNotEmpty(
                $cc['flags'],
                'The property `flags` is empty ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertArrayHasKey(
                'svg',
                $cc['flags'],
                'The property `flags.svg` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsString(
                $cc['flags']['svg'],
                'The property `flags.svg` is not a string inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertTrue(
                DataSets::isValidSVG($cc['flags']['svg']),
                'The property `flags.svg` is not a valid SVG for the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );


            $this->assertArrayHasKey(
                'dependency',
                $cc,
                'The property `dependency` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $is_string = is_string($cc['dependency']);
            $this->assertTrue(
                $is_string || is_null($cc['dependency']),
                'The country property `dependency` ' .
                ' for the alpha2 `' . $cc['alpha2'] . '` must be "string" or "null" ' .
                '(`' . gettype($cc['dependency']) . '` returned)'
            );
            if ($is_string) {
                $this->assertNotEmpty(
                    trim(preg_replace('/\s+/u', '', $cc['dependency'])),
                    'The country property `dependency` cannot be an empty string ' .
                    'for the alpha2 `' . $cc['alpha2'] . '`'
                );
                $this->assertContains(
                    $cc['dependency'],
                    $alpha2Values,
                    'Dependency `' . $cc['dependency'] . '` does not match any existing `alpha2`'
                );
            }

            $this->assertArrayHasKey(
                'officialName',
                $cc,
                'The property `officialName` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsArray(
                $cc['officialName'],
                'The property `officialName` is not an array ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertNotEmpty(
                $cc['officialName'],
                'The property `officialName` is empty ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            foreach ($cc['officialName'] as $ln => $name) {
                if (!array_key_exists($ln, self::$countriesData['officialName'])) {
                    self::$countriesData['officialName'][$ln] = [];
                }
                $this->assertIsString(
                    $name,
                    'The country property `officialName` must have elements as string ' .
                    'for the alpha2 `' . $cc['alpha2'] . '`'
                );
                $this->assertNotEmpty(
                    trim(preg_replace('/\s+/u', '', $name)),
                    'The country property `officialName` cannot have elements as empty string ' .
                    'for the alpha2 `' . $cc['alpha2'] . '`'
                );
                $this->assertNotContains(
                    $name,
                    self::$countriesData['officialName'][$ln],
                    'The country code with `officialName` ' . $name . ' for the language `'
                    . $ln .
                    '` is a duplicated key in the `countries` data with the alpha2 `' . $cc['alpha2'] . '`'
                );
                self::$countriesData['officialName'][$ln][] = $name;
                self::$countriesData['ln'][] = $ln;
            }

            $this->assertArrayHasKey(
                'mottos',
                $cc,
                'The property `mottos` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsArray(
                $cc['mottos'],
                'The property `mottos` is not an array ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            if (!empty($cc['mottos'])) {
                foreach ($cc['mottos'] as $key => $mottoGr) {
                    $this->assertIsArray(
                        $mottoGr,
                        'The property `mottos.' . $key  . '` is not an array ' .
                        'for the alpha2 `' . $cc['alpha2'] . '`'
                    );
                    $this->assertNotEmpty(
                        $mottoGr,
                        'The property `mottos.' . $key  . '` is empty ' .
                        'for the alpha2 `' . $cc['alpha2'] . '`'
                    );
                    foreach ($mottoGr as $ln => $motto) {
                        $this->assertIsString(
                            $motto,
                            'The country property `mottos.' . $key . '` must have elements as string ' .
                            'for the alpha2 `' . $cc['alpha2'] . '`'
                        );
                        $this->assertNotEmpty(
                            trim(preg_replace('/\s+/u', '', $motto)),
                            'The country property `mottos.' . $key . '` cannot have elements as empty string ' .
                            'for the alpha2 `' . $cc['alpha2'] . '`'
                        );
                        self::$countriesData['ln'][] = $ln;
                    }
                }
            }


            $this->assertArrayHasKey(
                'currencies',
                $cc,
                'The property `currencies` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsArray(
                $cc['currencies'],
                'The property `currencies` is not an array ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertNotEmpty(
                $cc['currencies'],
                'The property `currencies` is empty ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertArrayHasKey(
                'legalTenders',
                $cc['currencies'],
                'The property `currencies.legalTenders` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsArray(
                $cc['currencies']['legalTenders'],
                'The property `currencies.legalTenders` is not an array ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $curInTender = [];
            if (!empty($cc['currencies']['legalTenders'])) {
                foreach ($cc['currencies']['legalTenders'] as $cur) {
                    $curInTender[] = $cur;
                    $this->assertContains(
                        $cur,
                        self::$countriesData['currencies'],
                        'The property `currencies.legalTenders` with value `'
                        . $cur .
                        '` for the alpha2 `' . $cc['alpha2'] . '` ' .
                        'is not in the list of the ISO currencies'
                    );
                }
            }
            $this->assertArrayHasKey(
                'widelyAccepted',
                $cc['currencies'],
                'The property `currencies.widelyAccepted` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsArray(
                $cc['currencies']['widelyAccepted'],
                'The property `currencies.widelyAccepted` is not an array ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            if (!empty($cc['currencies']['widelyAccepted'])) {
                foreach ($cc['currencies']['widelyAccepted'] as $cur) {
                    $this->assertNotContains(
                        $cur,
                        $curInTender,
                        'The property `currencies.widelyAccepted` with value `'
                        . $cur .
                        '` already exits in `currencies.legalTenders` for the alpha2 `' . $cc['alpha2'] . '`'
                    );
                    $this->assertContains(
                        $cur,
                        self::$countriesData['currencies'],
                        'The property `currencies.widelyAccepted` with value `'
                        . $cur .
                        '` for the alpha2 `' . $cc['alpha2'] . '` ' .
                        'is not in the list of the ISO currencies'
                    );
                }
            }


            $this->assertArrayHasKey(
                'dialCodes',
                $cc,
                'The property `dialCodes` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsArray(
                $cc['dialCodes'],
                'The property `dialCodes` is not an array ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertNotEmpty(
                $cc['dialCodes'],
                'The property `dialCodes` is empty ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertArrayHasKey(
                'main',
                $cc['dialCodes'],
                'The property `dialCodes.main` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsArray(
                $cc['dialCodes']['main'],
                'The property `dialCodes.main` is not an array ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            foreach ($cc['dialCodes']['main'] as $dial) {
                $this->assertMatchesRegularExpression(
                    '/^\+\d+$/',
                    $dial,
                    'The property `dialCodes.main`=' . $dial . ' has wrong format ' .
                    'for the alpha2 `' . $cc['alpha2'] . '`'
                );
            }
            $this->assertArrayHasKey(
                'exceptions',
                $cc['dialCodes'],
                'The property `dialCodes.exceptions` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsArray(
                $cc['dialCodes']['exceptions'],
                'The property `dialCodes.exceptions` is not an array ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            foreach ($cc['dialCodes']['exceptions'] as $dial) {
                $this->assertMatchesRegularExpression(
                    '/^\+\d+$/',
                    $dial,
                    'The property `dialCodes.exceptions`=' . $dial . ' has wrong format ' .
                    'for the alpha2 `' . $cc['alpha2'] . '`'
                );
            }

            $this->assertArrayHasKey(
                'ccTld',
                $cc,
                'The property `ccTld` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $is_string = is_string($cc['ccTld']);
            $this->assertTrue(
                $is_string || is_null($cc['ccTld']),
                'The country property `ccTld` ' .
                ' for the alpha2 `' . $cc['alpha2'] . '` must be "string" or "null" ' .
                '(`' . gettype($cc['ccTld']) . '` returned)'
            );
            if ($is_string) {
                $this->assertNotEmpty(
                    trim(preg_replace('/\s+/u', '', $cc['ccTld'])),
                    'The country property `ccTld` cannot be an empty string ' .
                    'for the alpha2 `' . $cc['alpha2'] . '`'
                );
            }

            $this->assertArrayHasKey(
                'timeZones',
                $cc,
                'The property `timeZones` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsArray(
                $cc['timeZones'],
                'The property `timeZones` is not an array ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertNotEmpty(
                $cc['timeZones'],
                'The property `timeZones` is empty ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );

            $this->assertArrayHasKey(
                'locales',
                $cc,
                'The property `locales` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsArray(
                $cc['locales'],
                'The property `locales` is not an array ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertNotEmpty(
                $cc['locales'],
                'The property `locales` is empty ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $locs = [];
            foreach ($cc['locales'] as $loc) {
                $this->assertNotContains(
                    $loc,
                    $locs,
                    'The property `locales` with value `'
                    . $loc .
                    '` already exits (duplicated) in `locales` for the alpha2 `' . $cc['alpha2'] . '`'
                );
//                $this->assertContains(  // [TODO] DA METTERE ANCHE LA LISTA DEI LOCALES
//                    $loc,
//                    self::$countriesData['currencies'],
//                    'The property `currencies.widelyAccepted` with value `'
//                    . $loc .
//                    '` for the alpha2 `' . $cc['alpha2'] . '` ' .
//                    'is not in the list of the ISO currencies'
//                );

                self::$countriesData['ln'][] = $loc;
                $locs[] = $loc;
            }

            $this->assertArrayHasKey(
                'otherAppsIds',
                $cc,
                'The property `otherAppsIds` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertIsArray(
                $cc['otherAppsIds'],
                'The property `otherAppsIds` is not an array ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertNotEmpty(
                $cc['otherAppsIds'],
                'The property `otherAppsIds` is empty ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $this->assertArrayHasKey(
                'geoNamesOrg',
                $cc['otherAppsIds'],
                'The property `otherAppsIds.geoNamesOrg` is not present inside the `countries` data ' .
                'for the alpha2 `' . $cc['alpha2'] . '`'
            );
            $is_int = is_int($cc['otherAppsIds']['geoNamesOrg']);
            $this->assertTrue(
                $is_int || is_null($cc['otherAppsIds']['geoNamesOrg']),
                'The country property `otherAppsIds.geoNamesOrg` ' .
                ' for the alpha2 `' . $cc['alpha2'] . '` must be "string" or "null" ' .
                '(`' . gettype($cc['otherAppsIds']['geoNamesOrg']) . '` returned)'
            );
            if ($is_int) {
                $this->assertTrue(
                    $cc['otherAppsIds']['geoNamesOrg'] > 0,
                    'The country property `otherAppsIds.geoNamesOrg` cannot be zero ' .
                    'for the alpha2 `' . $cc['alpha2'] . '`'
                );
            }


            // [TODO] LANGUAGES
        }


        self::$countriesData['ln'] = array_values(array_unique(self::$countriesData['ln']));
    }


    /** @test
     * @depends testValidationCountryData
     * @return void
     */
    public function testValidationTranslationsFiles(): void
    {
        $countries = [];
        $currencies = [];
        $geosets = [];
        $languages = [];
        foreach (self::$Config['settings']['languages']['inPackage'] as $lang => $locale) {
            $transDir = self::$dataDir . '/Translations/' . $lang . '/';
            self::$countriesData['translations']['countries'][$lang] = [
                'name' => [],
                'fullName' => [],
            ];
            self::$countriesData['translations']['geosets'][$lang] = [
                'name' => []
            ];
            self::$countriesData['translations']['currencies'][$lang] = [
                'name' => []
            ];

            foreach (array_keys(self::$countriesData['translations']) as $item) {
                $this->assertFileExists(
                    $transDir . $item . '.php',
                    'The translation ' . $item . ' file is missing'
                );
                ${$item} = require_once($transDir . $item . '.php');
            }

            if ($lang == self::$Config['settings']['languages']['default']) {
                foreach (self::$countriesData['alpha2'] as $cc) {
                    $this->assertArrayHasKey(
                        $cc,
                        $countries,
                        'The country code `' . $cc . '`does not exist in `translations.' .
                        $lang . '.countries` dataset'
                    );
                    $this->assertIsArray(
                        /** @phpstan-ignore-next-line */
                        $countries[$cc],
                        'The country code `' . $cc . '`is not an array in `translations.' .
                        $lang . '.countries` dataset'
                    );
                    $this->assertNotEmpty(
                        $countries[$cc],
                        'The country code `' . $cc . '`is empty in `translations.' .
                        $lang . '.countries` dataset'
                    );
                    $this->assertArrayHasKey(
                        'name',
                        $countries[$cc],
                        'The country code `' . $cc . '`has not the property `name` in `translations.' .
                        $lang . '.countries` dataset'
                    );
                    $this->assertNotContains(
                        $countries[$cc]['name'],
                        self::$countriesData['translations']['countries'][$lang]['name'],
                        'The country property `name` as "' . $countries[$cc]['name'] .
                        '" for the translation `' . $lang .
                        '` is a duplicated key in `alpha2` "' . $cc . '"'
                    );
                    self::$countriesData['translations']['countries'][$lang]['name'][] = $countries[$cc]['name'];
                    $this->assertArrayHasKey(
                        'fullName',
                        $countries[$cc],
                        'The country code `' . $cc . '` has the missing property ' .
                        '`fullName` in `translations.' . $lang . '.countries` dataset'
                    );
                    $this->assertNotContains(
                        $countries[$cc]['fullName'],
                        self::$countriesData['translations']['countries'][$lang]['fullName'],
                        'The country property `fullName` as "' . $countries[$cc]['fullName'] .
                        '" for the translation `' . $lang .
                        '` is a duplicated key in `alpha2` "' . $cc . '"'
                    );
                    self::$countriesData['translations']['countries'][$lang]['fullName'][] =
                        $countries[$cc]['fullName'];
                    $this->assertArrayHasKey(
                        'demonyms',
                        $countries[$cc],
                        'The country code `' . $cc . '` has the missing property `demonyms` in `translations.' .
                        $lang . '.countries` dataset'
                    );
                    $this->assertArrayHasKey(
                        'keywords',
                        $countries[$cc],
                        'The country code `' . $cc . '` has the missing property `keywords` in `translations.' .
                        $lang . '.countries` dataset'
                    );


                    $this->assertIsString(
                        $countries[$cc]['name'],
                        'The country code `' . $cc . '` property `name` must be string in `translations.' .
                        $lang . '.countries` dataset'
                    );
                    $this->assertNotEmpty(
                        trim(preg_replace('/\s+/u', '', $countries[$cc]['name'])),
                        'The country code `' . $cc . '` has the property `name` as empty in `translations.' .
                        $lang . '.countries` dataset'
                    );

                    $this->assertIsString(
                        $countries[$cc]['fullName'],
                        'The country code `' . $cc . '` property `fullName` must be string ' .
                        'in `translations.' . $lang . '.countries` dataset'
                    );
                    $this->assertNotEmpty(
                        trim(preg_replace('/\s+/u', '', $countries[$cc]['fullName'])),
                        'The country code `' . $cc . '` has the property `fullName` as ' .
                        'empty in `translations.' . $lang . '.countries` dataset'
                    );

                    $this->assertIsArray(
                        $countries[$cc]['demonyms'],
                        'The country code `' . $cc . '` property `demonyms` must be array ' .
                        'in `translations.' . $lang . '.countries` dataset'
                    );

                    $this->assertIsArray(
                        $countries[$cc]['keywords'],
                        'The country code `' . $cc . '` property `keywords` must be array ' .
                        'in `translations.' . $lang . '.countries` dataset'
                    );
                }

                foreach (self::$countriesData['currencies'] as $cur) {
                    $this->assertArrayHasKey(
                        $cur,
                        $currencies,
                        'The currency code `' . $cur . '`does not exist in `translations.' .
                        $lang . '.currencies` dataset'
                    );
                    /** @phpstan-ignore-next-line */
                    $currencyName = $currencies[$cur]['name'];
                    $this->assertIsString(
                        $currencyName,
                        'The currency code `' . $cur . '` must be string in `translations.' .
                        $lang . '.currencies` dataset'
                    );
                    $this->assertNotEmpty(
                        trim(preg_replace('/\s+/u', '', $currencyName)),
                        'The currency code `' . $cur . '` is empty in `translations.' .
                        $lang . '.currencies` dataset'
                    );
                    $this->assertNotContains(
                        $currencyName,
                        self::$countriesData['translations']['currencies'][$lang]['name'],
                        'The currencies property `name` as "' . $currencyName .
                        '" for the translation `' . $lang .
                        '` is a duplicated key in `isoAlpha` "' . $cur . '"'
                    );
                    self::$countriesData['translations']['currencies'][$lang]['name'][] = $currencyName;
                }


                foreach (self::$countriesData['setsInternal'] as $gs) {
                    $this->assertArrayHasKey(
                        $gs,
                        $geosets,
                        'The geoSets internal code `' . $gs . '`does not exist in `translations.' .
                        $lang . '.geoSets` dataset'
                    );
                    /** @phpstan-ignore-next-line */
                    $geoSetName = $geosets[$gs]['name'];
                    $this->assertIsString(
                        $geoSetName,
                        'The geoSets internal code `' . $gs . '` must be string in `translations.' .
                        $lang . '.geoSets` dataset'
                    );
                    $this->assertNotEmpty(
                        trim(preg_replace('/\s+/u', '', $geoSetName)),
                        'The geoSets internal code `' . $gs . '` is empty in `translations.' .
                        $lang . '.geoSets` dataset'
                    );
                    $this->assertNotContains(
                        $geoSetName,
                        self::$countriesData['translations']['geosets'][$lang]['name'],
                        'The geoSets property `name` as "' . $geoSetName .
                        '" for the translation `' . $lang .
                        '` is a duplicated key in `internalCode` "' . $gs . '"'
                    );
                    self::$countriesData['translations']['geosets'][$lang]['name'][] = $geoSetName;
                }


                foreach (self::$countriesData['ln'] as $ln) {
                    $ln = explode('-', $ln)[0];
                    $this->assertArrayHasKey(
                        $ln,
                        $languages,
                        'The language internal code `' . $ln . '`does not exist in `translations.' .
                        $lang . '.languages` dataset'
                    );
                    $this->assertIsString(
                        /** @phpstan-ignore-next-line */
                        $languages[$ln],
                        'The language internal code `' . $ln . '` must be string in `translations.' .
                        $lang . '.languages` dataset'
                    );
                    $this->assertNotEmpty(
                        trim(preg_replace('/\s+/u', '', $languages[$ln])),
                        'The language internal code `' . $ln . '` is empty in `translations.' .
                        $lang . '.languages` dataset'
                    );
                }
            }
        }
    }
}
