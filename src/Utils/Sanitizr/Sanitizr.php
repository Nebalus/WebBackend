<?php

namespace Nebalus\Webapi\Utils\Sanitizr;

use Nebalus\Webapi\Utils\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Webapi\Utils\Sanitizr\Schema\SanitizerArraySchema;
use Nebalus\Webapi\Utils\Sanitizr\Schema\SanitizrBooleanSchema;
use Nebalus\Webapi\Utils\Sanitizr\Schema\SanitizrNullSchema;
use Nebalus\Webapi\Utils\Sanitizr\Schema\SanitizrNumberSchema;
use Nebalus\Webapi\Utils\Sanitizr\Schema\SanitizrObjectSchema;
use Nebalus\Webapi\Utils\Sanitizr\Schema\SanitizrStringSchema;

class Sanitizr
{
    public static function boolean(): SanitizrBooleanSchema
    {
        return new SanitizrBooleanSchema();
    }

    public static function number(): SanitizrNumberSchema
    {
        return new SanitizrNumberSchema();
    }

    public static function string(): SanitizrStringSchema
    {
        return new SanitizrStringSchema();
    }

    public static function array(AbstractSanitizrSchema $schema): SanitizerArraySchema
    {
        return new SanitizerArraySchema($schema);
    }

    public static function object(array $schemas): SanitizrObjectSchema
    {
        return new SanitizrObjectSchema($schemas);
    }

    public static function null(): SanitizrNullSchema
    {
        return new SanitizrNullSchema();
    }
}
