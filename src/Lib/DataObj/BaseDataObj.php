<?php

namespace Alibe\GeoCodes\Lib\DataObj;

use Alibe\GeoCodes\Lib\Enums\Exceptions\GeneralCodes;
use Alibe\GeoCodes\Lib\Exceptions\GeneralException;
use DOMDocument;
use DOMElement;
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
     * @var string
     */
    protected string $xmlRootElement;

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
        return Yaml::dump($this->toArray(), 5, 4, Yaml::DUMP_OBJECT_AS_MAP);
    }

    /**
     * @return string
     * @throws GeneralException
     */
    public function toXml(): string
    {
        return $this->execToXml();
    }

    /**
     * @return string
     * @throws GeneralException
     */
    public function toXmlAndValidate(): string
    {
        return $this->execToXml(true);
    }

    /**
     * @return string
     * @throws GeneralException
     */
    public function getXsd(): string
    {
        $xsd = file_get_contents($schemaPath = __DIR__ . '/Xsd/' . $this->xmlRootElement . '.xsd');
        if (!$xsd) {
            throw new GeneralException(GeneralCodes::INVALID_XSD);
        }
        return $xsd;
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
     * @param bool $withValidation
     * @return string
     * @throws GeneralException
     */
    private function execToXml(bool $withValidation = false): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $rootElement = $this->xmlRootElement;
        $root = $dom->createElement($rootElement);
        $dom->appendChild($root);
        $this->arrayToXml($this->toArray(), $root, $dom, $rootElement, $this->getXmlMap());
        $xmlString = $dom->saveXML();

        if (!is_string($xmlString)) {
            throw new GeneralException(GeneralCodes::INVALID_XML, [$rootElement, 'The XML is not a string']);
        }

        libxml_use_internal_errors(true);
        if (!$dom->loadXML($xmlString)) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            throw new GeneralException(GeneralCodes::INVALID_XML, [$rootElement, json_encode($errors)]);
        }

        if ($withValidation && !$this->validateXml($xmlString)) {
            throw new GeneralException(GeneralCodes::XML_FAILED_VALIDATION, [$rootElement]);
        }

        return $xmlString;
    }

    /**
     * @param array<string, array<string, array<string, string>|string>|array<string, string>|string|null> $data
     * @param DOMElement $element
     * @param DOMDocument $dom
     * @param string $rootElement
     * @param array<string, mixed> $map
     */
    private function arrayToXml(
        array $data,
        DOMElement $element,
        DOMDocument $dom,
        string $rootElement,
        array $map
    ): void {
        $tagKey = $attributeKey = $typeKey = $subElement = null;
        if (isset($map[$rootElement]) && is_array($map[$rootElement])) {
            if (array_key_exists('@tag', $map[$rootElement])) {
                $tagKey = $map[$rootElement]['@tag'];
            }
            if (array_key_exists('@attribute', $map[$rootElement])) {
                $attributeKey = $map[$rootElement]['@attribute'];
                if (!is_string($attributeKey)) {
                    $attributeKey = null;
                }
            }
            if (array_key_exists('@type', $map[$rootElement])) {
                $typeKey = $map[$rootElement]['@type'];
                if (!is_array($typeKey)) {
                    $typeKey = null;
                }
            }
        }

        foreach ($data as $key => $value) {
            $transformedKey = preg_replace('/[^a-zA-Z0-9_]/', '_', $tagKey ?? $key);
            if (is_string($transformedKey)) {
                if (is_array($value)) {
                    $subElement = $dom->createElement($transformedKey);
                    $element->appendChild($subElement);

                    $newRootElement = $transformedKey;
                    $newMap = isset($map[$rootElement]) && is_array($map[$rootElement]) ? $map[$rootElement] : [];
                    $this->arrayToXml($value, $subElement, $dom, $newRootElement, $newMap);
                } else {
                    if (isset($typeKey) && array_key_exists($transformedKey, $typeKey)) {
                        switch ($typeKey[$transformedKey]) {
                            case 'CDATA':
                                $subElement = $dom->createElement($transformedKey);
                                $cdata = $dom->createCDATASection($value);
                                $subElement->appendChild($cdata);
                                break;
                        }
                    } else {
                        if (is_null($value)) {
                            $subElement = $dom->createElement($transformedKey);
                            $subElement->setAttribute('xsi:nil', 'true');
                            $subElement->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
                        } else {
                            $subElement = $dom->createElement($transformedKey, $value);
                        }
                    }
                }

                if ($attributeKey) {
                    $subElement->setAttribute($attributeKey, $key);
                }
                $element->appendChild($subElement);
            }
        }
    }

    /**
     * @param string $xmlString
     * @return bool
     */
    private function validateXml(string $xmlString): bool
    {
        $dom = new DOMDocument();
        $dom->loadXML($xmlString);
        $schemaPath = __DIR__ . '/Xsd/' . $this->xmlRootElement . '.xsd';
        return $dom->schemaValidate($schemaPath);
    }

    /**
     * @return array<string, mixed> | array<int, array<int, string>>
     */
    protected function getObjectStructureParser(): array
    {
        return [];
    }

    /**
     * @return array<string, array<string, array<string, array<string, string>|string>>>
     */
    protected function getXmlMap(): array
    {
        return [];
    }
}
