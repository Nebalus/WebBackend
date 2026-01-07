<?php

namespace Nebalus\Webapi\Api\Module\Referral\Analytics\Click;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Referral\Referral;
use Nebalus\Webapi\Value\Result\Result;

class ClickReferralResponder
{
    public function render(Referral $referral): ResultInterface
    {
        $fields = [
            "url" => $referral->getUrl()->asString()
        ];

        return Result::createSuccess("Referral found", StatusCodeInterface::STATUS_OK, $fields);
    }
}
