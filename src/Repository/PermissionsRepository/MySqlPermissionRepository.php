<?php

namespace Nebalus\Webapi\Repository\PermissionsRepository;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Nebalus\Webapi\Value\User\AccessControl\Permission\Permission;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionCollection;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionId;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionNode;
use PDO;

readonly class MySqlPermissionRepository
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    /**
     * @throws ApiException
     */
    public function getAllPermissions(): PermissionCollection
    {
        $sql = <<<SQL
            SELECT 
                * 
            FROM permissions
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $data = [];

        while ($row = $stmt->fetch()) {
            $data[] = Permission::fromArray($row);
        }

        return PermissionCollection::fromObjects(...$data);
    }

    /**
     * @throws ApiInvalidArgumentException
     * @throws ApiException
     */
    public function findPrivilegeByNode(PermissionNode $node): ?Permission
    {
        $sql = <<<SQL
            SELECT
                *
            FROM permissions 
            WHERE 
                node = :node
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":node", $node->asString());
        $stmt->execute();

        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return Permission::fromArray($data);
    }

    /**
     * @throws ApiInvalidArgumentException
     * @throws ApiException
     */
    public function findPermissionByPermissionId(PermissionId $permissionId): ?Permission
    {
        $sql = <<<SQL
            SELECT 
                * 
            FROM permissions
            WHERE 
                permission_id = :permission_id
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":permission_id", $permissionId->asInt());
        $stmt->execute();

        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return Permission::fromArray($data);
    }
}
