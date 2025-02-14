<?php

namespace Nebalus\Webapi\Api\Module\Referral\Analytics\ClickHistory;

use Nebalus\Webapi\Value\Internal\Result\ResultInterface;
use Nebalus\Webapi\Value\User\User;

class ClickHistoryReferralService
{


    public function execute(ClickHistoryReferralValidator $validator, User $user): ResultInterface
    {

        return ClickHistoryReferralView::render();
    }
}
