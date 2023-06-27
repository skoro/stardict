<?php

declare(strict_types=1);

namespace StarDict\DictData\Sequences;

class HtmlCodes extends TypeSequence
{
    public function getId(): string
    {
        return 'h';
    }

    public function asText(): string
    {
        return strip_tags($this->getValue());
    }
}
