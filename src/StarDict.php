<?php declare(strict_types=1);

namespace StarDict;

use RuntimeException;
use StarDict\DictData\DataReader;
use StarDict\DictData\FileDataReader;
use StarDict\DictData\Sequences\{
    PhoneticString, PngFile, PureText, TypeSequence, WavFile,
};
use StarDict\DictData\TypeSequenceManager;
use StarDict\Index\DataOffsetItem;
use StarDict\Index\FileIndexProvider;
use StarDict\Index\IndexDataHandler;
use StarDict\Info\DictInfoFile;
use StarDict\Info\DictProvider;
use StarDict\Info\SignatureChecker;

class StarDict
{
    private Dict $dict;
    private IndexDataHandler $indexHandler;
    private DataReader $dataReader;

    /**
     * @var DataOffsetItem[]
     */
    private array $offsets;

    /**
     * @var TypeSequence[]
     */
    private array $typeSequences;

    public function __construct(
        Dict $dict,
        IndexDataHandler $indexHandler,
        DataReader $dataReader,
        TypeSequenceManager $typeSequenceManager
    ) {
        $this->dict = $dict;
        $this->indexHandler = $indexHandler;
        $this->dataReader = $dataReader;
        $this->offsets = [];
        $this->typeSequences = $typeSequenceManager->getSequences($dict->getSameTypeSequence());

        $this->checkVersion();
        $this->buildOffsets();
    }

    protected function checkVersion(): void
    {
        if ($this->dict->getVersion() !== '2.4.2') {
            throw new RuntimeException('Only 2.4.2 version is supported.');
        }
    }

    protected function buildOffsets(): void
    {
        foreach ($this->indexHandler->getDataOffsets() as $index => $offset) {
            $this->offsets[$index] = $offset;
        }
    }

    public function get(string $toc)
    {
        $offset = $this->offsets[$toc];
        return $this->dataReader->fillSequences($offset, $this->typeSequences);
    }

    public static function createFromFiles(
        string $fileIfo,
        string $fileIdx,
        string $fileDict
    ): self {
        $dict = static::createDictProviderFromFile($fileIfo)->getDict();
        $index = new FileIndexProvider($fileIdx, $dict->getIndexFilesize());
        $reader = new FileDataReader($fileDict);

        return new static(
            $dict,
            $index->getIndexDataHandler(),
            $reader,
            static::createTypeSequenceManager()
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

    public static function createTypeSequenceManager(): TypeSequenceManager
    {
        return (new TypeSequenceManager())
            ->register(new PureText())
            ->register(new PhoneticString())
            ->register(new PngFile())
            ->register(new WavFile())
            ;
    }
}