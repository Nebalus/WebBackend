<?php

namespace Nebalus\Webapi\Utils\Sanitizr\Schema;

use Nebalus\Webapi\Utils\Sanitizr\Exception\SanitizValidationException;

class SanitizrStringSchema extends AbstractSanitizrSchema
{
    public function length(int $length, string $message = 'Must be exact %s characters long'): static
    {
        $this->addEffect(function (string $input) use ($length, $message) {
            $inputLength = strlen($input);

            if ($inputLength !== $length) {
                throw new SanitizValidationException(sprintf($message, $length));
            }
        });

        return $this;
    }

    public function min(int $min, string $message = 'Must be %s or more characters long'): static
    {
        $this->addEffect(function (string $input) use ($min, $message) {
            $inputLength = strlen($input);

            if ($inputLength < $min) {
                throw new SanitizValidationException(sprintf($message, $min));
            }
        });

        return $this;
    }

    public function max(int $max, string $message = 'Must be %s or fewer characters long'): static
    {
        $this->addEffect(function (string $input) use ($max, $message) {
            $inputLength = strlen($input);

            if ($inputLength > $max) {
                throw new SanitizValidationException(sprintf($message, $max));
            }
        });

        return $this;
    }

    public function regex(string $pattern, string $message = 'Does not match the pattern'): static
    {
        $this->addEffect(function (string $input) use ($pattern, $message) {
            if (! preg_match($pattern, $input)) {
                throw new SanitizValidationException($message);
            }
        });

        return $this;
    }

    public function email(string $message = 'Not a valid email address'): static
    {
        $this->addEffect(function (string $input) use ($message) {
            if (! filter_var($input, FILTER_VALIDATE_EMAIL)) {
                throw new SanitizValidationException($message);
            }
        });

        return $this;
    }

    public function url(string $message = 'Not a valid URL'): static
    {
        $this->addEffect(function (string $input) use ($message) {
            if (! filter_var($input, FILTER_VALIDATE_URL)) {
                throw new SanitizValidationException($message);
            }
        });

        return $this;
    }

    public function startsWith(string $prefix, string $message = 'Does not start with required string'): static
    {
        $this->addEffect(function (string $input) use ($prefix, $message) {
            if (str_starts_with($input, $prefix) === false) {
                throw new SanitizValidationException(sprintf($message, $prefix));
            }
        });

        return $this;
    }

    public function endsWith(string $suffix, string $message = 'Does not end with required string'): static
    {
        $this->addEffect(function (string $input) use ($suffix, $message) {
            if (str_ends_with($input, $suffix) === false) {
                throw new SanitizValidationException(sprintf($message, $suffix));
            }
        });

        return $this;
    }

    /**
     * @throws SanitizValidationException
     */
    protected function parseValue(mixed $input): string
    {


        if (! is_string($input)) {
            throw new SanitizValidationException('Not a string value');
        }

        return $input;
    }
}
