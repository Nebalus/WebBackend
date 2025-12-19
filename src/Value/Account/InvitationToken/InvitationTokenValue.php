<?php

namespace Nebalus\Webapi\Value\Account\InvitationToken;

use Nebalus\Sanitizr\SanitizrStatic as S;
use Nebalus\Sanitizr\Schema\AbstractSanitizrSchema;
use Nebalus\Sanitizr\Trait\SanitizrValueObjectTrait;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

class InvitationTokenValue
{
    use SanitizrValueObjectTrait;

    public const string REGEX = "/^(([0-9]{4})-){4}([0-9]{4})$/";

    private function __construct(
        private readonly string $token
    ) {
    }

    protected static function defineSchema(): AbstractSanitizrSchema
    {
        return S::string()->regex(InvitationTokenValue::REGEX);
    }

    public static function create(): self
    {
        $fields = [
            rand(0, 9999),
            rand(0, 9999),
            rand(0, 9999),
            rand(0, 9999)
        ];

        return new self(sprintf(
            "%s-%s-%s-%s-%s",
            self::stringifyValue($fields[0]),
            self::stringifyValue($fields[1]),
            self::stringifyValue($fields[2]),
            self::stringifyValue($fields[3]),
            self::stringifyValue(self::calculateChecksum($fields)),
        ));
    }

    /**
     * @throws ApiException
     */
    public static function from(string $token): self
    {
        $schema = static::getSchema();
        $validData = $schema->safeParse($token);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException($validData->getErrorMessage());
        }

        $allFields = array_map('intval', explode('-', $validData->getValue()));
        $fields = array_slice($allFields, 0, 4);

        if (self::calculateChecksum($fields) !== $allFields[4]) {
            throw new ApiInvalidArgumentException('Invalid Token: Checksum does not match');
        }

        return new self($validData->getValue());
    }

    public function asString(): string
    {
        return $this->token;
    }

    private static function calculateChecksum(array $fields): int
    {
        $checksum = 0;
        foreach ($fields as $field) {
            $checksum += $field;
        }
        $checksum = abs($checksum);
        $checksum /= 4;
        return (int) floor($checksum);
    }

    private static function stringifyValue(string $value): string
    {
        if (strlen($value) === 1) {
            $value = "000" . $value;
        }

        if (strlen($value) === 2) {
            $value = "00" . $value;
        }

        if (strlen($value) === 3) {
            $value = "0" . $value;
        }
        return $value;
    }
}
