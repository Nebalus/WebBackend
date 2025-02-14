<?php

namespace Nebalus\Webapi\Value\Referral;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Nebalus\Webapi\Utils\Sanitizr\Sanitizr;

class ReferralName
{
    public const int MAX_LENGTH = 32;
    public const string REGEX = '/^[a-zA-Z0-9 !@#$%^&*]*$/';

    private function __construct(
        private readonly ?string $referralName,
    ) {
    }
    /**
     * @throws ApiException
     */
    public static function from(?string $referralName): self
    {
        $schema = Sanitizr::string()->nullable()->max(self::MAX_LENGTH)->regex(self::REGEX);
        $validData = $schema->safeParse($referralName);

        if ($validData->isError()) {
            throw new ApiInvalidArgumentException("Invalid referral name: " . $validData->getErrorMessage());
        }

        return new self($referralName);
    }
    public function asString(): ?string
    {
        return $this?->referralName;
    }
}
