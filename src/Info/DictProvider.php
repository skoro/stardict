<?php declare(strict_types=1);

namespace StarDict\Info;

use StarDict\Dict;

interface DictProvider
{
    public function getDict(): Dict;
}
