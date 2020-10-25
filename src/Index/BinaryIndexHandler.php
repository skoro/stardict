<?php declare(strict_types=1);

namespace StarDict\Index;

use Generator;
use InvalidArgumentException;

class BinaryIndexHandler implements IndexDataHandler
{
    private string $data;

    public function __construct(string $data)
    {
        $this->data = $data;
        if ($this->getDataLength() === 0) {
            throw new InvalidArgumentException('Binary data cannot be empty.');
        }
    }

    protected function getDataLength(): int
    {
        return strlen($this->data);
    }

    public function getDataOffsets(): Generator
    {
        $pos = 0;
        $length = $this->getDataLength();

        while ($pos < $length) {
            $chars = [];
            while (TRUE) {
                $x = unpack("@{$pos}/Cch", $this->data);
                if ($x['ch'] === 0 && $pos !== 0) {
                    break;
                }
                $chars[] = $x['ch'];
                $pos++;
            }
            $word = pack('C*', ...$chars);
            $pos++;
            $x = unpack("@{$pos}/Noffset/Nsize", $this->data);
            yield $word => new DataOffsetItem($word, $x['offset'], $x['size']);
            $pos += 8;
        }
    }
}
