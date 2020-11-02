<?php declare(strict_types=1);

namespace StarDict;

use InvalidArgumentException;
use RuntimeException;
use StarDict\Files\DictFile;
use StarDict\Files\DZDictFile;
use StarDict\Files\Factory;
use StarDict\Files\File;
use StarDict\Files\IndexFile;
use StarDict\Files\InfoFile;

class DictFiles
{
    private ?File $info = NULL;
    private ?File $index = NULL;
    private ?File $dict = NULL;

    public static function create(
        string $info,
        string $index,
        string $dict,
        Factory $fileFactory
    ): self {
        $me = new static();
        $me->add($fileFactory->createFileFromFilename($info))
           ->add($fileFactory->createFileFromFilename($index))
           ->add($fileFactory->createFileFromFilename($dict));
        return $me;
    }

    public function getInfo(): File
    {
        if ($this->info === NULL) {
            throw new RuntimeException('Info is missing.');
        }
        return $this->info;
    }

    public function getIndex(): File
    {
        if ($this->index === NULL) {
            throw new RuntimeException('Index is missing.');
        }
        return $this->index;
    }

    public function getDict(): File
    {
        if ($this->dict === NULL) {
            throw new RuntimeException('Dictionary data is missing.');
        }
        return $this->dict;
    }

    public function add(File $file): self
    {
        if ($file instanceof InfoFile) {
            $this->info = $file;
        } elseif ($file instanceof IndexFile) {
            $this->index = $file;
        } elseif (($file instanceof DictFile) || ($file instanceof DZDictFile)) {
            $this->dict = $file;
        } else {
            throw new InvalidArgumentException(sprintf('"%s" is not part of StarDict files.', $file->getFilename()));
        }
        return $this;
    }

    public function isDictCompressed(): bool
    {
        return $this->dict instanceof DZDictFile;
    }

    public function hasAllFiles(): bool
    {
        return $this->info && $this->index && $this->dict;
    }
}
