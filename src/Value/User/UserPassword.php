<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Value\User;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

use Nebalus\Webapi\Utils\Sanitizr\Sanitizr;
use function strlen;

readonly class UserPassword
{
    public const int MIN_LENGTH = 8;
    public const int MAX_LENGTH = 64;

    private function __construct(
        private string $passwordHash
    ) {
    }

    /**
     * @throws ApiException
     */
    public static function fromPlain(string $plainPassword, int $cost = 10): self
    {
        $schema = Sanitizr::string()->min(self::MIN_LENGTH)->max(self::MAX_LENGTH);
        $validData = $schema->safeParse($plainPassword);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException("Invalid password: " . $validData->getErrorMessage());
        }

        $passwordHash = password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => $cost]);

        return new self($passwordHash);
    }

    public static function fromHash(string $hashedPassword): self
    {
        return new self($hashedPassword);
    }

    public function asString(): string
    {
        return $this->passwordHash;
    }

    public function verify(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->passwordHash);
    }
}
