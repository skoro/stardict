<?php declare(strict_types=1);

namespace StarDict\DictData;

/**
 * Data chunk.
 */
class Chunk
{
    private int $length;
    private string $data;

    public function __construct(int $length, string $data)
    {
        $this->length = $length;
        $this->data = $data;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getData(): string
    {
        return $this->data;
    }
}