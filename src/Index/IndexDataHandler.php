<?php declare(strict_types=1);

namespace StarDict\Index;

use Generator;

interface IndexDataHandler
{
    /**
     * @return Generator<DataOffsetItem>
     */
    public function getDataOffsets(): Generator;
}
