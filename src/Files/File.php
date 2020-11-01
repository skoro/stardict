<?php declare(strict_types=1);

namespace StarDict\Files;

use InvalidArgumentException;

abstract class File
{
    private string $filename;

    public function __construct(string $filename)
    {
        if (!static::isValidFilename($filename)) {
            throw new InvalidArgumentException(sprintf('"%s" is not StarDict file.', $filename));
        }
        $this->filename = $filename;
    }

    public function __toString()
    {
        return $this->filename;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getExtension(): string
    {
        return static::extension();
    }

    abstract public static function extension(): string;

    public static function isValidFilename(string $filename): bool
    {
        $ext = static::extension();
        return substr($filename, -strlen($ext)) === $ext;
    }
}