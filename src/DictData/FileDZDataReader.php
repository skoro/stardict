<?php declare(strict_types=1);

namespace StarDict\DictData;

class FileDZDataReader extends FileDataReader
{
    /**
     * @inheritdoc
     */
    protected function openFile()
    {
        return gzopen($this->getFilename(), 'r');
    }

    /**
     * @inheritdoc
     */
    protected function seekFile($handle, int $offset): int
    {
        return gzseek($handle, $offset, SEEK_SET);
    }

    /**
     * @inheritdoc
     */
    protected function readFile($handle, int $length)
    {
        return gzread($handle, $length);
    }

    /**
     * @inheritdoc
     */
    protected function closeFile($handle)
    {
        gzclose($handle);
    }
}