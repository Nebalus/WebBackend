<?php

namespace Nebalus\Webapi\Api\Module\Referral\Create;

use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\ReferralRepository\MySqlReferralRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;
use Nebalus\Webapi\Value\Result\ResultBuilder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\UserAccount;

readonly class CreateReferralService
{
    public function __construct(
        private MySQlReferralRepository $referralRepository,
        private CreateReferralResponder $responder,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(CreateReferralValidator $validator, UserAccount $requestingUser, UserPermissionIndex $userPerms): ResultInterface
    {
        if ($userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::FEATURE_REFERRAL_OWN_CREATE, true))) {
            $referralCode = ReferralCode::create();

            $this->referralRepository->insertReferral($requestingUser->getUserId(), $referralCode, $validator->getUrl(), $validator->getLabel(), $validator->isDisabled());
            $referral = $this->referralRepository->findReferralByCode($referralCode);

            return $this->responder->render($referral);
        }

        return ResultBuilder::buildNoPermissionResult();
    }
}
