<?php

namespace Nebalus\Webapi\Value\User\AccessControl\Role;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Value\SanitizrValueObjectTrait;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

class RoleAccessLevel
{
    use SanitizrValueObjectTrait;

    private function __construct(
        private readonly int $accessLevel,
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::number()->integer()->nonNegative();
    }

    /**
     * @throws ApiInvalidArgumentException
     */
    public static function from(int $accessLevel): self
    {
        $schema = static::getSchema();
        $validData = $schema->safeParse($accessLevel);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException('Invalid role accessLevel: ' . $validData->getErrorMessage());
        }

        return new self($validData->getValue());
    }

    public function asInt(): int
    {
        return $this->accessLevel;
    }
}
