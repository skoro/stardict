<?php declare(strict_types=1);

namespace StarDict\Tests;

use InvalidArgumentException;
use RuntimeException;
use StarDict\DictData\Sequences\PhoneticString;
use StarDict\DictData\Sequences\PngFile;
use StarDict\DictData\Sequences\PureText;
use StarDict\DictData\Sequences\WavFile;
use StarDict\DictData\TypeSequenceManager;

class TypeSequenceManagerTest extends TestCase
{
    public function testSameTypeSequenceCannotByEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('sameTypeSequence cannot be empty.');

        $m = new TypeSequenceManager();
        $m->getSequences('');
    }

    public function testGetSameTypeSequence()
    {
        $m = new TypeSequenceManager();
        $m->register(new PureText());
        $m->register(new PhoneticString());
        $m->register(new PngFile());
        $m->register(new WavFile());

        $s = $m->getSequences('tm');
        $this->assertEquals('t', $s[0]->getId());
        $this->assertEquals('m', $s[1]->getId());
    }

    public function testTypeNotFound()
    {
        $m = new TypeSequenceManager();
        $m->register(new PhoneticString());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unknown type "m", maybe it should be registered ?');

        $m->getSequences('tm');
    }

    public function testSequencesClear()
    {
        $p = new PureText();
        $p->setRawValue('test');
        $t = new PhoneticString();
        $t->setRawValue('test');

        $this->assertEquals('test', $p->getValue());
        $this->assertEquals('test', $t->getValue());

        $m = new TypeSequenceManager();
        $m->register($p)->register($t);
        $m->clear();

        $this->assertEmpty($p->getValue());
        $this->assertEmpty($t->getValue());
    }
}