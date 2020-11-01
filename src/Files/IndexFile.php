<?php declare(strict_types=1);

namespace StarDict\Files;

class IndexFile extends File
{
    public static function extension(): string
    {
        return '.idx';
    }
}