<?php

namespace Nebalus\Webapi\Api\Module\Linktree\Delete;

use Nebalus\Sanitizr\Sanitizr as S;
use Nebalus\Webapi\Api\AbstractValidator;
use Nebalus\Webapi\Api\RequestParamTypes;
use Nebalus\Webapi\Value\Module\Referral\ReferralName;
use Nebalus\Webapi\Value\Url;

class DeleteLinktreeValidator extends AbstractValidator
{
    public function __construct()
    {
        parent::__construct(S::object([]));
    }

    protected function onValidate(array $bodyData, array $queryParamsData, array $pathArgsData): void
    {
    }
}
