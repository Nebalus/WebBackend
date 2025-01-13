<?php

namespace Nebalus\Webapi\Utils\Sanitizr\Schema;

use Nebalus\Webapi\Utils\Sanitizr\Exception\SanitizValidationException;
use Nebalus\Webapi\Utils\Sanitizr\Value\SafeParsedData;

abstract class AbstractSanitizrSchema
{
    private array $effectQueue = [];
    private bool $isRequired = false;
    private bool $isNullable = false;
    private mixed $defaultValue;

    protected function addEffect(callable $callable): void
    {
        $this->effectQueue[] = [$callable];
    }

    public function required(): static
    {
        $this->isRequired = true;
        return $this;
    }

    public function nullable(): static
    {
        $this->isNullable = true;
        return $this;
    }

    public function default(mixed $value): static
    {
        $this->defaultValue = $value;
        return $this;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    /**
     * @throws SanitizValidationException
     */
    public function parse(mixed $input): mixed
    {
        if ($this->isNullable && is_null($input)) {
            return null;
        }

        if (is_null($input) && isset($this->defaultValue)) {
            return $this->defaultValue;
        }

        $parsedValue = $this->parseValue($input);

        foreach ($this->effectQueue as $effect) {
            $effect[0]($parsedValue);
        }

        return $parsedValue;
    }

    public function safeParse(mixed $input): SafeParsedData
    {
        try {
            $result = $this->parse($input);
            return SafeParsedData::from(true, $result, null);
        } catch (SanitizValidationException $e) {
            return SafeParsedData::from(false, null, $e->getMessage());
        }
    }

    /**
     * @throws SanitizValidationException
     */
    abstract protected function parseValue(mixed $input): mixed;
}
