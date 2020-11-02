<?php declare(strict_types=1);

namespace StarDict\Info;

use RuntimeException;
use StarDict\Files\InfoFile;

class DictInfoFile
{
    private InfoFile $file;
    private SignatureChecker $signature;
    private ?DictArrayProvider $provider;

    public function __construct(InfoFile $file, SignatureChecker $signature)
    {
        $this->file = $file;
        $this->signature = $signature;
        $this->provider = null;
    }

    public function getProvider(): DictProvider
    {
        if (!$this->provider) {
            $lines = $this->explodeContents(
                $this->getFileContents()
            );
    
            // Signature + 3 required lines (see docs).
            if (count($lines) < 4) {
                throw new RuntimeException('Invalid info file: ' . $this->file);
            }
    
            $signature = array_shift($lines);
            $this->signature->checkAndThrow($signature);
    
            $this->provider = new DictArrayProvider($lines);
        }

        return $this->provider;
    }

    public function getFileContents(): string
    {
        $buf = @file_get_contents($this->file->getFilename());
        if ($buf === FALSE) {
            throw new RuntimeException('Cannot read file: ' . $this->file);
        }
        return trim($buf);
    }

    protected function explodeContents(string $contents): array
    {
        return array_filter(explode("\n", $contents));
    }
}