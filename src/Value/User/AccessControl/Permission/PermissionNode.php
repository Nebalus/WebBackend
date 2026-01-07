<?php

namespace Nebalus\Webapi\Value\User\AccessControl\Permission;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

class PermissionNode
{
    use SanitizrValueObjectTrait;

    public const int MAX_LENGTH = 128;
    private const string REGEX = '/^(([a-z_])+(\.[a-z_.]+)*)$/';

    private function __construct(
        private readonly string $node
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::string()->max(self::MAX_LENGTH)->regex(self::REGEX);
    }

    /**
     * @throws ApiException
     */
    public static function from(string $node): self
    {
        $schema = static::getSchema();
        $validData = $schema->safeParse($node);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException('Invalid Permission Node: ' . $validData->getErrorMessage());
        }

        return new self($validData->getValue());
    }

    public function asString(): string
    {
        return $this->node;
    }
}
