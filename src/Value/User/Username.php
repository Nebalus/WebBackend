<?php

namespace Nebalus\Webapi\Value\User;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

class Username
{
    use SanitizrValueObjectTrait;

    public const int MIN_LENGTH = 4;
    public const int MAX_LENGTH = 16;
    public const string REGEX = '/^[a-zA-Z]+$/';

    private function __construct(
        private readonly string $username
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::string()->min(self::MIN_LENGTH)->max(self::MAX_LENGTH)->regex(self::REGEX);
    }

    /**
     * @throws ApiException
     */
    public static function from(string $username): self
    {
        $schema = static::getSchema();
        $validData = $schema->safeParse($username);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException('Invalid username: ' . $validData->getErrorMessage());
        }

        return new self($validData->getValue());
    }

    public function asString(): string
    {
        return $this->username;
    }
}
