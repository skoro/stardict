<?php declare(strict_types=1);

namespace StarDict\DictData\Sequences;

use StarDict\DictData\Chunk;

abstract class TypeSequence
{
    /**
     * @return mixed
     */
    public function getValue()
    {
    }

    public function readChunk(string $buf): Chunk
    {
        $pos = 0;
        $length = strlen($buf);

        while ($pos < $length) {
            $bytes = unpack('cchr', $buf, $pos++);
            if ($bytes['chr'] == '\0') {
                return $this->createChunk(substr($buf, 0, $pos - 1));
            }
        }

        return $this->createChunk($buf);
    }

    protected function createChunk(string $data): Chunk
    {
        return new Chunk($data);
    }

    abstract public function getId(): string;
}