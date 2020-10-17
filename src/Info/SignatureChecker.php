<?php declare(strict_types=1);

namespace StarDict\Info;

use StarDict\Exception\InvalidSignatureException;

class SignatureChecker
{
    private string $signature;

    public function __construct(string $signature = "StarDict's dict ifo file")
    {
        $this->signature = $signature;
    }

    public function check(string $sig): bool
    {
        return $this->signature === $sig;
    }

    public function checkAndThrow(string $sig): void
    {
        if (! $this->check($sig)) {
            throw new InvalidSignatureException();
        }
    }

    public function getSignature(): string
    {
        return $this->signature;
    }
}