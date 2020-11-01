<?php declare(strict_types=1);

namespace StarDict\DictData;

use InvalidArgumentException;
use RuntimeException;
use StarDict\DictData\Sequences\TypeSequence;

class TypeSequenceManager
{
    private array $types = [];

    public function register(TypeSequence $type): self
    {
        $this->types[$type->getId()] = $type;
        return $this;
    }

    public function clear(): self
    {
        foreach ($this->types as $type) {
            $type->clear();
        }
        return $this;
    }

    /**
     * @return TypeSequence[]
     */
    public function getSequences(string $sameTypeSequence): array
    {
        if (empty($sameTypeSequence)) {
            throw new InvalidArgumentException('sameTypeSequence cannot be empty.');
        }

        $types = [];

        foreach (str_split($sameTypeSequence) as $char) {
            if (isset($this->types[$char])) {
                $types[] = $this->types[$char];
            } else {
                throw new RuntimeException(sprintf('Unknown type "%s", maybe it should be registered ?', $char));
            }
        }

        return $types;
    }
}