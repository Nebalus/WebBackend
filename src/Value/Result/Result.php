<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Value\Result;

use Fig\Http\Message\StatusCodeInterface;
use InvalidArgumentException;
use Nebalus\Webapi\Slim\ResultInterface;

readonly class Result implements ResultInterface
{
    private function __construct(
        private bool $success,
        private ?string $message,
        private int $statusCode,
        private array $payload
    ) {
    }

    protected static function from(bool $success, ?string $message, int $statusCode, array $fields): static
    {
        if ($statusCode < 100 || $statusCode > 599) {
            throw new InvalidArgumentException("Code '$statusCode' is not a valid http status code!", StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }

        $payload = [
            'success' => $success,
            'message' => $message,
            'status_code' => $statusCode,
            'payload' => $fields,
        ];

        return new static($success, $message, $statusCode, $payload);
    }

    public static function createSuccess(?string $message, int $statusCode = StatusCodeInterface::STATUS_OK, array $fields = []): static
    {
        return static::from(true, $message, $statusCode, $fields);
    }

    public static function createError(?string $message, int $statusCode = StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, array $fields = []): static
    {
        return static::from(false, $message, $statusCode, $fields);
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getPayloadAsJson(): string
    {
        return json_encode($this->payload);
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function isSuccessful(): bool
    {
        return $this->success;
    }
}
