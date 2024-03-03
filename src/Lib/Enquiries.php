<?php

namespace Alibe\GeoCodes\Lib;

use Alibe\GeoCodes\Lib\DataObj\InstanceLanguage;
use Alibe\GeoCodes\Lib\DataObj\ConfigSettings;
use Alibe\GeoCodes\Lib\Enums\DataSets\Access;
use Alibe\GeoCodes\Lib\Enums\DataSets\Index;
use Alibe\GeoCodes\Lib\Enums\DataSets\Source;

class Enquiries
{
    private ConfigSettings $ConfigSettings;
    private InstanceLanguage $InstanceLanguage;

    protected string $dataSetName;

    protected array $dataSets = [
        Source::DATA => [],
        Source::TRANSLATIONS => []
    ];

    protected array $dataSetsStructure;


    protected array $data;

    private array $query = [
        'select' => [],
        'where' => [],
        'limit' => [
            'from' => null,
            'to' => null
        ]
    ];

    public function __construct(ConfigSettings $settings, InstanceLanguage $languages)
    {
        $this->ConfigSettings = $settings;
        $this->InstanceLanguage = $languages;
        $this->getDataSetData(Source::DATA, $this->dataSetName);
        $this->getDataSetData(Source::TRANSLATIONS, $this->dataSetName);
    }

    private function getDataSetData($source, $name)
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

    public function getSelectables(): array
    {
        $selectables = [];
        foreach ($this->dataSetsStructure as $property => $structure) {
            if ($structure['access'] ===  Access::PUBLIC) {
                $selectables[$property] =
                    '(' . ($structure['nullable'] === true ? '?' : '') . $structure['type'] . ') - ' .
                        $structure['description'] .
                        ($structure['source'] === Source::TRANSLATIONS ? ' (in the chosen language)' : '');
            }
        }
        return $selectables;
    }

    public function getIndexables(): array
    {
        $indexables = [];
        foreach ($this->dataSetsStructure as $property => $structure) {
            if ($structure['access'] ===  Access::PUBLIC && $structure['index'] !== Index::NOTINDEXABLE) {
                $indexables[$property] = 'Key usable in the `->withIndex(?string $key)` method' .
                    ($structure['index'] === Index::PRIMARY ? ' (default key)' : '');
            }
        }
        return $indexables;
    }


    public function limit(int $from, int $numberOfItems): Enquiries
    {
        $this->query['limit']['from'] = $from;
        $this->query['limit']['to'] = $numberOfItems;

        return $this;
    }


    public function select(string ...$select): Enquiries
    {
        $this->query['select'] = [];
        foreach ($select as $element) {
            $element = trim($element);
            $this->query['select'][] = $element;
        }
        return $this;
    }

    protected function dataGet()
    {
        $this->data = [];
        foreach (
            array_slice(
                $this->dataSets[Source::DATA][$this->dataSetName],
                $this->query['limit']['from'] ?? 0,
                $this->query['limit']['to'] ?? count($this->dataSets[Source::DATA][$this->dataSetName])
            ) as $key => $val
        ) {
            $this->data[$key] = $val;
        }
    }
}
