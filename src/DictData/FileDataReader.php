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
        $this->fhandle = null;
    }

    public function __destruct()
    {
        if (is_resource($this->fhandle)) {
            fclose($this->fhandle);
        }
    }

    /**
     * @inheritdoc
     */
    public function readFromOffset(DataOffsetItem $offset, array $sequences): array
    {
        $handle = $this->getFileHandle();

        $chunks = [];

        $buf = $this->internalRead($handle, $offset->getOffset(), $offset->getLength());
        foreach ($sequences as $sequence) {
            $chunk = $sequence->readChunk($buf);
            $buf = substr($buf, $chunk->getLength());
            $chunks[] = $chunk;
        }

        return $chunks;
    }

    protected function internalRead($handle, int $offset, int $length)
    {
        if (fseek($handle, $offset, SEEK_SET) === -1) {
            throw new RuntimeException(sprintf('Seek offset %u is out of range.', $offset));
        }
        if (($buf = fread($handle, $length)) === FALSE) {
            throw new RuntimeException(sprintf('Cannot read data chunk of %u from "%s".', $length, $this->filename));
        }
        return $buf;
    }

    protected function getFileHandle()
    {
        if ($this->fhandle === NULL) {
            $this->fhandle = $this->openFile();
        }
        return $this->fhandle;
    }

    protected function openFile()
    {
        if (($fh = fopen($this->filename, 'r')) === FALSE) {
            throw new RuntimeException('Cannot open dict data file: ' . $this->filename);
        }
        return $fh;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}