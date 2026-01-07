<?php

namespace Nebalus\Webapi\Value\Module\Referral;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

class ReferralLabel
{
    use SanitizrValueObjectTrait;

    public const int MAX_LENGTH = 32;
    public const string REGEX = '/^[a-zA-Z0-9\s!@#$%^&*]*$/';

    private function __construct(
        private readonly string $referralName,
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::string()->max(self::MAX_LENGTH)->regex(self::REGEX);
    }

    /**
     * @throws ApiException
     */
    public static function from(?string $referralName): self
    {
        $schema = static::getSchema();
        $validData = $schema->safeParse($referralName);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException("Invalid referral name: " . $validData->getErrorMessage());
        }

        return new self($validData->getValue());
    }
    public function asString(): string
    {
        return $this->referralName;
    }
}
