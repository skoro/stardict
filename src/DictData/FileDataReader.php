<?php declare(strict_types=1);

namespace StarDict\DictData;

use RuntimeException;
use StarDict\Index\DataOffsetItem;

class FileDataReader implements DataReader
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
    public function readFromOffset(DataOffsetItem $offset, array $sequences): array
    {
        if ($this->fhandle === NULL) {
            if (($this->fhandle = $this->openFile()) === FALSE) {
                throw new RuntimeException('Cannot open dict data file: ' . $this->filename);
            }
        }

        if ($this->seekFile($this->fhandle, $offset->getOffset()) !== 0) {
            throw new RuntimeException(sprintf('Seek offset %u is out of range.', $offset->getOffset()));
        }

        $buf = $this->readFile($this->fhandle, $offset->getLength());
        if ($buf === FALSE) {
            throw new RuntimeException(sprintf('Cannot read data chunk of %u from "%s".', $offset->getLength(), $this->filename));
        }
        if (strlen($buf) < $offset->getLength()) {
            throw new RuntimeException('Read buffer out of range.');
        }

        $chunks = [];

        foreach ($sequences as $sequence) {
            $chunk = $sequence->readChunk($buf);
            $buf = substr($buf, $chunk->getLength() + 1);
            $chunks[] = $chunk;
        }

        return $chunks;
    }

    /**
     * @return int Seek successful 0 or -1 on error.
     */
    protected function seekFile($handle, int $offset): int
    {
        return fseek($handle, $offset, SEEK_SET);
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
        return fopen($this->filename, 'r');
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}