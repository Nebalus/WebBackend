<?php

namespace Nebalus\Webapi\Api\Module\Referral\Create;

use Nebalus\Webapi\Utils\Sanitizr\Sanitizr as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Value\Internal\Validation\ValidatedData;
use Nebalus\Webapi\Value\Referral\ReferralName;
use Nebalus\Webapi\Value\Referral\ReferralPointer;

class CreateReferralValidator extends AbstractValidator
{
    private ReferralName $referralName;
    private ReferralPointer $referralPointer;
    private bool $disabled;

    public function __construct()
    {
        $rules = [
            "body" => S::object([
                'name' => S::string()->required(),
                'pointer' => S::string()->required()->url(),
                'disabled' => S::boolean()->default(false),
            ])
        ];
        parent::__construct($rules);
    }

    protected function onValidate(ValidatedData $validatedData): void
    {
        $this->referralName = ReferralName::from($validatedData->getBodyData()['name']);
        $this->referralPointer = ReferralPointer::from($validatedData->getBodyData()['pointer']);
        $this->disabled = $validatedData->getBodyData()['disabled'];
    }

    public function getReferralName(): ReferralName
    {
        return $this->referralName;
    }

    public function getReferralPointer(): ReferralPointer
    {
        return $this->referralPointer;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }
}
