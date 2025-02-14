<?php

namespace Nebalus\Webapi\Value\Internal\Validation;

readonly class ValidatedData
{
    private function __construct(
        private array $bodyData,
        private array $queryParamsData,
        private array $pathArgsData,
    ) {
    }

    public static function from(
        array $bodyData,
        array $queryParamsData,
        array $pathArgsData
    ): self {
        return new self($bodyData, $queryParamsData, $pathArgsData);
    }

    public function getBodyData(): array
    {
        return $this->bodyData;
    }

    public function getQueryParamsData(): array
    {
        return $this->queryParamsData;
    }

    public function getPathArgsData(): array
    {
        return $this->pathArgsData;
    }
}
