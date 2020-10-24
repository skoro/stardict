<?php declare(strict_types=1);

namespace StarDict\Index;

class DataOffsetItem
{
    private string $query;
    private int $offset;
    private int $length;

    public function __construct(string $query, int $offset, int $length)
    {
        $this->query = $query;
        $this->offset = $offset;
        $this->length = $length;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getLength(): int
    {
        return $this->length;
    }
}