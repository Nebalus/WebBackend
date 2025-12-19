<?php

namespace Nebalus\Webapi\Value\User\AccessControl\Role;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

class RoleName
{
    use SanitizrValueObjectTrait;

    public const int MIN_LENGTH = 4;
    public const int MAX_LENGTH = 32;
    public const string REGEX = '/^[a-zA-Z0-9_]+$/';

    private function __construct(
        private readonly string $roleName
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::string()->min(self::MIN_LENGTH)->max(self::MAX_LENGTH)->regex(self::REGEX);
    }

    /**
     * @throws ApiException
     */
    public static function from(string $roleName): self
    {
        $schema = static::getSchema();
        $validData = $schema->safeParse($roleName);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException('Invalid Role Name: ' . $validData->getErrorMessage());
        }

        return new self($validData->getValue());
    }

    public function asString(): string
    {
        return $this->roleName;
    }
}
