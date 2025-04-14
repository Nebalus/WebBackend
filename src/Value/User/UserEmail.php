<?php

namespace Nebalus\Webapi\Value\User;

use Nebalus\Sanitizr\Sanitizr;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Value\SanitizrValueObjectTrait;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

class UserEmail
{
    use SanitizrValueObjectTrait;

    private function __construct(
        private readonly string $email,
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return Sanitizr::string()->email();
    }

    /**
     * @throws ApiException
     */
    public static function from(string $email): self
    {
        $schema = static::getSchema();
        $validData = $schema->safeParse($email);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException($validData->getErrorMessage());
        }

        return new self($validData->getValue());
    }

    public function asString(): string
    {
        return $this->email;
    }
}
