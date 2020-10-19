<?php declare(strict_types=1);

namespace StarDict\Tests;

use InvalidArgumentException;
use StarDict\Info\DictInfoArrayProvider;

class DictInfoArrayProviderTest extends TestCase
{
    public function testFieldsCase()
    {
        $provider = new DictInfoArrayProvider([
            'Bookname=Test1',
            'authoR=me',
        ]);

        $this->assertEquals('Test1', $provider->getDict()->getBookname());
        $this->assertEquals('me', $provider->getDict()->getAuthor());
    }

    public function testDictDataCannotBeEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Dict data cannot be empty.');

        new DictInfoArrayProvider([]);
    }

    public function testSeparator()
    {
        $provider = new DictInfoArrayProvider([
            'bookname: test',
            'author: me',
        ], ':');

        $this->assertEquals('test', $provider->getDict()->getBookname());
        $this->assertEquals('me', $provider->getDict()->getAuthor());
    }
}