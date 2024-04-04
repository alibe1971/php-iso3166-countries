<?php

namespace Alibe\GeoCodes\Lib;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;
use Alibe\GeoCodes\Lib\DataObj\InstanceLanguage;
use Alibe\GeoCodes\Lib\Enums\DataSets\Access;
use Alibe\GeoCodes\Lib\Enums\DataSets\Index;
use Alibe\GeoCodes\Lib\Enums\DataSets\Source;
use Alibe\GeoCodes\Lib\Enums\Exceptions\QueryCodes;
use Alibe\GeoCodes\Lib\Exceptions\QueryException;

class Enquiries
{
    /**
     * @var InstanceLanguage
     */
    private InstanceLanguage $InstanceLanguage;

    /**
     * @var string
     */
    private string $currentLocale;

    /**
     * @var string
     */
    protected string $dataSetName;

    /**
     * @var array<string, mixed>
     */
    private array $dataSets = [];

    /**
     * @var string
     */
    private string $dataSetPrimaryKey;

    /**
     * @var array<string, mixed>
     */
    protected array $dataSetsStructure;

    /**
     * @var array<int|string, mixed>
     */
    private array $data;

    /**
     * @var array<string, mixed>
     */
    private array $query = [
        'index' => null,
        'fetchGroups' => [],
        'fetchSuperGroups' => [],
        'select' => [],
        'where' => [],
        'limit' => [
            'from' => 0,
            'to' => null
        ],
        'order' => [
            'property' => null,
            'type' => 'ASC'
        ]
    ];

    /**
     * @var int
     */
    private int $fetchIndex = 0;


    /**
     * @var string
     */
    protected string $instanceName;

    /**
     * @var string
     */
    protected string $singleItemInstanceName;


    public function __construct(InstanceLanguage $languages, string $currentLocale)
    {
        $this->InstanceLanguage = $languages;
        $this->currentLocale = $currentLocale;
        $this->dataSetPrimaryKey = $this->getPrimaryKey();
        if ($this->dataSetName == 'countries') {
            $this->dataSets['geoSets'] = (new CodesGeoSets($languages, $currentLocale))
                ->withIndex('internalCode')->get()->toArray();
            $this->dataSets['currencies'] = (new CodesCurrencies($languages, $currentLocale))
                ->withIndex('isoAlpha')->get()->toArray();
        }
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

    /**
     *
     */
    private function buildDataSet(): void
    {
        if (array_key_exists($this->dataSetName, $this->dataSets)) {
            return;
        }

        $this->dataSets[$this->dataSetName] = [];

        /** get the databases for the translations */
        $transCurrentLanguage = DataSets::$dataSets[Source::TRANSLATIONS][$this->InstanceLanguage->current]
            [$this->dataSetName];
        $transDefaultLanguage = DataSets::$dataSets[Source::TRANSLATIONS][$this->InstanceLanguage->current]
            [$this->dataSetName];

        /** parse the data*/
        $k = 0;
        foreach (DataSets::$dataSets[Source::DATA][$this->dataSetName] as $data) {
            $object = [];
            foreach ($this->dataSetsStructure as $prop => $structure) {
                /** get the value from the source */
                if ($structure['source'] === Source::DATA) {
                    if (preg_match('/\./', $prop)) {
                        list($prop0, $prop1) = explode('.', $prop);
                        $object[$prop0][$prop1] = $data[$prop0][$prop1];
                    } else {
                        $object[$prop] = $data[$prop];
                    }
                }
                if ($structure['source'] === Source::TRANSLATIONS) {
                    $object[$prop] = !$transCurrentLanguage[$data[$this->dataSetPrimaryKey]][$prop] ?
                        $transCurrentLanguage[$data[$this->dataSetPrimaryKey]][$prop] :
                        $transDefaultLanguage[$data[$this->dataSetPrimaryKey]][$prop];
                }
            }

            /** Case of Countries: build also the currencies */
            if ($this->dataSetName == 'countries') {
                foreach ($object['currencies'] as $typeCur => $currencies) {
                    if (!empty($currencies)) {
                        $newCurrenciesArray = [];
                        foreach ($currencies as $cur) {
                            $newCurrenciesArray[] = $this->dataSets['currencies'][$cur];
                        }
                        $object['currencies'][$typeCur] = $newCurrenciesArray;
                    }
                }
            }

            $this->dataSets[$this->dataSetName][$object[$this->dataSetPrimaryKey]] = $object;
            $k++;
        }
        $this->query['limit']['to'] = $k;
    }

    /**
     * @return array<string, mixed>
     */
    public function selectableFields(): array
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


    private function getPrimaryKey(): string
    {
        $primary = '';
        foreach ($this->dataSetsStructure as $property => $structure) {
            if ($structure['index'] === Index::PRIMARY) {
                $primary = $property;
                break;
            }
        }
        return $primary;
    }

    /**
     * @param int $from
     * @param int $numberOfItems
     * @return $this
     * @throws QueryException
     */
    public function limit(int $from, int $numberOfItems): Enquiries
    {
        if ($from < 0) {
            throw new QueryException(QueryCodes::LIMIT_FROM_LESS_THAN_ZERO);
        }
        if ($numberOfItems < 0) {
            throw new QueryException(QueryCodes::LIMIT_ROWS_NUMBER_LESS_THAN_ZERO);
        }

        $this->query['limit']['from'] = $from;
        $this->query['limit']['to'] = $numberOfItems;
        return $this;
    }

    /**
     * @param string $property
     * @param string $orderType
     * @return $this
     * @throws QueryException
     */
    public function orderBy(string $property, string $orderType = 'ASC'): Enquiries
    {
        if (!in_array($property, array_keys($this->getIndexes()))) {
            throw new QueryException(QueryCodes::PROPERTY_TO_ORDER_NOT_VALID);
        }
        $orderType = strtoupper($orderType);
        if (!in_array($orderType, ['ASC', 'DESC'])) {
            throw new QueryException(QueryCodes::ORDER_TYPE_NOT_VALID);
        }
        $this->query['order']['property'] = $property;
        $this->query['order']['type'] = $orderType;
        return $this;
    }

    /**
     * @param string $index
     * @return $this
     * @throws QueryException
     */
    public function withIndex(string $index): Enquiries
    {
        if (!in_array($index, array_keys($this->getIndexes()))) {
            throw new QueryException(QueryCodes::FIELD_NOT_INDEXABLE);
        }
        $this->query['index'] = $index;
        return $this;
    }

    /**
     * @param string ...$select
     * @return $this
     * @throws QueryException
     */
    public function select(string ...$select): Enquiries
    {
        foreach ($select as $element) {
            if (!in_array($element, array_keys($this->selectableFields()))) {
                throw new QueryException(QueryCodes::FIELD_NOT_SELECTABLE);
            }
            $element = trim($element);
            $this->query['select'][] = $element;
        }
        return $this;
    }


    /**
     * @param string $search
     * @param string $db
     * @param string $prop
     */
    private function executeFetches(string $search, string $db, string $prop): bool
    {
        $toGet = $this->dataSetPrimaryKey;
        if ($this->dataSetName == 'countries' && $db == 'geoSets') {
            $toGet = 'countryCodes';
        }

        if (!array_key_exists($this->fetchIndex, $this->query['fetchGroups'])) {
            $this->query['fetchGroups'][$this->fetchIndex] = [];
        }

        foreach ($this->dataSets[$db] as $item) {
            if (isset($item[$prop]) && $item[$prop] === $search) {
                if (is_array($item[$toGet])) {
                    $this->query['fetchGroups'][$this->fetchIndex] =
                        array_merge($this->query['fetchGroups'][$this->fetchIndex], $item[$toGet]);
                } else {
                    $this->query['fetchGroups'][$this->fetchIndex][] = $item[$toGet];
                }
                return true;
            }
        }
        return false;
    }

    /**
     * @return $this
     */
    public function fetchAll(): Enquiries
    {
        $this->query['fetchGroups'][$this->fetchIndex] = array_keys($this->dataSets[$this->dataSetName]);
        $this->fetchIndex++;
        return $this;
    }

    /**
     * @param string|int|array<string|int|array<string|int>> ...$items
     * @return Enquiries
     * @throws QueryException
     */
    public function fetch(...$items): Enquiries
    {
        $list = [];
        foreach ($items as $item) {
            if (is_array($item)) {
                foreach ($item as $it) {
                    if (is_array($it)) {
                        throw new QueryException(QueryCodes::FETCH_MULTIDIM_ARRAY_NOT_ALLOWED);
                    }
                    $it = trim((string) $it);
                    if ($it == '*') {
                        return $this->fetchAll();
                    }
                    if (preg_match('/\s/', $it)) {
                        continue;
                    }
                    $list[$it] = 1;
                }
                continue;
            }
            $item = trim((string) $item);
            if ($item == '*') {
                return $this->fetchAll();
            }
            if (preg_match('/\s/', $item)) {
                continue;
            }
            $list[$item] = 1;
        }
        foreach ($list as $item => $val) {
            $prop = 'string';
            $lenght = strlen($item);
            if ($lenght <= 3 && intval($item) != 0) {
                $prop = 'numeric';
                if ($lenght < 3) {
                    $item = str_pad($item, 3, '0', STR_PAD_LEFT);
                    $lenght = 3;
                }
            } else {
                $item = strtoupper($item);
            }
            if ($lenght < 2) {
                continue;
            }
            $Enquiry = [];
            switch ($this->dataSetName) {
                case 'countries':
                    if ($prop == 'numeric') {
                        $Enquiry = [
                            'countries' => 'unM49',
                            'geoSets' => 'unM49'
                        ];
                    } else {
                        switch ($lenght) {
                            case 2:
                                $Enquiry = [
                                    'countries' => 'alpha2'
                                ];
                                break;
                            case 3:
                                $Enquiry = [
                                    'countries' => 'alpha3'
                                ];
                                break;
                            default:
                                $prop = 'tags';
                                if (preg_match('/-/', $item)) {
                                    $prop = 'internalCode';
                                }
                                $Enquiry = [
                                    'geoSets' => $prop
                                ];
                        }
                    }
                    break;
                case 'geoSets':
                    if ($lenght < 3) {
                        break;
                    }
                    if ($prop == 'numeric') {
                        $Enquiry = [
                            'geoSets' => 'unM49'
                        ];
                    } else {
                        $prop = 'tags';
                        if (preg_match('/-/', $item)) {
                            $prop = 'internalCode';
                        }
                        $Enquiry = [
                            'geoSets' => $prop
                        ];
                    }
                    break;
                case 'currencies':
                    if ($lenght < 3) {
                        break;
                    }
                    $prop = $prop == 'numeric' ? 'isoNumber' : 'isoAlpha';
                    $Enquiry = [
                        'currencies' => $prop
                    ];
                    break;
                default:
                    break;
            }

            foreach ($Enquiry as $db => $prop) {
                if ($this->executeFetches($item, $db, $prop)) {
                    break;
                }
            }
        }
        $this->fetchIndex++;

        return $this;
    }

    /**
     * Execution of the queries
     */
    private function execQueries(): void
    {
        /** The return data */
        $this->data = [];

        /** check the limit `to` */
        if (!is_null($this->query['limit']['to']) && $this->query['limit']['to'] <= 0) {
            return;
        }

        /** get all the fields if they are not defined */
        if (empty($this->query['select'])) {
            $this->query['select'] = array_filter(
                array_map(function ($val, $key) {
                    return ($val['access'] === Access::PUBLIC) ? $key : null;
                }, $this->dataSetsStructure, array_keys($this->dataSetsStructure))
            );
        }

        /** The executive dataset */
        $catchSets = array_merge(
            ...array_values($this->query['fetchGroups']),
            ...array_values($this->query['fetchSuperGroups'])
        );
        if (empty($catchSets)) {
            $executiveSet = $this->dataSets[$this->dataSetName];
        } else {
            $executiveSet = [];
            foreach ($catchSets as $index) {
                $executiveSet[$index] = $this->dataSets[$this->dataSetName][$index];
            }
        }

        /** Execute the orderBy */
        if (!is_null($this->query['order']['property'])) {
            $property = $this->query['order']['property'];
            $order = strtoupper($this->query['order']['type'] ?? 'ASC');
            $collator = collator_create($this->currentLocale . '.utf8');
            usort($executiveSet, function ($a, $b) use ($collator, $property, $order) {
                $result = collator_compare($collator, $a[$property], $b[$property]);
                if ($result === false) {
                    return 0;
                }
                return ($order === 'ASC') ? $result : -$result;
            });
        }

        /** parse the data */
        $k = $key = $keyOut = 0;
        foreach ($executiveSet as $data) {
            /** apply the limits */
            if ($key < $this->query['limit']['from']) {
                $key++;
                continue;
            }
            $k++;
            if ($k > $this->query['limit']['to']) {
                return;
            }

            $object = [];
            foreach ($this->dataSetsStructure as $prop => $structure) {
                /** get only the requested property */
                if (!in_array($prop, $this->query['select'])) {
                    continue;
                }
                if (preg_match('/\./', $prop)) {
                    list($prop0, $prop1) = explode('.', $prop);
                    if (!array_key_exists($prop0, $object)) {
                        $object[$prop0] = [];
                    }
                    $object[$prop0][$prop1] = $data[$prop0][$prop1];
                } else {
                    $object[$prop] = $data[$prop];
                }
            }
            /** build the index */
            $this->data[($this->query['index'] ? $data[$this->query['index']] : $keyOut)] = $object;
            $key++;
            $keyOut++;
        }
    }

    /**
     * Execute the enquiries and get the result
     *
     * @return BaseDataObj
     */
    public function get(): BaseDataObj
    {
        $this->execQueries();
        /** @var BaseDataObj $childInstance */
        $childInstance = new $this->instanceName();
        return $childInstance->from($this->data);
    }


    /**
     * Get the first element of the result
     *
     * @return BaseDataObj
     * @throws QueryException
     */
    public function first(): BaseDataObj
    {
        $this->limit(0, 1);
        $this->execQueries();
        /** @var BaseDataObj $childInstance */
        $childInstance = new $this->singleItemInstanceName();
        return $childInstance->from(reset($this->data));
    }

    /**
     * @return int
     */
    public function count(): int
    {
        $this->execQueries();
        return count($this->data);
    }
}
