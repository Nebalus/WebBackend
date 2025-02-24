<?php

namespace Nebalus\Webapi\Api\Module\Referral\Get;

use Nebalus\Sanitizr\Sanitizr as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Value\Internal\Validation\ValidRequestData;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;

class GetReferralValidator extends AbstractValidator
{
    private ReferralCode $referralCode;

    public function __construct()
    {
        parent::__construct(S::object([
            "path_args" => S::object([
                'code' => S::string()->length(ReferralCode::LENGTH)->regex(ReferralCode::REGEX)
            ])
        ]));
    }

    protected function onValidate(ValidRequestData $request): void
    {
        $this->referralCode = ReferralCode::from($request->getPathArgsData()['code']);
    }

    public function getReferralCode(): ReferralCode
    {
        return $this->referralCode;
    }
}
