<?php declare(strict_types=1);

namespace StarDict\Test;

use RuntimeException;
use StarDict\DictData\FileDataReader;
use StarDict\DictData\Sequences\PureText;
use StarDict\Index\DataOffsetItem;
use StarDict\Tests\TestCase;

class FileDataReaderTest extends TestCase
{
    private string $filename;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filename = tempnam(dirname(__FILE__), 'test_');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->filename)) {
            unlink($this->filename);
        }
        parent::tearDown();
    }

    public function testOneChunkPureText()
    {
        $data = str_repeat('a', 10) . str_repeat('b', 5) . str_repeat('c', 8);
        file_put_contents($this->filename, $data);

        $reader = new FileDataReader($this->filename);
        $offset = new DataOffsetItem('test', 10, 5);
        $chunks = $reader->readFromOffset($offset, [
            new PureText(),
        ]);

        $this->assertEquals(1, count($chunks));
        $this->assertEquals('bbbbb', $chunks[0]->getData());
    }

    public function testTwoChunks()
    {
        $data = str_repeat('a', 5) . "\0" . str_repeat('b', 5) . str_repeat('c', 10);
        file_put_contents($this->filename, $data);

        $reader = new FileDataReader($this->filename);
        // read the buffer with two chunks and nul char: 5+5+1
        $offset = new DataOffsetItem('does not matter', 0, 11);
        $chunks = $reader->readFromOffset($offset, [
            new PureText(),
            new PureText(),
        ]);

        $this->assertCount(2, $chunks);
        $this->assertEquals('aaaaa', $chunks[0]->getData());
        $this->assertEquals('bbbbb', $chunks[1]->getData());
    }

    public function testChunksInTheEnd()
    {
        $data = str_repeat('a', 5) . "\0" . str_repeat('b', 5) . "\0" . str_repeat('c', 5);
        file_put_contents($this->filename, $data);

        $reader = new FileDataReader($this->filename);
        $offset = new DataOffsetItem('anything', 6, 11);
        $chunks = $reader->readFromOffset($offset, [
            new PureText(),
            new PureText(),
        ]);

        $this->assertCount(2, $chunks);
        $this->assertEquals('bbbbb', $chunks[0]->getData());
        $this->assertEquals('ccccc', $chunks[1]->getData());
    }

    public function testReadBufferOutOfRange()
    {
        file_put_contents($this->filename, 'zxcvb');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Read buffer out of range.');

        $reader = new FileDataReader($this->filename);
        $offset = new DataOffsetItem('anything', 3, 100);
        $reader->readFromOffset($offset, [new PureText()]);
    }
}