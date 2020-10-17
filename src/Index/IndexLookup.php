<?php declare(strict_types=1);

namespace StarDict\Index;

use Generator;

interface IndexLookup
{
    public function match(string $query): ?Offset;

    public function enumerate(): Generator;
}