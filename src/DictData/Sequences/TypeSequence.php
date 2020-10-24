<?php declare(strict_types=1);

namespace StarDict\DictData\Sequences;

abstract class TypeSequence
{
    /**
     * @return mixed
     */
    public function getValue()
    {
    }

    abstract public function getId(): string;
}