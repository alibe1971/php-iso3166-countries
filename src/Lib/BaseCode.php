<?php

namespace Alibe\GeoCodes\Lib;

use Exception;
use stdClass;

class BaseCode
{
    /**
     * The main config array
     *
     * @var object
     */
    private object $config;

    /**
     * @var object
     */
    protected object $Language;


    /**
     * Get the data from the database.
     * @param $file
     * @return array
     */
    private function getData($file): array
    {
        return include(dirname(__DIR__) . '/Data/' . $file . '.php');
    }

    /**
     * Set the internal main settings
     */
    protected function setConfig(): void
    {
        $this->config = $this->arrayToObjRecursive($this->getData('config'));
    }

    /**
     * Get the available languages present in the package
     * @return array
     */
    public function getAvailableLanguages(): array
    {
        return $this->config->settings->languages->inPackage;
    }

    /**
     * Reset the configuration parameters.
     * It set all the parameters at the default state.
     *
     * @throws Exception
     */
    protected function reset(): void
    {
        $this->Language = new StdClass();
        $language = $this->config->settings->languages->default;
        $this->setDefaultLanguage($language);
        $this->useLanguage($language);
    }

    /**
     * Set the default language to use in case the chosen current language doesn't exist.
     *
     * @param string $language
     * @throws Exception
     */
    public function setDefaultLanguage(string $language): void
    {
        if (!in_array($language, $this->config->settings->languages->inPackage)) {
            throw new Exception('Invalid format. Use "array" or "object"');
        }
        $this->Language->default = $language;
    }

    public function getDefaultLanguage(): string
    {
        return $this->Language->default;
    }

    /**
     * Set the language to use in the instance.
     *
     * @param string $language
     */
    public function useLanguage(string $language): void
    {
        $this->Language->current = (!in_array($language, $this->config->settings->languages->inPackage)) ?
            $this->Language->default : $language;
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
     * Transform an array in an instance of a defined class
     *
     * @param array $array
     * @return stdClass
     */
    protected function arrayToObjRecursive(array $array): StdClass
    {
        $obj = new stdClass();
        foreach ($array as $key => $value) {
            if (is_array($value) && count(array_filter(array_keys($value), 'is_string')) > 0) {
                $obj->$key = $this->arrayToObjRecursive($value);
            } else {
                $obj->$key = $value;
            }
        }
        return $obj;
    }

    private function buildTranslation($lang)
    {
//        $dir = 'Translations/'.$lang.'/';
//
//        $this->translations[$lang] = [
//            'countries' => $this->getData($dir.'countries'),
//            'currencies' => $this->getData($dir.'currencies'),
//            'geoSets' => $this->getData($dir.'geoSets'),
//            'languages' => $this->getData($dir.'languages')
//        ];
//
//        if($this->superDefLang != $lang) {
//            $this->execParsingTranslation($lang);
//        }
    }

    /**
     * [TODO]
     * @param $lang
     */
    private function execParsingTranslation($lang)
    {
    }
}
