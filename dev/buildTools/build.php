<?php

namespace Alibe\GeoCodes\Dev\BuildTools;

//
 // [TODO] Languages
 //    - https://en.wikipedia.org/wiki/List_of_official_languages_by_country_and_territory
 //    - https://github.com/datasets/language-codes
 //    ------> https://github.com/sokil/php-isocodes-db-only
 //

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/Functions.php';

$func = new Functions();


/**
 * Configuration
 */
$Config = $func->getData($func->devDirectory . '/Origin/Config.json');
$func->build('config', $Config);


/**
 * Countries
 */
$Countries = $func->getData($func->devDirectory . '/Origin/Countries.json');
usort($Countries, function ($a, $b) {
    return strcmp($a['alpha2'], $b['alpha2']);
});
foreach ($Countries as $index => $arr) {
    $Countries[$index]['alpha2'] = strtoupper($arr['alpha2']);
    $Countries[$index]['alpha3'] = strtoupper($arr['alpha3']);
    $Countries[$index]['unM49'] = str_pad($arr['unM49'], 3, '0', STR_PAD_LEFT);
    $Countries[$index]['currencies']['legalTenders'] = array_map('strtoupper', $arr['currencies']['legalTenders']);
    $Countries[$index]['currencies']['widelyAccepted'] = array_map('strtoupper', $arr['currencies']['widelyAccepted']);
    unset($Countries[$index]['label (not included in the build)']);
    unset($Countries[$index]['NOTES (not included in the build)']);
}
$func->build('countries', $Countries);


/**
 * Currencies
 */
$Currencies = $func->getData($func->devDirectory . '/Origin/Currencies.json');
usort($Currencies, function ($a, $b) {
    return strcmp($a['isoAlpha'], $b['isoAlpha']);
});
foreach ($Currencies as $index => $arr) {
    $Currencies[$index]['isoNumber'] = str_pad($arr['isoNumber'], 3, '0', STR_PAD_LEFT);
    unset($Currencies[$index]['label (not included in the build)']);
    unset($Currencies[$index]['NOTES (not included in the build)']);
}
$func->build('currencies', $Currencies);


/**
 * Geo Sets
 */
$geoSets = $func->getData($func->devDirectory . '/Origin/geoSets.json');
usort($geoSets, function ($a, $b) {
    return strcmp($a['internalCode'], $b['internalCode']);
});
foreach ($geoSets as $index => $arr) {
    $geoSets[$index]['internalCode'] = strtoupper($arr['internalCode']);
    $geoSets[$index]['unM49'] = ($arr['unM49'] !== null) ? str_pad($arr['unM49'], 3, '0', STR_PAD_LEFT) : null;
    $geoSets[$index]['tags'] = array_map('strtoupper', $arr['tags']);
    $geoSets[$index]['countryCodes'] = array_map('strtoupper', $arr['countryCodes']);
    unset($geoSets[$index]['label (not included in the build)']);
    unset($geoSets[$index]['NOTES (not included in the build)']);
}
$func->build('geoSets', $geoSets);


/**
 * Translations
 */
foreach ($Config['settings']['languages']['inPackage'] as $lang => $locale) {
    $translationDir = 'Translations/' . $lang . '/';

    $names = $func->getData($func->devDirectory . '/Origin/' . $translationDir . '/ccNameCommon.json');
    ksort($names);

    $nameComplete = $func->getData($func->devDirectory . '/Origin/' . $translationDir . '/ccNameComplete.json');
    $demonyms = $func->getData($func->devDirectory . '/Origin/' . $translationDir . '/ccDemonyms.json');
    $alias = $func->getData($func->devDirectory . '/Origin/' . $translationDir . '/ccAcronymsAliasFormer.json');
    $adjectives = $func->getData($func->devDirectory . '/Origin/' . $translationDir . '/ccAdjectives.json');
    $typos = $func->getData($func->devDirectory . '/Origin/' . $translationDir . '/ccTypos.json');
    $others = $func->getData($func->devDirectory . '/Origin/' . $translationDir . '/ccOthers.json');

    $currencies = $func->getData($func->devDirectory . '/Origin/' . $translationDir . '/currencies.json');
    ksort($currencies);
    $geoSets = $func->getData($func->devDirectory . '/Origin/' . $translationDir . '/geoSets.json');
    ksort($geoSets);

    $languages = $func->getData($func->devDirectory . '/Origin/' . $translationDir . '/languages.json');
    ksort($languages);

    $countryTranslation = [];
    foreach ($names as $cc => $name) {
        $countryTranslation[$cc]['name'] = $name;
        $countryTranslation[$cc]['completeName'] = $nameComplete[$cc];
        $countryTranslation[$cc]['demonyms'] = $demonyms[$cc]['demonyms'];

        $countryTranslation[$cc]['keywords'] = array_values(array_unique(array_diff(
            array_filter(array_merge(
                array_merge(...array_map(function ($element) use ($func) {
                    /** @phpstan-ignore-next-line */
                     return str_word_count($func->slug($element), 1);
                }, $alias[$cc]['acronymsAliasFormer'])),
                array_merge(...array_map(function ($element) use ($func) {
                    /** @phpstan-ignore-next-line */
                     return str_word_count($func->slug($element), 1);
                }, $adjectives[$cc]['adjectives'])),
                array_merge(...array_map(function ($element) use ($func) {
                    /** @phpstan-ignore-next-line */
                     return str_word_count($func->slug($element), 1);
                }, $typos[$cc]['typos'])),
                array_merge(...array_map(function ($element) use ($func) {
                    /** @phpstan-ignore-next-line */
                     return str_word_count($func->slug($element), 1);
                }, $others[$cc]['others']))
            )),
            array_filter(array_merge(                               /** @phpstan-ignore-next-line */
                explode(' ', $func->slug($name)),                   /** @phpstan-ignore-next-line */
                explode(' ', $func->slug($nameComplete[$cc])),      /** @phpstan-ignore-next-line */
                explode(' ', $func->slug(implode(' ', $demonyms[$cc]['demonyms'])))
            ))
        )));
    }

    $func->build($translationDir . 'countries', $countryTranslation);
    $func->build($translationDir . 'currencies', $currencies);
    $func->build($translationDir . 'geoSets', $geoSets);
    $func->build($translationDir . 'languages', $languages);
}

$func->cleanStructure();
