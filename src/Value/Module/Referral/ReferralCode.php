<?php

namespace Nebalus\Webapi\Value\Module\Referral;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

class ReferralCode
{
    use SanitizrValueObjectTrait;

    public const int LENGTH = 8;
    public const string REGEX = '/^[0-9A-Za-z]+$/';

    private function __construct(
        private readonly string $code
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::string()->length(self::LENGTH)->regex(self::REGEX);
    }

    public static function create(): self
    {
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        return new self(substr(str_shuffle($str_result), 0, self::LENGTH));
    }

    /**
     * @throws ApiException
     */
    public static function from(string $code): self
    {
        $schema = static::getSchema();
        $validData = $schema->safeParse($code);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException('Invalid referral code: ' . $validData->getErrorMessage());
        }

        return new self($validData->getValue());
    }

    public function asString(): string
    {
        return $this->code;
    }
}
