<?php

namespace Nebalus\Webapi\Value\User;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Nebalus\Webapi\Utils\Sanitizr\Sanitizr;

readonly class Username
{
    public const int MIN_LENGTH = 4;
    public const int MAX_LENGTH = 25;
    public const string REGEX = '/^[a-zA-Z]+$/';

    private function __construct(
        private string $username
    ) {
    }

    /**
     * @throws ApiException
     */
    public static function from(string $username): self
    {
        $schema = Sanitizr::string()->min(self::MIN_LENGTH)->max(self::MAX_LENGTH)->regex(self::REGEX);
        $validData = $schema->safeParse($username);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException('Invalid username: ' . $validData->getErrorMessage());
        }

        return new self($username);
    }

    public function asString(): string
    {
        return $this->username;
    }
}
