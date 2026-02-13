<?php

namespace Nebalus\Webapi\Value\Hash;

readonly class SHA256Hash
{
    private function __construct(
        private string $hash
    ) {
    }

    public static function from(string $input): self
    {
        return new self(hash('sha256', $input));
    }

    public function asString(): string
    {
        return $this->hash;
    }
}
