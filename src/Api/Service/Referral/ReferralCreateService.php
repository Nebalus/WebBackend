<?php

namespace Nebalus\Webapi\Api\Service\Referral;

use Nebalus\Webapi\Api\Filter\Referral\ReferralCreateFilter;
use Nebalus\Webapi\Api\View\Referral\ReferralCreateView;
use Nebalus\Webapi\Repository\ReferralRepository\MySqlReferralRepository;
use Nebalus\Webapi\Value\Result\ResultInterface;

readonly class ReferralCreateService
{
    public function __construct(
        private MySQlReferralRepository $referralRepository,
    ) {
    }

    public function execute(array $params): ResultInterface
    {

        return ReferralCreateView::render();
    }
}
