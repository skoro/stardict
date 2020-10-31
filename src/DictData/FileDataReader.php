<?php declare(strict_types=1);

namespace StarDict\DictData;

use RuntimeException;
use StarDict\Index\DataOffsetItem;

class FileDataReader extends DataReader
{
    private string $filename;
    private $fhandle;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
        $this->fhandle = NULL;
    }

    public function __destruct()
    {
        if (is_resource($this->fhandle)) {
            $this->closeFile($this->fhandle);
        }
    }

    protected function closeFile($handle)
    {
        fclose($handle);
    }

    /**
     * @inheritdoc
     */
    protected function readBuffer(DataOffsetItem $offset): string
    {
        if ($this->fhandle === NULL) {
            if (($this->fhandle = $this->openFile()) === FALSE) {
                throw new RuntimeException('Cannot open dict data file: ' . $this->filename);
            }
        }

        if ($this->seekFile($this->fhandle, $offset->getOffset()) === FALSE) {
            throw new RuntimeException(sprintf('Cannot seek to "%d".', $offset->getOffset()));
        }

        $buf = $this->readFile($this->fhandle, $offset->getLength());
        if ($buf === FALSE) {
            throw new RuntimeException(sprintf('Cannot read data chunk of %u from "%s".', $offset->getLength(), $this->filename));
        }
        if (strlen($buf) < $offset->getLength()) {
            throw new RuntimeException('Read buffer out of range.');
        }

        return $buf;
    }

    /**
     * @return bool
     */
    protected function seekFile($handle, int $offset): bool
    {
        return fseek($handle, $offset, SEEK_SET) === 0;
    }

    /**
     * @return string|FALSE
     */
    protected function readFile($handle, int $length)
    {
        return fread($handle, $length);
    }

    /**
     * @return resource|NULL
     */
    protected function getFileHandle()
    {
        return $this->fhandle;
    }

    /**
     * @return resource|FALSE
     */
    protected function openFile()
    {
        return @fopen($this->filename, 'r');
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}