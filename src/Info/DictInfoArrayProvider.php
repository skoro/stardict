<?php declare(strict_types=1);

namespace StarDict\Info;

use StarDict\Dict;

class DictInfoArrayProvider implements DictInfoProvider
{
    private array $data;
    private string $keyValueSeparator;

    /**
     * @param array  $data              Each item is a line with key-value.
     * @param string $keyValueSeparator The separator between key value.
     */
    public function __construct(array $data, string $keyValueSeparator = '=')
    {
        $this->data = $data;
        $this->keyValueSeparator = $keyValueSeparator;
    }

    public function createDict(): Dict
    {
        $values = [];
        foreach ($this->data as $row) {
            list ($key, $value) = explode($this->keyValueSeparator, $row, 2);
            $values[$key] = $value;
        }
        return new Dict($values);
    }
}

