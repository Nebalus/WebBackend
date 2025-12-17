<?php

namespace Nebalus\Webapi\Api\Admin\User\GetAll;

use Nebalus\Webapi\Config\Types\PermissionNodeTypes;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\UserRepository\MySqlUserRepository;
use Nebalus\Webapi\Slim\ResultInterface;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionAccess;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;

readonly class GetAllUserService
{
    public function __construct(
        private GetAllUserResponder $responder,
        private MySqlUserRepository $userRepository,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function execute(UserPermissionIndex $userPerms): ResultInterface
    {
        if ($userPerms->hasAccessTo(PermissionAccess::from(PermissionNodeTypes::ADMIN_USER, true))) {
            $roles = $this->userRepository->get();
            return $this->responder->render($roles);
        }
    }
}
