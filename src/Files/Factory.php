<?php declare(strict_types=1);

namespace StarDict\Files;

use RuntimeException;

class Factory
{
    /**
     * @return string[]
     */
    public function fileClasses(): array
    {
        return [
            InfoFile::class,
            IndexFile::class,
            DictFile::class,
            DZDictFile::class,
        ];
    }

    public function isDictFile(string $filename): bool
    {
        foreach ($this->fileClasses() as $class) {
            if ($class::isValidFilename($filename)) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function createFileFromFilename(string $filename): File
    {
        foreach ($this->fileClasses() as $class) {
            if ($class::isValidFilename($filename)) {
                return new $class($filename);
            }
        }
        throw new RuntimeException(sprintf('"%s" is not StarDict file.', $filename));
    }
}