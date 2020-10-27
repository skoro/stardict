<?php declare(strict_types=1);

namespace StarDict\DictData;

/**
 * Data chunk.
 */
class Chunk
{
    private string $data;

    public function __construct(string $data)
    {
        $this->data = $data;
    }

    public function getLength(): int
    {
        return strlen($this->data);
    }

    public function getData(): string
    {
        return $this->data;
    }
}