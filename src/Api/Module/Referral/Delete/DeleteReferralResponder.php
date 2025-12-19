<?php

namespace Nebalus\Webapi\Api\Module\Referral\Delete;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\Result;

class DeleteReferralResponder
{
    public function render(): ResultInterface
    {
        return Result::createSuccess("Referral Deleted", StatusCodeInterface::STATUS_OK);
    }
}
