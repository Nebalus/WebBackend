<?php

namespace Nebalus\Webapi\Value\User;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Nebalus\Webapi\Utils\Sanitizr\Sanitizr;

readonly class UserEmail
{
    private function __construct(
        private string $email,
    ) {
    }

    /**
     * @throws ApiException
     */
    public static function from(string $email): self
    {
        $schema = Sanitizr::string()->email();
        $validData = $schema->safeParse($email);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException($validData->getErrorMessage());
        }

        return new self($email);
    }

    public function asString(): string
    {
        return $this->email;
    }
}
