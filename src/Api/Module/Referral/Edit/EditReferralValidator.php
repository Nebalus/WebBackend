<?php

namespace Nebalus\Webapi\Api\Module\Referral\Edit;

use Nebalus\Sanitizr\Sanitizr as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Api\RequestParamTypes;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;
use Nebalus\Webapi\Value\Module\Referral\ReferralLabel;
use Nebalus\Webapi\Value\Url;

class EditReferralValidator extends AbstractValidator
{
    private ReferralCode $code;
    private Url $url;
    private ReferralLabel $label;
    private bool $disabled;

    public function __construct()
    {
        parent::__construct(S::object([
            RequestParamTypes::PATH_ARGS => S::object([
                'code' => S::string()->length(ReferralCode::LENGTH)->regex(ReferralCode::REGEX)
            ]),
            RequestParamTypes::BODY => S::object([
                'url' => S::string()->url(),
                'label' => S::string()->nullable(),
                'disabled' => S::boolean()->optional()->default(false),
            ])
        ]));
    }

    protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void
    {
        $this->code = ReferralCode::from($pathArgsData['code']);
        $this->url = Url::from($bodyData['url']);
        $this->label = ReferralLabel::from($bodyData['label']);
        $this->disabled = $bodyData['disabled'];
    }

    public function getCode(): ReferralCode
    {
        return $this->code;
    }

    public function getUrl(): Url
    {
        return $this->url;
    }

    public function getLabel(): ReferralLabel
    {
        return $this->label;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }
}
