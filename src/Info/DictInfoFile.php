<?php declare(strict_types=1);

namespace StarDict\Info;

use RuntimeException;

class DictInfoFile
{
    private string $filename;
    private SignatureChecker $signature;

    public function __construct(string $filename, SignatureChecker $signature)
    {
        $this->filename = $filename;
        $this->signature = $signature;
    }

    public function getProvider(): DictProvider
    {
        $lines = $this->explodeContents(
            $this->getFileContents($this->filename)
        );

        // Signature + 3 required lines (see docs).
        if (count($lines) < 4) {
            throw new RuntimeException('Invalid info file: ' . $this->filename);
        }

        $signature = array_shift($lines);
        $this->signature->checkAndThrow($signature);

        return new DictArrayProvider($lines);
    }

    public function getFileContents(): string
    {
        $buf = @file_get_contents($this->filename);
        if ($buf === FALSE) {
            throw new RuntimeException('Cannot read file: ' . $this->filename);
        }
        return trim($buf);
    }

    protected function explodeContents(string $contents): array
    {
        return array_filter(explode("\n", $contents));
    }
}