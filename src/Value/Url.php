<?php

namespace Nebalus\Webapi\Value;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

class Url
{
    use SanitizrValueObjectTrait;

    private function __construct(
        private readonly string $url
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::string()->url();
    }

    /**
     * @throws ApiInvalidArgumentException
     */
    public static function from(string $url): self
    {
        $schema = static::getSchema();
        $validData = $schema->safeParse($url);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException("Invalid url: " . $validData->getErrorMessage());
        }

        return new self($validData->getValue());
    }

    public function asString(): string
    {
        return $this->url;
    }
}
