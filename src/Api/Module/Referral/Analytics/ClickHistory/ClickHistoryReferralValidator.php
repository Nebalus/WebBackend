<?php

namespace Nebalus\Webapi\Api\Module\Referral\Analytics\ClickHistory;

use Nebalus\Sanitizr\Sanitizr as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Value\Internal\Validation\ValidRequestData;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;

class ClickHistoryReferralValidator extends AbstractValidator
{
    private ReferralCode $referralCode;
    private int $range;

    public function __construct()
    {
        $rules = [
            "path_args" => S::object([
                'code' => S::string()->length(ReferralCode::LENGTH)->regex(ReferralCode::REGEX)
            ]),
            "query_params" => S::object([
                'range' => S::number()->integer()->positive()
            ])
        ];
        parent::__construct($rules);
    }

    protected function onValidate(ValidRequestData $request): void
    {
        $this->referralCode = ReferralCode::from($request->getPathArgsData()['code']);
        $this->range = $request->getQueryParamsData()['range'];
    }

    public function getReferralCode(): ReferralCode
    {
        return $this->referralCode;
    }

    public function getRange(): int
    {
        return $this->range;
    }
}
