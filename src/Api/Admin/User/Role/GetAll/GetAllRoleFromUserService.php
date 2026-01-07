<?php

namespace Nebalus\Webapi\Api\Admin\User\Role\GetAll;

use Exception;
use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Exception\ApiDateMalformedStringException;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Nebalus\Webapi\Repository\UserRepository\MySqlUserRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\Result\ResultBuilder;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;

readonly class GetAllRoleFromUserService
{
    public function __construct(
        private MySqlUserRepository $userRepository,
        private GetAllRoleFromUserResponder $responder
    ) {
    }

    /**
     * @throws ApiInvalidArgumentException
     * @throws ApiException
     * @throws ApiDateMalformedStringException
     * @throws Exception
     */
    public function execute(GetAllRoleFromUserValidator $validator, UserPermissionIndex $userPerms): ResultInterface
    {
        if (!$userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::ADMIN_ROLE, true))) {
            return ResultBuilder::buildNoPermissionResult();
        }

        $roles = $this->userRepository->getAllRolesFromUserByUserId($validator->getUserId());

        return $this->responder->render($roles);
    }
}
