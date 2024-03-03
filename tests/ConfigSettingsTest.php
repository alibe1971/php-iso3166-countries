<?php

namespace Alibe\GeoCodes\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Alibe\GeoCodes\GeoCodes;

final class ConfigSettingsTest extends TestCase
{
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
     * @testdox Configuration: Verify that the available languages exist.
     * @return void
     */
    public function testAvailableLanguages(): void
    {
        $availableLanguages = self::$geoCodes->getAvailableLanguages();
        $this->assertIsArray($availableLanguages, 'The available language is not an array');
        $this->assertContains('en', $availableLanguages, 'The language `en` is not available');
        $this->assertContains('it', $availableLanguages, 'The language `it` is not available');
    }

    /**
     * @test
     * @testdox Configuration: Verify that the default language exists at the beginning of the instance.
     * @return void
     */
    public function testMainDefaultLanguage(): void
    {
        $this->assertIsString(self::$geoCodes->getDefaultLanguage(), 'The default language is not a string');
        $this->assertEquals('en', self::$geoCodes->getDefaultLanguage(), 'The default language is not `en`');
    }

    /**
     * @test
     * @testdox Configuration: Verify that the current language exists at the beginning of the instance.
     * @return void
     */
    public function testCurrentLanguage(): void
    {
        $this->assertIsString(self::$geoCodes->getLanguage(), 'The current language is not a string');
        $this->assertEquals('en', self::$geoCodes->getLanguage(), 'The current language is not `en`');
    }

    /**
     * @test
     * @testdox Configuration: Verify that setting default language with a not available parameter returns an exception.
     * @return void
     */
    public function testDefaultLanguageWithException(): void
    {
        $this->expectException(Exception::class);
        self::$geoCodes->setDefaultLanguage('zz');
    }

    /**
     * @test
     * @testdox Configuration: Verify that setting default language works with available parameter.
     * @throws Exception
     * @return void
     */
    public function testDefaultLanguageCorrectly(): void
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
     * @return void
     */
    public function testUseLanguageWithWrongParameter(): void
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
     * @return void
     */
    public function testUseLanguageWithRightParameter(): void
    {
        self::$geoCodes->useLanguage('it');
        $this->assertEquals(
            'it',
            self::$geoCodes->getLanguage(),
            'The current language is not correctly set'
        );
    }
}
