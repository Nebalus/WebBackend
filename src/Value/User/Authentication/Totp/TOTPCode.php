<?php

namespace Nebalus\Webapi\Value\User\Authentication\Totp;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

class TOTPCode
{
    use SanitizrValueObjectTrait;

    public const string REGEX = '/^[\d]*$/';
    public const int LENGTH = 6;

    private function __construct(
        private readonly string $code,
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::string()->length(self::LENGTH)->regex(self::REGEX);
    }

    /**
     * @throws ApiException
     */
    public static function from(string $code): self
    {
        $schema = static::getSchema();
        $validData = $schema->safeParse($code);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException('Invalid totp code: ' . $validData->getErrorMessage());
        }

        return new self($validData->getValue());
    }

    public function asString(): string
    {
        return $this->code;
    }
}
