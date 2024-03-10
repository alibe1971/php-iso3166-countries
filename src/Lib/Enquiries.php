<?php

namespace Alibe\GeoCodes\Lib;

use Alibe\GeoCodes\Lib\DataObj\Countries;
use Alibe\GeoCodes\Lib\DataObj\InstanceLanguage;
use Alibe\GeoCodes\Lib\Enums\DataSets\Access;
use Alibe\GeoCodes\Lib\Enums\DataSets\Index;
use Alibe\GeoCodes\Lib\Enums\DataSets\Source;

class Enquiries
{
    /**
     * @var InstanceLanguage
     */
    private InstanceLanguage $InstanceLanguage;

    /**
     * @var string
     */
    protected string $dataSetName;

    /**
     * @var array<string, mixed>
     */
    protected array $dataSets = [];

    /**
     * @var array<string, mixed>
     */
    protected array $dataSetsStructure;

    /**
     * @var array<int|string, mixed>
     */
    protected array $data;

    /**
     * @var array<string, mixed>
     */
    private array $query = [
        'index' => null,
        'fields' => [],
        'where' => [],
        'limit' => [
            'from' => null,
            'to' => null
        ]
    ];

//    /**
//     * @var string
//     */
//    private string $extendedClass;


    /**
     * @var string
     */
    protected string $instanceName;

    public function __construct(InstanceLanguage $languages)
    {
        $this->InstanceLanguage = $languages;

//        $this->extendedClass = static::class;
//        $this->dataSetName = $this->extendedClass->dataSetName;

        $this->getDataSetData(Source::DATA);
        $this->getDataSetData(Source::TRANSLATIONS);

        $this->buildDataSet();
    }

    /**
     * @param string $source
     */
    private function getDataSetData(string $source): void
    {
        if ($source === Source::DATA && empty(DataSets::$dataSets[$source][$this->dataSetName])) {
            DataSets::$dataSets[$source][$this->dataSetName] = DataSets::getData($this->dataSetName);
            return;
        }

        if (empty(DataSets::$dataSets[Source::TRANSLATIONS][$this->InstanceLanguage->default][$this->dataSetName])) {
            $dir = 'Translations/' . $this->InstanceLanguage->default . '/' . $this->dataSetName;
            DataSets::$dataSets[Source::TRANSLATIONS][$this->InstanceLanguage->default][$this->dataSetName] =
                DataSets::getData($dir);
        }

        if (empty(DataSets::$dataSets[Source::TRANSLATIONS][$this->InstanceLanguage->current][$this->dataSetName])) {
            $dir = 'Translations/' . $this->InstanceLanguage->current . '/' . $this->dataSetName;
            DataSets::$dataSets[Source::TRANSLATIONS][$this->InstanceLanguage->current][$this->dataSetName] =
                DataSets::getData($dir);
        }
    }

    private function buildDataSet(): void
    {
        $this->dataSets[$this->dataSetName] = [];

        /** get the databases for the translations */
        $transCurrentLanguage = DataSets::$dataSets[Source::TRANSLATIONS][$this->InstanceLanguage->current]
        [$this->dataSetName];
        $transDefaultLanguage = DataSets::$dataSets[Source::TRANSLATIONS][$this->InstanceLanguage->current]
        [$this->dataSetName];

        /** parse the data*/
        foreach (DataSets::$dataSets[Source::DATA][$this->dataSetName] as $key => $data) {
            $object = [];
            foreach ($this->dataSetsStructure as $prop => $structure) {
                /** get the value from the source */
                if ($structure['source'] === Source::DATA) {
                    $object[$prop] = $data[$prop];
                }
                if ($structure['source'] === Source::TRANSLATIONS) {
                    $object[$prop] = !$transCurrentLanguage[$data['alpha2']][$prop] ?
                        $transCurrentLanguage[$data['alpha2']][$prop] :
                        $transDefaultLanguage[$data['alpha2']][$prop];
                }
            }

            $this->dataSets[$this->dataSetName][] = $object;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function getFields(): array
    {
        $fields = [];
        foreach ($this->dataSetsStructure as $property => $structure) {
            if ($structure['access'] ===  Access::PUBLIC) {
                $fields[$property] =
                    '[' . ($structure['nullable'] === true ? '?' : '') . $structure['type'] . '] - ' .
                        $structure['description'] .
                        ($structure['source'] === Source::TRANSLATIONS ? ' (in the chosen language)' : '');
            }
        }
        return $fields;
    }

    /**
     * @return array<string, mixed>
     */
    public function getIndexes(): array
    {
        $indexes = [];
        foreach ($this->dataSetsStructure as $property => $structure) {
            if ($structure['access'] ===  Access::PUBLIC && $structure['index'] !== Index::NOTINDEXABLE) {
                $indexes[$property] = 'Key usable in the `->withIndex(?string $index)` method' .
                    ($structure['index'] === Index::PRIMARY ? ' (default key)' : '');
            }
        }
        return $indexes;
    }

    /**
     * @param int $from
     * @param int $numberOfItems
     * @return $this
     */
    public function limit(int $from, int $numberOfItems): Enquiries
    {
        $this->query['limit']['from'] = $from;
        $this->query['limit']['to'] = $numberOfItems;
        return $this;
    }

    /**
     * @param string $index
     * @return $this
     */
    public function withIndex(string $index): Enquiries
    {
        $this->query['index'] = $index;
        return $this;
    }

//    /**
//     * @param string ...$select
//     * @return $this
//     */
//    public function select(string ...$select): Enquiries
//    {
//        $this->query['select'] = [];
//        foreach ($select as $element) {
//            $element = trim($element);
//            $this->query['select'][] = $element;
//        }
//        return $this;
//    }

    /**
     * Execute the enquiries and get the result
     *
     * @return object   the simple object instead a multiple instance is needed for php 7.4
     */
    public function get(): object
    {

        $this->data = [];

        /** get all the fields if they are not defined */
        if (empty($this->query['fields'])) {
            $this->query['fields'] = array_filter(
                array_map(function ($val, $key) {
                    return ($val['access'] === Access::PUBLIC) ? $key : null;
                }, $this->dataSetsStructure, array_keys($this->dataSetsStructure))
            );
        }

        /** parse the data*/
        foreach ($this->dataSets[$this->dataSetName] as $key => $data) {
            $object = [];
            foreach ($this->dataSetsStructure as $prop => $structure) {

                /** get only the requested fields */
//                if (!in_array($prop, $this->query['fields'])) {
//                    continue;
//                }
            }
            /** build the index */
            $this->data[($this->query['index'] ? $data[$this->query['index']] : $key)] = $data;
        }
//        foreach (DataSets::$dataSets[Source::DATA][$this->dataSetName] as $key => $data) {
//            $object = [];
//            foreach ($this->dataSetsStructure as $prop => $structure) {
//
//                /** get only the requested fields */
//                if (!in_array($prop, $this->query['fields'])) {
//                    continue;
//                }
//                /** get the value from the source */
//                if ($structure['source'] === Source::DATA) {
//                    $object[$prop] = $data[$prop];
//                }
//                if ($structure['source'] === Source::TRANSLATIONS) {
//                    $object[$prop] = !$transCurrentLanguage[$data['alpha2']][$prop] ?
//                        $transCurrentLanguage[$data['alpha2']][$prop] :
//                        $transDefaultLanguage[$data['alpha2']][$prop];
//                }
//            }
//            /** build the index */
//            $this->data[($this->query['index'] ? $object[$this->query['index']] : $key)] = $object;
//        }


        /** set the limits */
        if ($this->query['limit']['from'] && $this->query['limit']['to']) {
            $this->data = array_slice($this->data, $this->query['limit']['from'], $this->query['limit']['to']);
        }

//        foreach (
//            /** set the limits */
//            array_slice(
//                $this->data,
//                $this->query['limit']['from'] ?? 0,
//                $this->query['limit']['to'] ?? count(DataSets::$dataSets[Source::DATA][$this->dataSetName])
//            ) as $key => $data
//        ) {
//
//            /** build the index */
//            $this->data[($this->query['index'] ? $object[$this->query['index']] : $key)] = $object;
//        }

        /** @var Countries $childInstance */
        $childInstance = new $this->instanceName($this->InstanceLanguage);
        return $childInstance->from($this->data);
    }


    /**
     * Get the first element of the result
     *
     * @return object   the simple object instead a multiple instance is needed for php 7.4
     */
    public function first(): object
    {
        $this->limit(0, 1);
        $item = (array) $this->get();
        if (!empty($item)) {
            return (object) $item[0];
        }
        return (object) [];
    }
}
