<?php

namespace Nebalus\Webapi\Value\User\AccessControl\Permission;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

class PermissionValue
{
    use SanitizrValueObjectTrait;

    private function __construct(
        private readonly int $value
    ) {
    }

    public static function defineSchema(): AbstractSanitizrSchema
    {
        return S::number()->integer();
    }

    /**
     * @throws ApiException
     */
    public static function from(int $value): self
    {
        $schema = static::getSchema();
        $validData = $schema->safeParse($value);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException('Invalid Permission Value: ' . $validData->getErrorMessage());
        }

        return new self($validData->getValue());
    }

    public function asInt(): int
    {
        return $this->value;
    }
}
