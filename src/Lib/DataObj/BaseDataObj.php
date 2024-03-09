<?php

namespace Alibe\GeoCodes\Lib\DataObj;

use stdClass;

class BaseDataObj extends StdClass
{
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
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return json_decode($this->toJson(), true);
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

        foreach ($parser as $parserKey => $parserValue) {
            if ($parserKey == '0' && is_array($parserValue) && class_exists($parserValue[0])) {
                foreach ($data as $dataKey => $dataValue) {
                    /** @phpstan-ignore-next-line */
                    $this->{$dataKey} = (new $parser[0][0]())->from($dataValue);
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
                        throw new \Error('STICA');
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
