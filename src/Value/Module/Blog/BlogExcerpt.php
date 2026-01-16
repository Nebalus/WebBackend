<?php

namespace Nebalus\Webapi\Value\Module\Blog;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

class BlogExcerpt
{
    use SanitizrValueObjectTrait;

    public const int MAX_LENGTH = 256;
    public const string REGEX = '/^[a-zA-Z0-9\s!@#$%^&*]*$/';

    private function __construct(
        private readonly string $blogExcerpt,
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::string()->max(self::MAX_LENGTH)->regex(self::REGEX);
    }

    /**
     * @throws ApiException
     */
    public static function from(?string $blogExcerpt): self
    {
        $schema = static::getSchema();
        $validData = $schema->safeParse($blogExcerpt);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException("Invalid blog excerpt: " . $validData->getErrorMessage());
        }

        return new self($validData->getValue());
    }
    public function asString(): string
    {
        return $this->blogExcerpt;
    }
}
