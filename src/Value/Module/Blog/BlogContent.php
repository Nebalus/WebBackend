<?php

namespace Nebalus\Webapi\Value\Module\Blog;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

class BlogContent
{
    use SanitizrValueObjectTrait;

    public const string REGEX = '/^[a-zA-Z0-9\s!@#$%^&*]*$/';

    private function __construct(
        private readonly string $blogContent,
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::string()->regex(self::REGEX);
    }

    /**
     * @throws ApiException
     */
    public static function from(?string $blogContent): self
    {
        $schema = static::getSchema();
        $validData = $schema->safeParse($blogContent);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException("Invalid blog content: " . $validData->getErrorMessage());
        }

        return new self($validData->getValue());
    }
    public function asString(): string
    {
        return $this->blogContent;
    }
}
