<?php

namespace Nebalus\Webapi\Api\Module\Referral\Analytics\ClickHistory;

use Nebalus\Webapi\Value\Internal\Result\Result;
use Nebalus\Webapi\Value\Internal\Result\ResultInterface;

class ClickHistoryReferralView
{
    public static function render(): ResultInterface
    {
        $fields = [];

        return Result::createSuccess("Referral history found", 200, $fields);
    }
}
