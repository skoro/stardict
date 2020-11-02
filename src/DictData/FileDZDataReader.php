<?php declare(strict_types=1);

namespace StarDict\DictData;

use StarDict\Files\DZDictFile;

class FileDZDataReader extends FileDataReader
{
    public function __construct(DZDictFile $file)
    {
        parent::__construct($file);
    }

    /**
     * @inheritdoc
     */
    protected function openFile()
    {
        return @gzopen($this->getFile()->getFilename(), 'r');
    }

    /**
     * @inheritdoc
     */
    protected function seekFile($handle, int $offset): bool
    {
        return gzseek($handle, $offset, SEEK_SET) === 0;
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