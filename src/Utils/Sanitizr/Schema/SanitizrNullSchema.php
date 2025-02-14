<?php

namespace Nebalus\Webapi\Utils\Sanitizr\Schema;

use Nebalus\Webapi\Utils\Sanitizr\Exception\SanitizValidationException;

class SanitizrNullSchema extends AbstractSanitizrSchema
{
    protected function parseValue(mixed $input): null
    {
        if (! is_null($input)) {
            throw new SanitizValidationException('Not a null value');
        }

        return null;
    }
}
