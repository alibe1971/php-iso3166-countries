<?php

namespace Alibe\GeoCodes\Lib;

use Alibe\GeoCodes\Lib\DataObj\InstanceLanguage;
use Alibe\GeoCodes\Lib\DataObj\ConfigSettings;
use Alibe\GeoCodes\Lib\Enums\Exceptions\ConfigCodes;
use Alibe\GeoCodes\Lib\Exceptions\ConfigException;

class BaseCode
{
    /**
     * The main config array
     *
     * @var ConfigSettings
     */
    private ConfigSettings $config;

    /**
     * @var InstanceLanguage
     */
    private InstanceLanguage $Language;


//    /**
//     * Get the data from the database.
//     * @param string $file
//     * @return array<string, mixed>
//     */
//    public static function getData(string $file): array
//    {
//        return include(dirname(__DIR__) . '/Data/' . $file . '.php');
//    }

    /**
     * Set the internal main settings
     */
    protected function setConfig(): void
    {
        $this->config = (new ConfigSettings())->from(DataSets::getData('config'));
    }

    /**
     * Get the internal main config
     *
     * @return ConfigSettings
     */
    protected function getConfig(): ConfigSettings
    {
        return $this->config;
    }

    /**
     * Get the available languages present in the package
     * @return array<string, mixed>
     */
    public function getAvailableLanguages(): array
    {
        return $this->config->settings->languages->inPackage->toArray();
    }

    /**
     * Get the instance config Languages
     *
     * @return InstanceLanguage
     */
    protected function getInstanceLanguage(): InstanceLanguage
    {
        return $this->Language;
    }

    /**
     * Reset the configuration parameters.
     * It set all the parameters at the default state.
     *
     * @throws ConfigException
     */
    protected function reset(): void
    {
        $this->Language = new InstanceLanguage();
        $language = $this->config->settings->languages->default;
        $this->setDefaultLanguage($language);
        $this->useLanguage($language);
    }

    /**
     * Set the default language to use in case the chosen current language doesn't exist.
     *
     * @param string $language
     * @return BaseCode
     * @throws ConfigException
     */
    public function setDefaultLanguage(string $language): BaseCode
    {
        if (!in_array($language, $this->config->settings->languages->inPackage->toArray())) {
            throw new ConfigException(ConfigCodes::LANGUAGE_NOT_AVAILABLE);
        }
        $this->Language->default = $language;
        return $this;
    }

    /**
     * Get the default language code
     *
     * @return string
     */
    public function getDefaultLanguage(): string
    {
        return $this->Language->default;
    }

    /**
     * Set the language to use in the instance.
     *
     * @param string $language
     * @return BaseCode
     */
    public function useLanguage(string $language): BaseCode
    {
        $this->Language->current = (!in_array($language, $this->config->settings->languages->inPackage->toArray())) ?
            $this->Language->default : $language;
        return $this;
    }

    /**
     * Get the current language used in the instance.
     *
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->Language->current;
    }


    /**
     * @return CodesCountries
     */
    public function countries(): CodesCountries
    {
        return new CodesCountries($this->getInstanceLanguage());
    }

    /**
     * @return CodesGeoSets
     */
    public function geoSets(): CodesGeoSets
    {
        return new CodesGeoSets($this->getInstanceLanguage());
    }

    /**
     * @return CodesCurrencies
     */
    public function geoCurrencies(): CodesCurrencies
    {
        return new CodesCurrencies($this->getInstanceLanguage());
    }
}
