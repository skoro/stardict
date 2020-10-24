<?php declare(strict_types=1);

namespace StarDict\Index;

use RuntimeException;

class FileIndexProvider implements IndexProvider
{
    private string $filename;
    private int $indexSize;
    private ?BinaryIndexHandler $handler;

    public function __construct(string $filename, int $indexSize)
    {
        $this->filename = $filename;
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
        if (($data = @file_get_contents($this->filename)) === FALSE) {
            throw new RuntimeException('Cannot read index data file: ' . $this->filename);
        }
        return $data;
    }

    protected function checkIndexSize()
    {
        $fileSize = filesize($this->filename);
        if ($this->indexSize != $fileSize) {
            throw new RuntimeException('Invalid index file size.');
        }
    }
}