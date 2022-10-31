<?php

declare(strict_types=1);

namespace StarDict\Tests;

use StarDict\DictFiles;
use StarDict\Files\DictFile;
use StarDict\Files\DZDictFile;
use StarDict\Files\IndexFile;
use StarDict\Files\InfoFile;

class DictFilesTest extends TestCase
{
    /** @test */
    public function it_can_add_info_file(): void
    {
        $files = new DictFiles();
        $files->add(new InfoFile('test.ifo'));

        $this->assertTrue($files->hasInfo());
    }

    /** @test */
    public function it_can_add_index_file(): void
    {
        $files = new DictFiles();
        $files->add(new IndexFile('test.idx'));

        $this->assertTrue($files->hasIndex());
    }

    /** @test */
    public function it_can_add_dict_file(): void
    {
        $files = new DictFiles();
        $files->add(new DictFile('test.dict'));

        $this->assertTrue($files->hasDict());
    }

    /** @test */
    public function it_can_add_compressed_dict_file(): void
    {
        $files = new DictFiles();
        $files->add(new DZDictFile('test.dict.dz'));

        $this->assertTrue($files->hasDict());
    }
}
