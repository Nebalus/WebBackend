<?php

namespace Nebalus\Webapi\Value\User\AccessControl\Permission;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

class PermissionDescription
{
    use SanitizrValueObjectTrait;

    public const int MAX_LENGTH = 255;
    public const string REGEX = '/^[\w\d_.\s\-\(\)]*$/';

    private function __construct(
        private readonly string $description,
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::string()->max(self::MAX_LENGTH)->regex(self::REGEX);
    }

    /**
     * @throws ApiInvalidArgumentException
     */
    public static function from(string $description): self
    {
        $description = trim($description);

        $schema = static::getSchema();
        $validData = $schema->safeParse($description);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException('Invalid Permission Description: ' . $validData->getErrorMessage());
        }

        return new self($validData->getValue());
    }

    public function asString(): string
    {
        return $this->description;
    }
}
