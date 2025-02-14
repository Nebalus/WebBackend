<?php

namespace Nebalus\Webapi\Utils\Sanitizr\Schema;

use Nebalus\Webapi\Utils\Sanitizr\Exception\SanitizValidationException;

class SanitizrArraySchema extends AbstractSanitizrSchema
{
    public function __construct(
        private readonly AbstractSanitizrSchema $schema
    ) {
    }

    protected function parseValue(mixed $input): array
    {
        if (! is_array($input)) {
            throw new SanitizValidationException('Not an array');
        }

        $result = [];

        foreach ($input as $v) {
            $result[] = $this->schema->parse($v);
        }

        return $result;
    }
}
