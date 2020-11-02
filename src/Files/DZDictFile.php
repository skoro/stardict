<?php declare(strict_types=1);

namespace StarDict\Files;

class DZDictFile extends DictFile
{
    public static function extension(): string
    {
        return '.dict.dz';
    }
}