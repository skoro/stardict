<?php declare(strict_types=1);

namespace StarDict\Files;

class DictFile extends File
{
    public static function extension(): string
    {
        return '.dict';
    }
}