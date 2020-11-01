<?php declare(strict_types=1);

namespace StarDict\Files;

class InfoFile extends File
{
    public static function extension(): string
    {
        return '.ifo';
    }
}
