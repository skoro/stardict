<?php declare(strict_types=1);

namespace StarDict\DictData\Sequences;

class PureText extends TypeSequence
{
    /**
     * @inheritdoc
     */
    public function getId(): string
    {
        return 'm';
    }
}