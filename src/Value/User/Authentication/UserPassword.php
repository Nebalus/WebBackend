<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Value\User\Authentication;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

class UserPassword
{
    use SanitizrValueObjectTrait;

    public const int MIN_LENGTH = 8;
    public const int MAX_LENGTH = 256;

    private function __construct(
        private readonly string $passwordHash
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::string()->min(self::MIN_LENGTH)->max(self::MAX_LENGTH);
    }

    /**
     * @throws ApiException
     */
    public static function fromPlain(string $plainPassword, int $cost = 10): self
    {
        $schema = static::getSchema();
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
