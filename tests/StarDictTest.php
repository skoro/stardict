<?php

declare(strict_types=1);

namespace StarDict\Tests;

use StarDict\Dict;
use StarDict\DictData\DataReader;
use StarDict\DictData\TypeSequenceManager;
use StarDict\Index\DataOffsetItem;
use StarDict\Index\IndexDataHandler;
use StarDict\StarDict;

class StarDictTest extends TestCase
{
    public function testThrowExceptionWhenStarDictVersionIsNotMatched(): void
    {
        $dict = $this->createMock(Dict::class);
        $dict->expects($this->once())
            ->method('getVersion')
            ->willReturn('1.1.1');

        $indexDataHandler = $this->createMock(IndexDataHandler::class);
        $dataReader = $this->createMock(DataReader::class);
        $typeSequenceManager = $this->createStub(TypeSequenceManager::class);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Only 2.4.2 version is supported.');

        new StarDict($dict, $indexDataHandler, $dataReader, $typeSequenceManager, true);
    }

    public function testNoOffsetExists(): void
    {
        $dict = $this->createStub(Dict::class);

        $indexDataHandler = $this->createStub(IndexDataHandler::class);
        $indexDataHandler->method('getDataOffsets')
            ->will($this->returnCallback(function () {
                yield from [
                    'a1' => new DataOffsetItem('a1', 0, 1),
                    'a2' => new DataOffsetItem('a2', 1, 1),
                    'a3' => new DataOffsetItem('a3', 2, 1),
                ];
            }));

        $dataReader = $this->createStub(DataReader::class);
        $typeSequenceManager = $this->createStub(TypeSequenceManager::class);

        $stardict = new StarDict($dict, $indexDataHandler, $dataReader, $typeSequenceManager, false);
        $result = $stardict->get('not found');

        $this->assertCount(0, $result);
    }
}
