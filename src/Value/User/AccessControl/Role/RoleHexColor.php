<?php

namespace Nebalus\Webapi\Value\User\AccessControl\Role;

use Nebalus\Sanitizr\Sanitizr;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Value\SanitizrValueObjectTrait;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

class RoleHexColor
{
    use SanitizrValueObjectTrait;

    public const int LENGTH = 6;
    public const string REGEX = '/^[a-fA-F0-9]+$/';

    private function __construct(
        private readonly string $color
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return Sanitizr::string()->length(self::LENGTH, "Is not a valid hex color code")->regex(self::REGEX, "Is not a valid hex color code");
    }

    /**
     * @throws ApiInvalidArgumentException
     */
    public static function from(string $color): self
    {
        $schema = static::getSchema();
        $validData = $schema->safeParse($color);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException("Invalid color: " . $validData->getErrorMessage());
        }

        return new self($validData->getValue());
    }

    public function asString(): string
    {
        return $this->color;
    }
}
