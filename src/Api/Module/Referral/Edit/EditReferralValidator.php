<?php

namespace Nebalus\Webapi\Api\Module\Referral\Edit;

use Nebalus\Sanitizr\Sanitizr as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Api\RequestParamTypes;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;
use Nebalus\Webapi\Value\Module\Referral\ReferralName;
use Nebalus\Webapi\Value\Url;

class EditReferralValidator extends AbstractValidator
{
    private ReferralCode $referralCode;
    private Url $url;
    private ReferralName $name;
    private bool $disabled;

    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::PATH_ARGS => S::object([
                'code' => S::string()->length(ReferralCode::LENGTH)->regex(ReferralCode::REGEX)
            ]),
            RequestParamTypes::BODY => S::object([
                'url' => S::string()->url(),
                'name' => S::string()->nullable(),
                'disabled' => S::boolean()->optional()->default(false),
            ])
        ]));
    }

    protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void
    {
        $this->referralCode = ReferralCode::from($pathArgsData['code']);
        $this->url = Url::from($bodyData['url']);
        $this->name = ReferralName::from($bodyData['name']);
        $this->disabled = $bodyData['disabled'];
    }

    public function getReferralCode(): ReferralCode
    {
        return $this->referralCode;
    }

    public function getUrl(): Url
    {
        return $this->url;
    }

    public function getName(): ReferralName
    {
        return $this->name;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }
}
