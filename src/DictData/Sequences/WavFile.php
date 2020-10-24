<?php declare(strict_types=1);

namespace StarDict\DictData\Sequences;

class WavFile extends TypeSequence
{
    /**
     * @inheritdoc
     */
    public function getId(): string
    {
        return 'W';
    }
}