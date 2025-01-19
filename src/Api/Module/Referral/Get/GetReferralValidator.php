<?php

namespace Nebalus\Webapi\Api\Module\Referral\Get;

use Nebalus\Sanitizr\Sanitizr as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Value\Internal\Validation\ValidatedData;
use Nebalus\Webapi\Value\Referral\ReferralCode;

class GetReferralValidator extends AbstractValidator
{
    private ReferralCode $referralCode;

    public function __construct()
    {
        $rules = [
            "path_args" => S::object([
                'code' => S::string()->required()->length(ReferralCode::LENGTH)->regex(ReferralCode::REGEX)
            ])
        ];
        parent::__construct($rules);
    }

    protected function onValidate(ValidatedData $validatedData): void
    {
        $this->referralCode = ReferralCode::from($validatedData->getPathArgsData()['code']);
    }

    public function getReferralCode(): ReferralCode
    {
        return $this->referralCode;
    }
}
