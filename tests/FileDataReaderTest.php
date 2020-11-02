<?php declare(strict_types=1);

namespace StarDict\Tests;

use RuntimeException;
use StarDict\DictData\FileDataReader;
use StarDict\DictData\Sequences\PureText;
use StarDict\Files\DictFile;
use StarDict\Index\DataOffsetItem;

class FileDataReaderTest extends TestCase
{
    private string $filename;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'test_' . random_int(10000, 99999) . '.dict';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->filename)) {
            unlink($this->filename);
        }
        parent::tearDown();
    }

    public function testOpenNotFound()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot open dict data file: not-found.dic');

        $reader = new FileDataReader(new DictFile('not-found.dict'));
        $reader->fillSequences(new DataOffsetItem('', 0, 0), []);
    }

    public function testSeekOutRange()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Read buffer out of range.');

        file_put_contents($this->filename, 'abcdef');
        $reader = new FileDataReader(new DictFile($this->filename));
        $reader->fillSequences(new DataOffsetItem('', 999, 10), []);
    }

    public function testReadMoreThanExist()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Read buffer out of range.');

        file_put_contents($this->filename, 'abcdef');
        $reader = new FileDataReader(new DictFile($this->filename));
        $reader->fillSequences(new DataOffsetItem('', 0, 999), []);
    }

    public function testReadBufferOutOfRange()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Read buffer out of range.');

        file_put_contents($this->filename, '12345');
        $reader = new FileDataReader(new DictFile($this->filename));
        $offset = new DataOffsetItem('anything', 3, 100);
        $reader->fillSequences($offset, [new PureText()]);
    }
}