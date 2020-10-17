<?php declare(strict_types=1);

namespace StarDict\Index;

use Generator;
use RuntimeException;
use StarDict\Dict;

class BinaryIndexLookup implements IndexLookup
{
    private string $filename;
    private Dict $dict;
    /**
     * Is index already loaded ?
     */
    private bool $loaded;

    /**
     * @var Offset[]
     */
    private array $index;

    public function __construct(string $filename, Dict $dict)
    {
        $this->filename = $filename;
        $this->dict = $dict;
        $this->index = [];
        $this->loaded = FALSE;
    }

    protected function loadIndex(): void
    {
        if ($this->loaded) {
            return;
        }

        $binary = $this->readFile();

        if (($length = strlen($binary)) != $this->dict->getIndexFilesize()) {
            throw new RuntimeException('Index size is mismatched.');
        }

        $pos = 0;
        $this->index = [];

        while ($pos < $length) {
            $chars = [];
            while (TRUE) {
                $x = unpack("@{$pos}/Cch", $binary);
                $pos++;
                if ($x['ch'] === 0) {
                    break;
                }
                $chars[] = $x['ch'];
            }
            $word = pack('C*', ...$chars);
            $x = unpack("@{$pos}/Noffset/Nsize", $binary);
            $this->index[$word] = new Offset($word, $x['offset'], $x['length']);
            $pos += 8;
        }

        $this->loaded = TRUE;
    }

    public function match(string $query): ?Offset
    {
        $this->loadIndex();

        return $this->index[$query] ?? null;
    }

    protected function readFile(): string
    {
        // TODO: check for file extension.

        if (($bin = file_get_contents($this->filename)) === FALSE) {
            throw new RuntimeException("Couldn't load index: " . $this->filename);
        }
        return $bin;
    }

    public function enumerate(): Generator
    {
        $this->loadIndex();

        foreach ($this->index as $index) {
            yield $index->getQuery() => $index;
        }
    }
}