<?php

namespace Nebalus\Webapi\Value\User\Totp;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Nebalus\Webapi\Utils\Sanitizr\Sanitizr;

readonly class TOTPCode
{
    public const string REGEX = '/^[\d]{6}$/';

    private function __construct(
        private string $code,
    ) {
    }

    /**
     * @throws ApiException
     */
    public static function from(string $code): self
    {
        $schema = Sanitizr::string()->regex(self::REGEX);
        $validData = $schema->safeParse($code);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException('Invalid totp code: ' . $validData->getErrorMessage());
        }

        return new self($code);
    }

    private function asString(): string
    {
        return $this->code;
    }
}
