<?php declare(strict_types=1);

namespace StarDict\Test;

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
}