<?php declare(strict_types=1);

namespace StarDict\DictData;

use RuntimeException;
use StarDict\DictData\Sequences\TypeSequence;
use StarDict\Index\DataOffsetItem;

abstract class DataReader
{
    /**
     * @param DataOffsetItem $offset
     * @param TypeSequence[] $sequences
     *
     * @return TypeSequence[]
     */
    public function fillSequences(DataOffsetItem $offset, array $sequences): array
    {
        $buf = $this->readBuffer($offset);
        $pos = 0;
        foreach ($sequences as $sequence) {
            $chunk = $this->getChunk($buf, $pos);
            $pos += strlen($chunk) + 1;
            $sequence->setRawValue($chunk);
        }
        return $sequences;
    }

    protected function getChunk(string $buf, int $pos): string
    {
        $from = $pos;
        $length = strlen($buf);
        if ($pos > $length) {
            throw new RuntimeException('pos out of range.');
        }
        while ($pos < $length) {
            $bytes = unpack('cchr', $buf, $pos);
            if ($bytes['chr'] === 0) {
                return substr($buf, $from, $pos - $from);
            }
            $pos++;
        }
        return substr($buf, $from);
    }

    abstract protected function readBuffer(DataOffsetItem $offset): string;
}