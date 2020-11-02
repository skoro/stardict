<?php declare(strict_types=1);

namespace StarDict;

use RuntimeException;
use StarDict\DictData\DataReader;
use StarDict\DictData\FileDataReader;
use StarDict\DictData\FileDZDataReader;
use StarDict\DictData\Sequences\{
    PhoneticString, PngFile, PureText, TypeSequence, WavFile,
};
use StarDict\DictData\TypeSequenceManager;
use StarDict\Files\Factory;
use StarDict\Files\File;
use StarDict\Index\DataOffsetItem;
use StarDict\Index\FileIndexProvider;
use StarDict\Index\IndexDataHandler;
use StarDict\Index\IndexProvider;
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

    private bool $needBuildOffsets;

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
        $this->needBuildOffsets = TRUE;

        $this->checkVersion();
    }

    protected function checkVersion(): void
    {
        if ($this->dict->getVersion() !== '2.4.2') {
            throw new RuntimeException('Only 2.4.2 version is supported.');
        }
    }

    protected function buildOffsets(): void
    {
        if ($this->needBuildOffsets) {
            foreach ($this->indexHandler->getDataOffsets() as $index => $offset) {
                $this->offsets[$index] = $offset;
            }
            $this->needBuildOffsets = FALSE;
        }
    }

    /**
     * @return TypeSequence[]
     */
    public function get(string $toc): array
    {
        $this->buildOffsets();
        $offset = $this->offsets[$toc];
        return $this->dataReader->fillSequences($offset, $this->typeSequences);
    }

    /**
     * @return DataOffsetItem[]
     */
    public function getOffsets(): array
    {
        $this->buildOffsets();
        return $this->offsets;
    }

    public static function createFromFiles(
        string $fileInfo,
        string $fileIdx,
        string $fileDict
    ): self {
        return static::create(
            DictFiles::create($fileInfo, $fileIdx, $fileDict, new Factory())
        );
    }

    public static function create(DictFiles $files): self
    {
        $infoProvider = static::createInfoProvider($files->getInfo());
        $dict = $infoProvider->getDict();
        $indexProvider = static::createIndexProvider($files->getIndex(), $dict->getIndexFilesize());

        if ($files->isDictCompressed()) {
            $reader = new FileDZDataReader($files->getDict());
        } else {
            $reader = new FileDataReader($files->getDict());
        }

        return new static(
            $dict,
            $indexProvider->getIndexDataHandler(),
            $reader,
            static::createTypeSequenceManager()
        );
    }

    protected static function createInfoProvider(File $file): DictProvider
    {
        $info = new DictInfoFile($file, new SignatureChecker());
        return $info->getProvider();
    }

    protected static function createIndexProvider(File $file, int $indexSize): IndexProvider
    {
        return new FileIndexProvider($file, $indexSize);
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