<?php

namespace Nebalus\Webapi\Value\User\Totp;

use Nebalus\Sanitizr\Sanitizr;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

readonly class TOTPCode
{
    public const string REGEX = '/^[\d]*$/';
    public const int LENGTH = 6;

    private function __construct(
        private string $code,
    ) {
    }

    /**
     * @throws ApiException
     */
    public static function from(string $code): self
    {
        $schema = Sanitizr::string()->length(self::LENGTH)->regex(self::REGEX);
        $validData = $schema->safeParse($code);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException('Invalid totp code: ' . $validData->getErrorMessage());
        }

        return new self($code);
    }

    public function asString(): string
    {
        return $this->code;
    }
}
