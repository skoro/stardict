<?php declare(strict_types=1);

namespace StarDict\DictData;

use StarDict\DictData\Sequences\TypeSequence;
use StarDict\Index\DataOffsetItem;

interface DataReader
{
    /**
     * @param DataOffsetItem $offset
     * @param TypeSequence[] $sequences
     *
     * @return Chunk[]
     */
    public function readFromOffset(DataOffsetItem $offset, array $sequences): array;
}