<?php declare(strict_types=1);

namespace StarDict\Tests;

use StarDict\Index\BinaryIndexHandler;
use StarDict\Index\DataOffsetItem;

class BinaryIndexHandlerTest extends TestCase
{
    public function testReadOne()
    {
        $data = hex2bin('2dd0b4d0b5d0b2d18fd1820000000000000000aa');
        $h = new BinaryIndexHandler($data);

        /** @var DataOffsetItem $offset */
        $offset = $h->getDataOffsets()->current();

        $this->assertEquals('-девят', $offset->getQuery());
        $this->assertEquals(0, $offset->getOffset());
        $this->assertEquals(170, $offset->getLength());
    }

    public function testReadTwo()
    {
        $data = hex2bin('2dd0b4d0b5d0b2d18fd1820000000000000000aa2dd0b4d0b5d181d18fd18200000000aa000000aa');
        $h = new BinaryIndexHandler($data);

        /** @var DataOffsetItem $offset */
        $gen = $h->getDataOffsets();
        $gen->next();
        /** @var DataOffsetItem $offset */
        $offset = $gen->current();

        $this->assertEquals('-десят', $offset->getQuery());
        $this->assertEquals(170, $offset->getOffset());
        $this->assertEquals(170, $offset->getLength());
    }
}