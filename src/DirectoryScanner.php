<?php declare(strict_types=1);

namespace StarDict;

use Directory;
use RuntimeException;
use StarDict\Files\Factory;
use StarDict\Files\File;

/**
 * Scans directories for StarDict files.
 */
class DirectoryScanner
{
    private string $baseDir;
    private Factory $filesFactory;

    /**
     * @param string $baseDir The base directory from scan.
     */
    public function __construct(string $baseDir, Factory $filesFactory)
    {
        $this->baseDir = $baseDir;
        $this->filesFactory = $filesFactory;
    }

    /**
     * @return DictFiles[]
     */
    protected function internalScan(string $subDir = '', array $files = []): array
    {
        $dir = $this->initDir($subDir);
        $files = [];
        while (($filename = $dir->read()) !== FALSE) {
            if ($filename === '.' || $filename === '..') {
                continue;
            }
            $filepath = $subDir . DIRECTORY_SEPARATOR . $filename;
            if (is_dir($filepath)) {
                $files = array_merge($files, $this->internalScan($filepath, $files));
            } else {
                if ($this->filesFactory->isDictFile($filepath)) {
                    $files[] = $this->filesFactory->createFileFromFilename($filepath);
                }
            }
        }
        $dir->close();
        return $files;
    }

    protected function initDir(string $baseDir): Directory
    {
        $dir = dir($baseDir);
        if (! $dir instanceof Directory) {
            throw new RuntimeException('Cannot read directory: ' . $baseDir);
        }
        return $dir;
    }

    /**
     * @throws RuntimeException When scanning is failed.
     */
    public function scan(): array
    {
        return $this->groupFiles($this->internalScan($this->baseDir));
    }

    /**
     * @param File[] $files
     *
     * @return DictFiles[]
     */
    protected function groupFiles(array $files): array
    {
        $groups = [];
        foreach ($files as $file) {
            $dir = dirname($file->getFilename());
            $filename = basename($file->getFilename(), $file->getExtension());
            $id = $dir . DIRECTORY_SEPARATOR . $filename;
            if (!isset($groups[$id])) {
                $groups[$id] = new DictFiles();
            }
            $groups[$id]->add($file);
        }
        return $groups;
    }
}