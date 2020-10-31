<?php declare(strict_types=1);

namespace StarDict\DictData\Sequences;

abstract class TypeSequence
{
    private string $value = '';

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function setRawValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function clear(): self
    {
        $this->value = '';
        return $this;
    }

    abstract public function getId(): string;
}