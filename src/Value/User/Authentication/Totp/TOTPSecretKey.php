<?php

namespace Nebalus\Webapi\Value\User\Authentication\Totp;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Random\RandomException;

class TOTPSecretKey
{
    use SanitizrValueObjectTrait;

    public const string REGEX = '/^[\d\w]*$/';
    public const int LENGTH = 32; // Must be divisible by 2

    private function __construct(
        private readonly string $secretKey
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::string()->length(self::LENGTH)->regex(self::REGEX);
    }

    /**
     * @throws ApiException
     */
    public static function create(): self
    {
        try {
            return self::from(strtoupper(bin2hex(random_bytes(self::LENGTH / 2))));
        } catch (RandomException $e) {
            throw new ApiInvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws ApiException
     */
    public static function from(string $secretKey): self
    {
        $schema = static::getSchema();
        $validData = $schema->safeParse($secretKey);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException('Invalid totp secret: ' . $validData->getErrorMessage());
        }

        return new self($validData->getValue());
    }

    public function asString(): string
    {
        return $this->secretKey;
    }
}
