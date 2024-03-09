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
    protected array $dataSets = [
        Source::DATA => [],
        Source::TRANSLATIONS => []
    ];

    /**
     * @var array<string, mixed>
     */
    protected array $dataSetsStructure;

    /**
     * @var array<string, mixed>
     */
    protected array $data;

    /**
     * @var array<string, mixed>
     */
    private array $query = [
        'index' => null,
        'select' => [],
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

        $this->getDataSetData(Source::DATA, $this->dataSetName);
        $this->getDataSetData(Source::TRANSLATIONS, $this->dataSetName);
    }

    /**
     * @param string $source
     * @param string $name
     */
    private function getDataSetData(string $source, string $name): void
    {
        if ($source === Source::DATA && empty($this->dataSets[$source][$name])) {
            $this->dataSets[$source][$name] = BaseCode::getData($name);
            return;
        }

        if (empty($this->dataSets[Source::TRANSLATIONS][$this->InstanceLanguage->default][$name])) {
            $dir = 'Translations/' . $this->InstanceLanguage->default . '/' . $name;
            $this->dataSets[Source::TRANSLATIONS][$this->InstanceLanguage->default][$name] = BaseCode::getData($dir);
        }

        if (empty($this->dataSets[Source::TRANSLATIONS][$this->InstanceLanguage->current][$name])) {
            $dir = 'Translations/' . $this->InstanceLanguage->current . '/' . $name;
            $this->dataSets[Source::TRANSLATIONS][$this->InstanceLanguage->current][$name] = BaseCode::getData($dir);
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

    /**
     * @param string ...$select
     * @return $this
     */
    public function select(string ...$select): Enquiries
    {
        $this->query['select'] = [];
        foreach ($select as $element) {
            $element = trim($element);
            $this->query['select'][] = $element;
        }
        return $this;
    }

    /**
     * Execute the enquiries and get the result
     *
     * @return object   the simple object instead a multiple instance is needed for php 7.4
     */
    public function get(): object
    {
        $this->data = [];



        foreach (
            array_slice(
                $this->dataSets[Source::DATA][$this->dataSetName],
                $this->query['limit']['from'] ?? 0,
                $this->query['limit']['to'] ?? count($this->dataSets[Source::DATA][$this->dataSetName])
            ) as $key => $val
        ) {
            $this->data[($this->query['index'] ? $val[$this->query['index']] : $key)] = $val;
        }

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
        $this->limit(0,1);
        foreach ($this->get() as $item) {
            return $item;
        }
        return (object) [];
    }
}
