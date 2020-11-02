<?php declare(strict_types=1);

namespace StarDict\Tests;

use RuntimeException;
use StarDict\Exception\InvalidSignatureException;
use StarDict\Files\InfoFile;
use StarDict\Info\DictInfoFile;
use StarDict\Info\SignatureChecker;

class DictInfoFileTest extends TestCase
{
    protected function getMockedProvider(string $contents)
    {
        $file = new InfoFile('test.ifo');
        $mock = $this->getMockBuilder(DictInfoFile::class)
                     ->setConstructorArgs([$file, new SignatureChecker('test')])
                     ->onlyMethods(['getFileContents'])
                     ->getMock();

        $mock->method('getFileContents')
             ->willReturn($contents);

        return $mock;
    }

    public function testInvalidSignature()
    {
        $infoMock = $this->getMockedProvider("test1\nline1\nline2\nline3");

        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('Invalid dictionary signature.');

        /** @var DictInfoFile $infoMock */
        $infoMock->getProvider();
    }

    public function testCannotBeEmpty()
    {
        $info = $this->getMockedProvider("test\n\n\n\n\n");

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid info file: test.ifo');

        /** @var DictInfoFile $info */
        $info->getProvider();
    }

    public function testMustBeFourLinesAtMinumim()
    {
        $infoMock = $this->getMockedProvider("test\nline1\nline2\n");

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid info file: test.ifo');

        /** @var DictInfoFile $infoMock */
        $infoMock->getProvider();
    }

    public function testRequiredFields()
    {
        $info = $this->getMockedProvider("test\nbookname=Test\nwordcount=100\nidxfilesize=255\n");

        /** @var DictInfoFile $info */
        $dict = $info->getProvider()->getDict();

        $this->assertEquals('Test', $dict->getBookname());
        $this->assertEquals(100, $dict->getWordCount());
        $this->assertEquals(255, $dict->getIndexFilesize());
    }

    public function testFieldsCase()
    {
        $info = $this->getMockedProvider("test\nBookName=Test\nWordCount=100\nIdxFileSize=255\n");

        /** @var DictInfoFile $info */
        $dict = $info->getProvider()->getDict();

        $this->assertEquals('Test', $dict->getBookname());
        $this->assertEquals(100, $dict->getWordCount());
        $this->assertEquals(255, $dict->getIndexFilesize());
    }
}