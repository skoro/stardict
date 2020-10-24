<?php declare(strict_types=1);

namespace StarDict\Index;

use Generator;

interface IndexDataHandler
{
    public function getDataOffsets(): Generator;
}