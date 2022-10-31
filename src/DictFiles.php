<?php declare(strict_types=1);

namespace StarDict;

use InvalidArgumentException;
use RuntimeException;
use StarDict\Files\DictFile;
use StarDict\Files\DZDictFile;
use StarDict\Files\Factory as FileFactory;
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
        FileFactory $fileFactory
    ): self {
        $me = new static();
        $me->add($fileFactory->createFileFromFilename($info))
           ->add($fileFactory->createFileFromFilename($index))
           ->add($fileFactory->createFileFromFilename($dict));
        return $me;
    }

    public function hasInfo(): bool
    {
        return $this->info !== NULL;
    }

    /**
     * @throws RuntimeException When dict info file is missing.
     */
    public function getInfo(): File
    {
        if ($this->hasInfo()) {
            return $this->info;
        }

        throw new RuntimeException('Info is missing.');
    }

    public function hasIndex(): bool
    {
        return $this->index !== null;
    }

    /**
     * @throws RuntimeException When dict index file is missing.
     */
    public function getIndex(): File
    {
        if ($this->hasIndex()) {
            return $this->index;
        }

        throw new RuntimeException('Index is missing.');
    }

    public function hasDict(): bool
    {
        return $this->dict !== null;
    }

    /**
     * @throws RuntimeException When dict data file is missing.
     */
    public function getDict(): File
    {
        if ($this->hasDict()) {
            return $this->dict;
        }

        throw new RuntimeException('Dictionary data is missing.');
    }

    /**
     * @param InfoFile|IndexFile|DictFile|DZDictFile $file
     */
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
        return $this->hasInfo() && $this->hasIndex() && $this->hasDict();
    }
}
