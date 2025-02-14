<?php

namespace Nebalus\Webapi\Utils\Sanitizr\Schema;

use Nebalus\Webapi\Utils\Sanitizr\Exception\SanitizValidationException;

class SanitizrObjectSchema extends AbstractSanitizrSchema
{
    public function __construct(
        private readonly array $schemas
    ) {
    }

    /**
     * @throws SanitizValidationException
     */
    protected function parseValue(mixed $input): array
    {
        if (is_object($input)) {
            $input = get_object_vars($input);
        }

        if (!is_array($input)) {
            throw new SanitizValidationException('Not an object');
        }

        $result = [];

        foreach ($this->schemas as $prop => $schema) {
            if ($schema instanceof AbstractSanitizrSchema) {
                if ($schema->isRequired() && isset($input[$prop]) === false) {
                    throw new SanitizValidationException($prop . " is required");
                }

                if ($schema instanceof SanitizrObjectSchema) {
                    if (isset($input[$prop]) && is_array($input[$prop])) {
                        $result[$prop] = $schema->parseValue($input[$prop]);
                    }
                    continue;
                }

                $result[$prop] = $schema->parse(
                    $input[$prop] ?? null
                );
            }
        }

        return $result;
    }
}
