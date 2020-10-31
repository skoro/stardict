<?php declare(strict_types=1);

namespace StarDict\Tests;

use StarDict\DictData\DataReader;
use StarDict\DictData\Sequences\PureText;
use StarDict\Index\DataOffsetItem;
use StarDict\Tests\TestCase;

class DataReaderTest extends TestCase
{
    protected function makeReader(string $buf)
    {
        return new class($buf) extends DataReader {
            private string $buf;
            public function __construct(string $buf)
            {
                $this->buf = $buf;
            }
            protected function readBuffer(DataOffsetItem $offset): string
            {
                return substr($this->buf, $offset->getOffset(), $offset->getLength());
            }
        };
    }

    public function testOneChunkPureText()
    {
        $data = str_repeat('a', 10) . str_repeat('b', 5) . str_repeat('c', 8);

        $reader = $this->makeReader($data);
        $offset = new DataOffsetItem('test', 10, 5);
        $seq = $reader->fillSequences($offset, [
            new PureText(),
        ]);

        $this->assertEquals(1, count($seq));
        $this->assertEquals('bbbbb', $seq[0]->getValue());
    }

    public function testTwoChunks()
    {
        $data = str_repeat('a', 5) . "\0" . str_repeat('b', 5) . str_repeat('c', 10);

        $reader = $this->makeReader($data);
        // read the buffer with two chunks and nul char: 5+5+1
        $offset = new DataOffsetItem('does not matter', 0, 11);
        $seq = $reader->fillSequences($offset, [
            new PureText(),
            new PureText(),
        ]);

        $this->assertCount(2, $seq);
        $this->assertEquals('aaaaa', $seq[0]->getValue());
        $this->assertEquals('bbbbb', $seq[1]->getValue());
    }

    public function testChunksInTheEnd()
    {
        $data = str_repeat('a', 5) . "\0" . str_repeat('b', 5) . "\0" . str_repeat('c', 5);

        $reader = $this->makeReader($data);
        $offset = new DataOffsetItem('anything', 6, 11);
        $seq = $reader->fillSequences($offset, [
            new PureText(),
            new PureText(),
        ]);

        $this->assertCount(2, $seq);
        $this->assertEquals('bbbbb', $seq[0]->getValue());
        $this->assertEquals('ccccc', $seq[1]->getValue());
    }
}