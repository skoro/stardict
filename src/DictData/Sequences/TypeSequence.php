<?php declare(strict_types=1);

namespace StarDict\DictData\Sequences;

use StarDict\DictData\Chunk;

abstract class TypeSequence
{
    /**
     * @return mixed
     */
    public function getValue()
    {
    }

    public function readChunk(string $buf): Chunk
    {
        return new Chunk(strlen($buf), $buf);
    }

    abstract public function getId(): string;
}