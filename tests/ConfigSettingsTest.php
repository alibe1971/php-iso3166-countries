<?php

namespace Alibe\GeoCodes\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Alibe\GeoCodes\GeoCodes;

final class ConfigSettingsTest extends TestCase
{
    private static GeoCodes $geoCodes;

    public static function setUpBeforeClass(): void
    {
        self::$geoCodes = new GeoCodes();
    }


    /**
     * @test
     * @testdox Configuration: Verify that the available languages exist.
     */
    public function testAvailableLanguages()
    {

        $elenaMyfile = fopen("/Users/aliberati/ALIBE/test.log", "a") or die("Unable to open file!");
        fwrite($elenaMyfile, print_r(self::$geoCodes , true)."\n");
        fclose($elenaMyfile);

        $availableLanguages = self::$geoCodes->getAvailableLanguages();
        $this->assertIsArray($availableLanguages, 'The available language is not an array');
        $this->assertContains('en', $availableLanguages, 'The language `en` is not available');
        $this->assertContains('it', $availableLanguages, 'The language `it` is not available');
    }

    /**
     * @test
     * @testdox Configuration: Verify that the default language exists at the beginning of the instance.
     */
    public function testMainDefaultLanguage()
    {
        $this->assertIsString(self::$geoCodes->getDefaultLanguage(), 'The default language is not a string');
        $this->assertEquals('en', self::$geoCodes->getDefaultLanguage(), 'The default language is not `en`');
    }

    /**
     * @test
     * @testdox Configuration: Verify that the current language exists at the beginning of the instance.
     */
    public function testCurrentLanguage()
    {
        $this->assertIsString(self::$geoCodes->getLanguage(), 'The current language is not a string');
        $this->assertEquals('en', self::$geoCodes->getLanguage(), 'The current language is not `en`');
    }

    /**
     * @test
     * @testdox Configuration: Verify that setting default language with a not available parameter returns an exception.
     */
    public function testDefaultLanguageWithException()
    {
        $this->expectException(Exception::class);
        self::$geoCodes->setDefaultLanguage('zz');
    }

    /**
     * @test
     * @testdox Configuration: Verify that setting default language works with available parameter.
     * @throws Exception
     */
    public function testDefaultLanguageCorrectly()
    {
        self::$geoCodes->setDefaultLanguage('it');
        $this->assertEquals(
            'it',
            self::$geoCodes->getDefaultLanguage(),
            'The default language is not correctly set'
        );
        self::$geoCodes->setDefaultLanguage('en');
        $this->assertEquals(
            'en',
            self::$geoCodes->getDefaultLanguage(),
            'The default language is not correctly set'
        );
    }

    /**
     * @test
     * @testdox Configuration: Verify useLanguage with not available parameter is ignored and the default one is used.
     */
    public function testUseLanguageWithWrongParameter()
    {
        self::$geoCodes->useLanguage('zz');
        $this->assertEquals(
            self::$geoCodes->getDefaultLanguage(),
            self::$geoCodes->getLanguage(),
            'The current language is not correctly set'
        );
    }

    /**
     * @test
     * @testdox Configuration: Verify useLanguage with available parameter is correctly set.
     */
    public function testUseLanguageWithRightParameter()
    {
        self::$geoCodes->useLanguage('it');
        $this->assertEquals(
            'it',
            self::$geoCodes->getLanguage(),
            'The current language is not correctly set'
        );
    }
}
