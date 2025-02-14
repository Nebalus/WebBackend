<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Value\Internal\Result;

interface ResultInterface
{
    public function getPayload(): array;
    public function getMessage(): ?string;
    public function getStatusCode(): int;
    public function isSuccessful(): bool;
}
