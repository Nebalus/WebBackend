<?php

namespace Nebalus\Webapi\Utils\Sanitizr\Schema;

use Nebalus\Webapi\Utils\Sanitizr\Exception\SanitizValidationException;

class SanitizrBooleanSchema extends AbstractSanitizrSchema
{
    /**
     * @throws SanitizValidationException
     */
    protected function parseValue($input): bool
    {
        if (! is_bool($input)) {
            throw new SanitizValidationException('Not a boolean value');
        }

        return $input;
    }
}
