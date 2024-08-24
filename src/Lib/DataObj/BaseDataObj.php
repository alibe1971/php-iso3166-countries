<?php

namespace Alibe\GeoCodes\Lib\DataObj;

use stdClass;
use IteratorAggregate;
use ArrayIterator;
use Symfony\Component\Yaml\Yaml;

/**
 * @implements IteratorAggregate<object>
 */
class BaseDataObj extends StdClass implements IteratorAggregate
{
    /**
     * @var ArrayIterator<int|string, mixed>
     */
    private ArrayIterator $iterator;

    /**
     * @return ArrayIterator<int|string, mixed>
     */
    public function getIterator(): ArrayIterator
    {
        return $this->iterator;
    }

    /**
     * @return ArrayIterator<int|string, mixed>
     */
    public function collect(): ArrayIterator
    {
        return $this->getIterator();
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        $jsonString = json_encode($this);

        if ($jsonString === false) {
            throw new \RuntimeException('Failed to encode object to JSON.');
        }

        return $jsonString;
    }

    /**
     * @return string
     */
    public function toYaml(): string
    {
        return Yaml::dump($this->toArray(),5, 4, Yaml::DUMP_OBJECT_AS_MAP);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return json_decode($this->toJson(), true);
    }

    /**
     * @return array<string, mixed>
     */
    public function toFlatten(string $separator = '.'): array
    {
        return $this->flattenArray($this->toArray(), '', $separator);
    }

    /**
     * Flattens a multidimensional array with keys in dot notation.
     *
     * @param array<string, mixed> $array The multidimensional array to flatten.
     * @param string $prefix Optional prefix for the keys in dot notation.
     * @param string $separator Separator to use between keys in dot notation.
     * @return array<string, mixed> The flattened array.
     */
    private function flattenArray(array $array, string $prefix, string $separator): array
    {
        $result = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $prefix . $key . $separator, $separator));
            } else {
                $result[$prefix . $key] = $value;
            }
        }
        return $result;
    }


    /**
     * @param array<int|string, mixed> $data
     * @return $this
     */
    public function from(array $data): BaseDataObj
    {
        $parser = method_exists($this, 'getObjectStructureParser') ? $this->getObjectStructureParser() : [];
        if (empty($parser)) {
            $class = get_called_class() ?: static::class;
            foreach ($data as $dataKey => $dataValue) {
                if (is_array($dataValue)) {
                    $this->{$dataKey} = (new $class())->from($dataValue);
                } else {
                    $this->{$dataKey} = $dataValue;
                }
            }
            return $this;
        }

        $this->iterator = new ArrayIterator();

        foreach ($parser as $parserKey => $parserValue) {
            if ($parserKey == '0' && is_array($parserValue) && class_exists($parserValue[0])) {
                foreach ($data as $dataKey => $dataValue) {
                    /** @phpstan-ignore-next-line */
                    $this->{$dataKey} = (new $parser[0][0]())->from($dataValue);
                    $this->iterator->append($this->{$dataKey});
                }
                return $this;
            }

            if (array_key_exists($parserKey, $data)) {
                switch (gettype($parserValue)) {
                    case 'string':
                        if (class_exists($parserValue)) {
                            /** @phpstan-ignore-next-line */
                            $this->{$parserKey} = (new $parserValue())->from($data[$parserKey]);
                        } else {
                            $this->{$parserKey} = $data[$parserKey];
                        }
                        break;
                    default:
                        throw new \Error('ELIBE');
                }
            }
        }
        return $this;
    }

    /**
     * @return array<string, mixed> | array<int, array<int, string>>
     */
    protected function getObjectStructureParser(): array
    {
        return [];
    }
}
