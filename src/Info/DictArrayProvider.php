<?php declare(strict_types=1);

namespace StarDict\Info;

use InvalidArgumentException;
use StarDict\Dict;

class DictArrayProvider implements DictProvider
{
    private array $data;
    private string $keyValueSeparator;
    private ?Dict $dict;

    /**
     * @param array  $data              Each item is a line with key-value.
     * @param string $keyValueSeparator The separator between key and value.
     */
    public function __construct(array $data, string $keyValueSeparator = '=')
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Dict data cannot be empty.');
        }
        $this->data = $data;
        $this->keyValueSeparator = $keyValueSeparator;
        $this->dict = null;
    }

    public function getDict(): Dict
    {
        if (!$this->dict) {
            $this->dict = $this->createDict();
        }
        return $this->dict;
    }

    protected function createDict(): Dict
    {
        $values = [];
        foreach ($this->data as $row) {
            list ($key, $value) = explode($this->keyValueSeparator, $row, 2);
            $values[strtolower($key)] = trim($value);
        }
        return new Dict($values);
    }
}
