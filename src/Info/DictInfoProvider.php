<?php declare(strict_types=1);

namespace StarDict\Info;

use StarDict\Dict;

interface DictInfoProvider
{
    public function getDict(): Dict;
}
