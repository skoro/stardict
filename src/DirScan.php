<?php declare(strict_types=1);

namespace StarDict;

use Directory;
use RuntimeException;

/**
 * Scans directories for StarDict files.
 */
class DirScan
{
    private string $baseDir;

    /**
     * @param string $baseDir The base directory from scan.
     */
    public function __construct(string $baseDir)
    {
        $this->baseDir = $baseDir;
    }

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
                if (substr($filename, -4) === '.ifo') {
                    $ext = 'ifo';
                } elseif (substr($filename, -4) === '.idx') {
                    $ext = 'idx';
                } elseif (substr($filename, -5) === '.dict') {
                    $ext = 'dict';
                } elseif (substr($filename, -8) === '.dict.dz') {
                    $ext = 'dict.dz';
                } else {
                    continue;
                }
                $idx = $subDir . DIRECTORY_SEPARATOR . basename($filename, ".$ext");
                if (substr($ext, -3) === '.dz') {
                    $ext = substr($ext, 0, -3);
                }
                $files[$idx][$ext] = $filepath;
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
     * @return StarDict[]
     *
     * @throws RuntimeException When scanning is failed.
     */
    public function scan(): array
    {
        $dicts = [];
        $dirFiles = $this->internalScan($this->baseDir);
        foreach ($dirFiles as $files) {
            $dicts[] = StarDict::createFromFiles($files['ifo'], $files['idx'], $files['dict']);
        }
        return $dicts;
    }
}