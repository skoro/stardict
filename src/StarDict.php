<?php declare(strict_types=1);

namespace StarDict;

use RuntimeException;
use StarDict\Index\BinaryIndexLookup;
use StarDict\Index\IndexLookup;
use StarDict\Info\DictInfoFile;
use StarDict\Info\DictProvider;
use StarDict\Info\SignatureChecker;

class StarDict
{
    private Dict $dict;
    private IndexLookup $indexLookup;

    public function __construct(
        Dict $dict,
        IndexLookup $indexLookup
    ) {
        $this->dict = $dict;
        $this->indexLookup = $indexLookup;
        $this->checkVersion();
    }

    protected function checkVersion()
    {
        if ($this->dict->getVersion() !== '2.4.2') {
            throw new RuntimeException('Only 2.4.2 version is supported.');
        }
    }

    public static function createFromFile(string $filename): self
    {
        $dict = static::createDictProviderFromFile($filename)->getDict();
        return new static(
            $dict,
            new BinaryIndexLookup($filename, $dict)
        );
    }

    protected static function createDictProviderFromFile(string $filename): DictProvider
    {
        $info = new DictInfoFile($filename, new SignatureChecker());
        return $info->getProvider();
    }

    public function getDict(): Dict
    {
        return $this->dict;
    }
}