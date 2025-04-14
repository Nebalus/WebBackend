<?php

namespace Nebalus\Webapi\Value\Module\Referral\Click;

use Nebalus\Sanitizr\Sanitizr;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Value\SanitizrValueObjectTrait;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

class ReferralClickAmount
{
    use SanitizrValueObjectTrait;

    private function __construct(
        private readonly int $clickAmount
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return Sanitizr::number()->integer()->nonNegative();
    }

    /**
     * @throws ApiInvalidArgumentException
     */
    public static function from(int $clickAmount): self
    {
        $schema = static::getSchema();
        $validData = $schema->safeParse($clickAmount);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException('Invalid click amount: ' . $validData->getErrorMessage());
        }

        return new self($validData->getValue());
    }

    public function asInt(): int
    {
        return $this->clickAmount;
    }
}
