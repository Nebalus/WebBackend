<?php

namespace Nebalus\Webapi\Value\Referral;

use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Nebalus\Webapi\Utils\Sanitizr\Sanitizr;

readonly class ReferralPointer
{
    private function __construct(
        private string $pointer
    ) {
    }

    /**
     * @throws ApiInvalidArgumentException
     */
    public static function from(string $pointer): self
    {
        $schema = Sanitizr::string()->url();
        $validData = $schema->safeParse($pointer);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException("Invalid referral pointer: " . $validData->getErrorMessage());
        }

        return new self($pointer);
    }

    public function asString(): string
    {
        return $this->pointer;
    }
}
