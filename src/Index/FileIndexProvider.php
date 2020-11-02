<?php declare(strict_types=1);

namespace StarDict\Index;

use RuntimeException;
use StarDict\Files\IndexFile;

class FileIndexProvider implements IndexProvider
{
    private IndexFile $file;
    private int $indexSize;
    private ?BinaryIndexHandler $handler;

    public function __construct(IndexFile $file, int $indexSize)
    {
        $this->file = $file;
        $this->indexSize = $indexSize;
        $this->handler = null;
    }

    public function getIndexDataHandler(): IndexDataHandler
    {
        if (!$this->handler) {
            $this->checkIndexSize();
            $this->handler = new BinaryIndexHandler($this->loadBinaryData());
        }
        return $this->handler;
    }

    protected function loadBinaryData(): string
    {
        if (($data = @file_get_contents($this->file->getFilename())) === FALSE) {
            throw new RuntimeException('Cannot read index data file: ' . $this->file);
        }
        return $data;
    }

    protected function checkIndexSize()
    {
        $fileSize = filesize($this->file->getFilename());
        if ($this->indexSize != $fileSize) {
            throw new RuntimeException('Invalid index file size.');
        }
    }
}