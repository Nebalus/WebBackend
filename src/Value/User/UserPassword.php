<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Value\User;

use InvalidArgumentException;

use function strlen;

readonly class UserPassword
{
    private function __construct(
        private string $password
    ) {
    }

    public static function fromPlain(string $plainPassword, int $cost = 10): self
    {
        if (strlen($plainPassword) < 8) {
            throw new InvalidArgumentException('Invalid password: must be longer than 8 characters');
        }

        if (strlen($plainPassword) > 20) {
            throw new InvalidArgumentException('Invalid password: cannot be longer than 20 characters');
        }

        $passwordHash = password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => $cost]);

        return new self($passwordHash);
    }

    public static function fromHash(string $hashedPassword): self
    {
        return new self($hashedPassword);
    }

    public function verify(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->password);
    }
}