<?php

namespace Alibe\GeoCodes\Lib;

use Alibe\GeoCodes\Lib\DataObj\BaseDataObj;
use Alibe\GeoCodes\Lib\DataObj\InstanceLanguage;
use Alibe\GeoCodes\Lib\Enums\DataSets\Access;
use Alibe\GeoCodes\Lib\Enums\DataSets\Index;
use Alibe\GeoCodes\Lib\Enums\DataSets\Source;
use Alibe\GeoCodes\Lib\Enums\DataSets\Type;
use Alibe\GeoCodes\Lib\Enums\Exceptions\QueryCodes;
use Alibe\GeoCodes\Lib\Exceptions\QueryException;
use Alibe\GeoCodes\Lib\Exceptions\GeneralException;

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
     * @var array|string[]
     */
    private array $conditionsOperators = [
        '=',
        '!=',
        '<>',
        'IS NULL',
        'IS NOT NULL',
        '>',
        '>=',
        '<',
        '<=',
        'IN',
        'NOT IN',
        'LIKE',
        'NOT LIKE',
    ];

    /**
     * @var array<string, mixed>
     */
    private array $query = [
        'index' => null,
        'fetchGroups' => [],
        'fetchSuperGroups' => [],
        'select' => [],
        'conditionsSet' => [],
        'interval' => [
            'offSet' => 0,
            'limit' => null
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
    private string $cursor = 'fetchGroups';

    /**
     * @var string
     */
    protected string $instanceName;

    /**
     * @var string
     */
    protected string $singleItemInstanceName;

    /**
     * @param InstanceLanguage $languages
     * @param string $currentLocale
     * @throws QueryException
     */
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

        foreach ($this->InstanceLanguage->toArray() as $lang) {
            if (empty(DataSets::$dataSets[Source::TRANSLATIONS][$lang][$this->dataSetName])) {
                $dir = 'Translations/' . $lang . '/' . $this->dataSetName;
                DataSets::$dataSets[Source::TRANSLATIONS][$lang][$this->dataSetName] = DataSets::getData($dir);
            }
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
        $transDefaultLanguage = DataSets::$dataSets[Source::TRANSLATIONS][$this->InstanceLanguage->default]
            [$this->dataSetName];
        $transSuperDefaultLanguage = DataSets::$dataSets[Source::TRANSLATIONS][$this->InstanceLanguage->superDefault]
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
                    $object[$prop] = !empty($transCurrentLanguage[$data[$this->dataSetPrimaryKey]][$prop]) ?
                        $transCurrentLanguage[$data[$this->dataSetPrimaryKey]][$prop] :
                        (!empty($transDefaultLanguage[$data[$this->dataSetPrimaryKey]][$prop]) ?
                            $transDefaultLanguage[$data[$this->dataSetPrimaryKey]][$prop] :
                            $transSuperDefaultLanguage[$data[$this->dataSetPrimaryKey]][$prop]
                        );
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
        $this->query['interval']['limit'] = $k;
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

    /**
     * @return string
     */
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
     * @param int $offSet
     * @return $this
     * @throws QueryException
     */
    public function offset(int $offSet): Enquiries
    {
        if ($offSet < 0) {
            throw new QueryException(QueryCodes::OFFSET_ROWS_NUMBER_LESS_THAN_ZERO);
        }
        $this->query['interval']['offSet'] = $offSet;
        return $this;
    }

    /**
     * @param int $offSet
     * @return $this
     * @throws QueryException
     */
    public function skip(int $offSet): Enquiries
    {
        $this->offset($offSet);
        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     * @throws QueryException
     */
    public function limit(int $limit): Enquiries
    {
        if ($limit < 0) {
            throw new QueryException(QueryCodes::LIMIT_ROWS_NUMBER_LESS_THAN_ZERO);
        }
        $this->query['interval']['limit'] = $limit;
        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     * @throws QueryException
     */
    public function take(int $limit): Enquiries
    {
        $this->limit($limit);
        return $this;
    }

    /**
     * @param string $property
     * @param string $orderType
     * @return Enquiries
     * @throws QueryException
     */
    public function orderBy(string $property, string $orderType = 'ASC'): Enquiries
    {
        if (!in_array($property, array_keys($this->getIndexes()))) {
            throw new QueryException(QueryCodes::PROPERTY_TO_ORDER_NOT_VALID, [$property]);
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
     * @return Enquiries
     * @throws QueryException
     */
    public function withIndex(string $index): Enquiries
    {
        if (!in_array($index, array_keys($this->getIndexes()))) {
            throw new QueryException(QueryCodes::FIELD_NOT_INDEXABLE, [$index]);
        }
        $this->query['index'] = $index;
        return $this;
    }

    /**
     * @param string ...$select
     * @return Enquiries
     * @throws QueryException
     */
    public function select(string ...$select): Enquiries
    {
        foreach ($select as $element) {
            if (!in_array($element, array_keys($this->selectableFields()))) {
                throw new QueryException(QueryCodes::FIELD_NOT_SELECTABLE, [$element]);
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
     * @return bool
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
     * @param array<string> $fetch
     */
    private function saveFetch(array $fetch): void
    {
        if ($this->cursor == 'fetchSuperGroups') {
            $this->query['fetchSuperGroups'] = [];
        }
        $this->query['fetchSuperGroups'][] = $fetch;
        $this->query['fetchGroups'] = [];
        $this->fetchIndex = 0;
        $this->cursor = 'fetchGroups';
    }

    /**
     * @param string $op
     * @throws QueryException
     */
    private function checkCursorElements(string $op = 'merge'): void
    {
        if (empty($this->query['fetchGroups'])) {
            $this->cursor = 'fetchSuperGroups';
        }
        if ($op != 'merge' && count($this->query[$this->cursor]) < 2) {
            $exceptions = [
                'intersect'  => QueryCodes::INTERSECT_OPERATION_NOT_ALLOWED,
                'complement' => QueryCodes::COMPLEMENT_OPERATION_NOT_ALLOWED
            ];
            throw new QueryException($exceptions[$op]);
        }
    }

    /**
     * Execution of the queries
     */
    private function execQueries(): void
    {
        /** The return data */
        $this->data = [];

        /** check the interval `limit` */
        if (!is_null($this->query['interval']['limit']) && $this->query['interval']['limit'] <= 0) {
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
        $this->executeConditions();
        $executiveSet = [];
        if (!empty($this->query['fetchGroups']) || !empty($this->query['fetchSuperGroups'])) {
            $catchSets = array_merge(
                ...array_values($this->query['fetchGroups']),
                ...array_values($this->query['fetchSuperGroups'])
            );
            if (!empty($catchSets)) {
                foreach ($catchSets as $index) {
                    $executiveSet[$index] = $this->dataSets[$this->dataSetName][$index];
                }
            }
        } else {
            $executiveSet = $this->dataSets[$this->dataSetName];
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
            if ($key < $this->query['interval']['offSet']) {
                $key++;
                continue;
            }
            $k++;
            if ($k > $this->query['interval']['limit']) {
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
     * @return Enquiries
     */
    public function fetchAll(): Enquiries
    {
        $this->executeConditions();
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
        $this->executeConditions();
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
     * @return Enquiries
     * @throws QueryException
     */
    public function merge(): Enquiries
    {
        $this->checkCursorElements();
        $this->saveFetch(
            array_merge(
                ...array_values($this->query[$this->cursor]),
            )
        );
        return $this;
    }

    /**
     * @return Enquiries
     * @throws QueryException
     */
    public function intersect(): Enquiries
    {
        $this->checkCursorElements('intersect');
        $this->saveFetch(
            array_intersect(
                ...array_values($this->query[$this->cursor]),
            )
        );
        return $this;
    }

    /**
     * @return Enquiries
     * @throws QueryException
     */
    public function complement(): Enquiries
    {
        $this->checkCursorElements('complement');
        $intersect = array_intersect(
            ...array_values($this->query[$this->cursor]),
        );
        $simmetricComplement = [];
        foreach ($this->query[$this->cursor] as $subArray) {
            $diff = array_diff($subArray, $intersect);
            $simmetricComplement = array_merge($simmetricComplement, $diff);
        }
        $this->saveFetch($simmetricComplement);
        return $this;
    }

    /**
     * @param mixed ...$conditions
     * @return $this
     * @throws QueryException
     */
    public function where(...$conditions): Enquiries
    {
        $this->parseConditions($conditions, 'and');
        return $this;
    }

    /**
     * @param array<array<int|string>|int|string>|int|string ...$conditions
     * @return Enquiries
     * @throws QueryException
     */
    public function orWhere(...$conditions): Enquiries
    {
        $this->parseConditions($conditions, 'or');
        return $this;
    }

    /**
     * @param array<int|string, array<array<int|string>|int|string>|int|string>  $conditions
     * @param string $target
     * @throws QueryException
     */
    private function parseConditions(array $conditions, string $target): void
    {
        if (
            empty($conditions) ||
            count($conditions) > 3 ||
            (!is_array($conditions[0]) && !is_string($conditions[0])) ||
            (is_array($conditions[0]) && count($conditions) > 1)
        ) {
            throw new QueryException(QueryCodes::CONDITIONS_STRUCTURE_WRONG);
        }
        /** @var array<int|string, array<int|string>|int|string|null> $structure */
        $structure = [];
        // case ->where(['field', 'operator', 'term']) or ->where(['field', 'term'])
        if (is_array($conditions[0]) && !is_array($conditions[0][0])) {
            if (count($conditions) > 1) {
                throw new QueryException(QueryCodes::CONDITIONS_STRUCTURE_WRONG);
            }
            $structure[] = $conditions[0];
        } elseif (is_string($conditions[0])) {
            $structure[] = $conditions;
        } elseif (is_array($conditions[0]) && is_array($conditions[0][0])) {
            if (count($conditions) > 1) {
                throw new QueryException(QueryCodes::CONDITIONS_STRUCTURE_WRONG);
            }
            $structure = $conditions[0];
        } else {
            throw new QueryException(QueryCodes::CONDITIONS_STRUCTURE_WRONG);
        }

        foreach ($structure as $key => $struct) {
            if (!is_array($struct)) {
                throw new QueryException(QueryCodes::CONDITIONS_STRUCTURE_WRONG);
            }
            $count = count($struct);
            if (
                !in_array($count, [2, 3]) ||
                !is_string($struct[0]) ||
                is_array($struct[1])
            ) {
                throw new QueryException(QueryCodes::CONDITIONS_STRUCTURE_COMPONENTS_WRONG);
            }

            switch ($count) {
                case 2:
                    if (is_string($struct[1]) && in_array(strtoupper($struct[1]), ['IS NULL', 'IS NOT NULL'])) {
                        /** @phpstan-ignore-next-line   false positive */
                        $structure[$key][1] = strtoupper($struct[1]);
                        $structure[$key][2] = null;
                    } elseif ($struct[1] == '=') {
                        throw new QueryException(QueryCodes::CONDITIONS_STRUCTURE_COMPONENTS_WRONG);
                    } else {
                        /** @phpstan-ignore-next-line   false positive */
                        $structure[$key][2] = $struct[1];
                        $structure[$key][1] = '=';
                    }
                    break;
                case 3:
                    if (!is_string($struct[1]) || in_array(strtoupper($struct[1]), ['IS NULL', 'IS NOT NULL'])) {
                        throw new QueryException(QueryCodes::CONDITIONS_STRUCTURE_COMPONENTS_WRONG);
                    }
                    break;
            }
            /** @phpstan-ignore-next-line   false positive */
            $structure[$key][1] = strtoupper($structure[$key][1]);

            // Check the operator
            if (!in_array($structure[$key][1], $this->conditionsOperators, true)) {
                throw new QueryException(QueryCodes::CONDITIONS_WRONG_OPERATOR, [$structure[$key][1]]);
            }

            // Check the term
            if (in_array($structure[$key][1], ['IN', 'NOT IN']) && !is_array($structure[$key][2])) {
                throw new QueryException(QueryCodes::CONDITIONS_TERM_MUST_BE_ARRAY, [$structure[$key][1]]);
            }
            if (!in_array($structure[$key][1], ['IN', 'NOT IN']) && is_array($structure[$key][2])) {
                throw new QueryException(QueryCodes::CONDITIONS_TERM_MUST_BE_NOT_ARRAY, [$structure[$key][1]]);
            }

            // Check the field
            if (!is_string($structure[$key][0])) {
                throw new QueryException(QueryCodes::CONDITIONS_WRONG_FIELD_TYPE);
            }
            if (!array_key_exists($structure[$key][0], $this->dataSetsStructure)) {
                if (strpos($structure[$key][0], '.') !== false) {
                    $chkFieldArray = explode('.', $structure[$key][0]);
                    $chkField = '';
                    foreach ($chkFieldArray as $chk) {
                        $chkField .= $chk;
                        if (array_key_exists($chkField, $this->dataSetsStructure)) {
                            if ($chkField == $structure[$key][0]) {
                                break;
                            }
                            if (
                                $this->dataSetsStructure[$chkField]['type'] !== Type::ARRAY &&
                                $this->dataSetsStructure[$chkField]['type'] !== Type::OBJECT
                            ) {
                                throw new QueryException(QueryCodes::CONDITIONS_WRONG_FIELD, [$structure[$key][0]]);
                            }
                            $chkField .= '.';
                            continue;
                        }
                        throw new QueryException(QueryCodes::CONDITIONS_WRONG_FIELD, [$structure[$key][0]]);
                    }
                } else {
                    throw new QueryException(QueryCodes::CONDITIONS_WRONG_FIELD, [$structure[$key][0]]);
                }
            }
        }

        // Check the for empty fetches
        if (empty($this->query['fetchGroups']) && empty($this->query['fetchSuperGroups'])) {
            $this->fetchAll();
        }

        // Add the conditions
        if (!array_key_exists($target, $this->query['conditionsSet'])) {
            $this->query['conditionsSet'][$target] = [];
        }
        $this->query['conditionsSet'][$target] = array_merge($this->query['conditionsSet'][$target], $structure);
    }

    /**
     * Execute the where and/or orWhere conditions
     */
    private function executeConditions(): void
    {
        if (empty($this->query['conditionsSet'])) {
            return;
        }
        if (empty($this->query['fetchGroups'])) {
            if (empty($this->query['fetchSuperGroups'])) {
                return;
            }
            $set4Conditions = array_pop($this->query['fetchSuperGroups']);
            $cursor = 'fetchSuperGroups';
        } else {
            $set4Conditions = array_pop($this->query['fetchGroups']);
            $cursor = 'fetchGroups';
        }

        // Execute the conditions and Rebuild the set
        $this->query[$cursor][] = array_merge(
            $this->applyConditionsToSet($set4Conditions, 'and'),
            $this->applyConditionsToSet($set4Conditions, 'or')
        );

        // At the end clean the conditions
        $this->query['conditionsSet'] = [];
    }

    /**
     * @param array<string> $set
     * @param string $method
     * @return array<string>
     */
    private function applyConditionsToSet(array $set, string $method): array
    {
        $result = [];
        foreach ($set as $setItem) {
            $matches = 0;
            if (empty($this->query['conditionsSet']) || !array_key_exists($method, $this->query['conditionsSet'])) {
                continue;
            }
            foreach ($this->query['conditionsSet'][$method] as $condition) {
                list($prop, $op, $term) = $condition;
                // Search the property alibe
                if (preg_match('/\./', $prop)) {
                    $object = $this->dataSets[$this->dataSetName][$setItem];
                    $propPath = explode('.', $prop);
                    foreach ($propPath as $path) {
                        if (!array_key_exists($path, $object)) {
                            if ($method == 'and') {
                                break 2;
                            }
                            continue 2;
                        }
                        $object = $object[$path];
                    }
                    $propValue = $object;
                } else {
                    $propValue = $this->dataSets[$this->dataSetName][$setItem][$prop];
                }

                /** This is an exception for dealing with numeric terms */
                if (is_numeric($term) && in_array($prop, ['unM49', 'isoNumber'])) {
                    $term = str_pad((string) $term, 3, '0', STR_PAD_LEFT);
                }
                // Check if the condition is met
                if ($this->applyCondition($op, $term, $propValue)) {
                    if ($method == 'and') {
                        $matches++;
                    } else {
                        $result[] = $setItem;
                        break;
                    }
                }
            }
            if ($method == 'and' && $matches == count($this->query['conditionsSet'][$method])) {
                $result[] = $setItem;
            }
        }
        return $result;
    }

    /**
     * @param string $operator
     * @param array<string>|string|null $term
     * @param array<array<string>>|array<string>|string|null $value
     * @return bool
     * @throws QueryException
     */
    private function applyCondition(string $operator, $term, $value): bool
    {
        if (is_array($value)) {
            foreach ($value as $val) {
                if ($this->applyCondition($operator, $term, $val)) {
                    return true;
                }
            }
            return false;
        }
        switch ($operator) {
            case '=':
            case '!=':
            case '<>':
                if (is_null($value) || is_null($term)) {
                    return $value === $term;
                }
                /** @var string $term */
                $result = (bool) preg_match('/^' . preg_quote($term, '/') . '$/iu', $value);
                switch ($operator) {
                    case '=':
                        return $result;
                    case '!=':
                    case '<>':
                        return !$result;
                }
                /** no break - The code above terminates. */
            case 'IS NULL':
                return is_null($value);
            case 'IS NOT NULL':
                return !is_null($value);
            case '>':
            case '>=':
            case '<':
            case '<=':
                $collator = collator_create($this->currentLocale . '.utf8');
                /** @var string $term */ /** @var string $value */
                $result = collator_compare($collator, (string) $value, (string) $term);
                switch ($operator) {
                    case '>':
                        return $result > 0;
                    case '>=':
                        return $result >= 0;
                    case '<':
                        return $result < 0;
                    case '<=':
                        return $result <= 0;
                }
                /** no break - The code above terminates. */
            case 'IN':
                /** @var array $term */ /** @var string $value */
                return in_array($value, $term);
            case 'NOT IN':
                /** @var array $term */ /** @var string $value */
                return !in_array($value, $term);
            case 'LIKE':
            case 'NOT LIKE':
                /** @var string $term */ /** @var string $value */
                $getRegex = function ($string) {
                    $escaped = preg_replace('/%/', '', preg_quote($string, '/'));
                    // Add quantifiers to handle the position of the % discriminant
                    if (strpos($string, '%') === 0 && strrpos($string, '%') === (strlen($string) - 1)) {
                        return '/' . $escaped . '/iu';    // '%my value%'
                    } elseif (strpos($string, '%') === 0) {
                        return '/' . $escaped . '$/iu';   // '%my value'
                    } elseif (strrpos($string, '%') === (strlen($string) - 1)) {
                        return '/^' . $escaped . '/iu';   // 'my value%'
                    } else {
                        return '/^' . $escaped . '$/iu';  // 'my value'
                    }
                };
                $result = (bool) preg_match($getRegex($term), (string) $value);
                switch ($operator) {
                    case 'LIKE':
                        return $result;
                    case 'NOT LIKE':
                        return !$result;
                }
                /** no break - The code above terminates. */
            default:
                throw new QueryException(QueryCodes::CONDITIONS_WRONG_OPERATOR, [$operator]);
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
        $this->limit(1)->offset($this->query['interval']['offSet']);
        $this->execQueries();
        /** @var BaseDataObj $childInstance */
        $childInstance = new $this->singleItemInstanceName();
        $first = reset($this->data);
        if (!is_array($first)) {
            $first = [];
        }
        return $childInstance->from($first);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        $this->execQueries();
        return count($this->data);
    }

    /**
     * @return string
     * @throws GeneralException
     */
    public function getXsd(): string
    {
        /** @var BaseDataObj $childInstance */
        $childInstance = new $this->instanceName();
        return $childInstance->getXsd();
    }

    /**
     * @return string
     * @throws GeneralException
     */
    public function getXsdSingle(): string
    {
        /** @var BaseDataObj $childInstance */
        $childInstance = new $this->singleItemInstanceName();
        return $childInstance->getXsd();
    }
}
