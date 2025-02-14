<?php

namespace Nebalus\Webapi\Value\User;

use Nebalus\Webapi\Exception\ApiInvalidArgumentException;

readonly class UserAgent
{
    public const string REGEX = "/\((?<info>.*?)\)(\s|$)|(?<name>.*?)\/(?<version>.*?)(\s|$)/";

    private function __construct(
        private string $userAgent
    ) {
    }

    /**
     * @throws ApiInvalidArgumentException
     */
    public static function from(string $userAgent): self
    {
        if (preg_match(self::REGEX, $userAgent)) {
            throw new ApiInvalidArgumentException(
                'Invalid useragent'
            );
        }

        return new self($userAgent);
    }

    public function asString(): ?string
    {
        return $this->userAgent;
    }
}
