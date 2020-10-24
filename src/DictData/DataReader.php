<?php declare(strict_types=1);

namespace StarDict\DictData;

use StarDict\DictData\Sequences\TypeSequence;
use StarDict\Index\DataOffsetItem;

interface DataReader
{
    /**
     * @param TypeSequence[] $sequences
     *
     * @return TypeSequence[]
     */
    public function readFromOffset(DataOffsetItem $offset, array $sequences): array;
}