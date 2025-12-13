<?php

namespace Nebalus\Webapi\Repository\RoleRepository;

use Exception;
use Nebalus\Webapi\Exception\ApiDateMalformedStringException;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Exception\ApiInvalidArgumentException;
use Nebalus\Webapi\Repository\UserRepository\MySqlUserRepository;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionNodeCollection;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLink;
use Nebalus\Webapi\Value\User\AccessControl\Permission\PermissionRoleLinkCollection;
use Nebalus\Webapi\Value\User\AccessControl\Permission\UserPermissionIndex;
use Nebalus\Webapi\Value\User\AccessControl\Role\Role;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleAccessLevel;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleCollection;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleDescription;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleHexColor;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleId;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleName;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleSortedCollection;
use Nebalus\Webapi\Value\User\UserId;
use PDO;
use PDOException;

readonly class MySqlRoleRepository
{
    public function __construct(
        private PDO $pdo,
        private MySqlUserRepository $userRepository,
    ) {
    }

    public function deletePermissionLinksByRoleId(RoleId $roleId, PermissionNodeCollection $permissionNodeCollection): void
    {
        try {
            $sql = <<<SQL
            DELETE FROM role_permission_map
            WHERE
                role_id = :role_id AND
                permission_id = (SELECT permission_id FROM permissions WHERE node = :node LIMIT 1)
        SQL;

            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare($sql);

            foreach ($permissionNodeCollection as $permissionNode) {
                $stmt->bindValue(':role_id', $roleId->asInt(), PDO::PARAM_INT);
                $stmt->bindValue(':node', $permissionNode->asString(), PDO::PARAM_STR);

                try {
                    $stmt->execute();
                } catch (PDOException $e) {
                    if ($e->getCode() === '23000') { // FK constraints failed
                        continue;
                    }
                    throw $e;
                }
            }

            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function upsertPermissionLinksToRoleByRoleId(RoleId $roleId, PermissionRoleLinkCollection $permissionRoleLinkCollection): void
    {
        try {
            $sql = <<<SQL
            INSERT INTO role_permission_map(
                role_id,
                permission_id,
                allow_all_sub_permissions,
                `value`
            )
            VALUES
                (:role_id, (SELECT permission_id FROM permissions WHERE node = :node LIMIT 1), :allow_all_sub_permissions, :value)
            ON DUPLICATE KEY UPDATE
                allow_all_sub_permissions = values(allow_all_sub_permissions),
                `value` = values(`value`)
        SQL;

            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare($sql);

            foreach ($permissionRoleLinkCollection as $permissionRoleLink) {
                $stmt->bindValue(':role_id', $roleId->asInt(), PDO::PARAM_INT);
                $stmt->bindValue(':node', $permissionRoleLink->getNode()->asString(), PDO::PARAM_STR);
                $stmt->bindValue(':allow_all_sub_permissions', $permissionRoleLink->getMetadata()->allowAllSubPermissions(), PDO::PARAM_BOOL);
                $stmt->bindValue(':value', $permissionRoleLink->getMetadata()->getValue()?->asInt(), PDO::PARAM_INT);

                try {
                    $stmt->execute();
                } catch (PDOException $e) {
                    if ($e->getCode() === '23000') { // FK constraints failed
                        continue;
                    }
                    throw $e;
                }
            }

            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * @throws ApiException
     * @throws Exception
     */
    public function getPermissionIndexFromUserId(UserId $userId): UserPermissionIndex
    {
        // Sorts the Roles by AccessLevel
        $unsortedRolesArray = $this->userRepository->getAllRolesFromUserByUserId($userId)->toArray();
        usort($unsortedRolesArray, function (Role $a, Role $b) {
            return $b->getAccessLevel() <=> $a->getAccessLevel();
        });
        $sortedRoles = RoleCollection::fromObjects(...$unsortedRolesArray);

        // Generates an UserPermissionIndex
        $sortedRoleLinkCollections = [];
        foreach ($sortedRoles as $role) {
            $sortedRoleLinkCollections[] = $this->getAllPermissionLinksByRoleId($role->getRoleId());
        }
        return UserPermissionIndex::fromPermissionRoleLinkCollections(...$sortedRoleLinkCollections);
    }

    /**
     * @throws ApiException
     */
    public function getAllPermissionLinksByRoleId(RoleId $roleId): PermissionRoleLinkCollection
    {
        $sql = <<<SQL
            SELECT 
                permissions.node AS node,
                permissions.default_value AS default_value,
                role_permission_map.allow_all_sub_permissions,
                role_permission_map.value
            FROM `role_permission_map` 
                INNER JOIN permissions 
                    ON permissions.permission_id = role_permission_map.permission_id  
            WHERE role_id = :roleId
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":roleId", $roleId->asInt(), PDO::PARAM_INT);
        $stmt->execute();

        $data = [];

        while ($row = $stmt->fetch()) {
            if ($row['value'] === null) {
                $row['value'] = $row['default_value'] ?? null;
            }

            $data[] = PermissionRoleLink::fromArray($row);
        }

        return PermissionRoleLinkCollection::fromObjects(...$data);
    }

    /**
     * @throws ApiInvalidArgumentException
     * @throws ApiDateMalformedStringException
     * @throws ApiException
     */
    public function getAllRoles(): RoleCollection
    {
        $sql = <<<SQL
            SELECT 
                role_id,
                name,
                description,
                HEX(color) AS color,
                access_level,
                applies_to_everyone,
                deletable,
                editable,
                disabled,
                created_at,
                updated_at
            FROM roles
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $data = [];

        while ($row = $stmt->fetch()) {
            $data[] = Role::fromArray($row);
        }

        return RoleCollection::fromObjects(...$data);
    }

    /**
     * @throws ApiException
     */
    public function insertRole(Role $role): Role
    {
        $sql = <<<SQL
            INSERT INTO roles
                (name, description, color, access_level, applies_to_everyone, deletable, editable, disabled, created_at, updated_at)
            VALUES 
                (:name, :description, UNHEX(:color), :access_level, :applies_to_everyone, :deletable, :editable, :disabled, :created_at, :updated_at)
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':name', $role->getName()->asString());
        $stmt->bindValue(':description', $role->getDescription()?->asString());
        $stmt->bindValue(':color', $role->getColor()->asString());
        $stmt->bindValue(':access_level', $role->getAccessLevel()->asInt(), PDO::PARAM_INT);
        $stmt->bindValue(':applies_to_everyone', $role->appliesToEveryone(), PDO::PARAM_BOOL);
        $stmt->bindValue(':deletable', $role->isDeletable(), PDO::PARAM_BOOL);
        $stmt->bindValue(':editable', $role->isEditable(), PDO::PARAM_BOOL);
        $stmt->bindValue(':disabled', $role->isDisabled(), PDO::PARAM_BOOL);
        $stmt->bindValue(':created_at', $role->getCreatedAtDate()->format("Y-m-d H:i:s"));
        $stmt->bindValue(':updated_at', $role->getUpdatedAtDate()->format("Y-m-d H:i:s"));
        $stmt->execute();

        $roleToArray = $role->asArray();
        $roleToArray["role_id"] = RoleId::from($this->pdo->lastInsertId())->asInt();

        return Role::fromArray($roleToArray);
    }

    /**
     * @throws ApiInvalidArgumentException
     * @throws ApiException
     * @throws ApiDateMalformedStringException
     */
    public function updateRoleByRoleId(RoleId $roleId, RoleName $name, ?RoleDescription $description, RoleHexColor $color, RoleAccessLevel $accessLevel, bool $appliesToEveryone, bool $disabled): ?Role
    {
        $sql = <<<SQL
            UPDATE roles
            SET
                name = :name,
                description = :description,
                color = UNHEX(:color),
                access_level = :access_level,
                applies_to_everyone = :applies_to_everyone,
                disabled = :disabled
            WHERE
                role_id = :role_id
                AND editable = true
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':role_id', $roleId->asInt(), PDO::PARAM_INT);
        $stmt->bindValue(':name', $name->asString());
        $stmt->bindValue(':description', $description?->asString(), PDO::PARAM_STR);
        $stmt->bindValue(':color', $color->asString());
        $stmt->bindValue(':access_level', $accessLevel->asInt(), PDO::PARAM_INT);
        $stmt->bindValue(':applies_to_everyone', $appliesToEveryone, PDO::PARAM_BOOL);
        $stmt->bindValue(':disabled', $disabled, PDO::PARAM_BOOL);
        $stmt->execute();

        return $this->findRoleByRoleId($roleId);
    }

    public function deleteRoleByRoleId(RoleId $roleId): bool
    {
        $sql = <<<SQL
            DELETE FROM roles 
            WHERE 
                role_id = :role_id 
                AND deletable = true
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':role_id', $roleId->asInt());
        $stmt->execute();

        return $stmt->rowCount() === 1;
    }

    /**
     * @throws ApiInvalidArgumentException
     * @throws ApiException
     * @throws ApiDateMalformedStringException
     */
    public function findRoleByRoleId(RoleId $roleId): ?Role
    {
        $sql = <<<SQL
            SELECT 
                role_id,
                name,
                description,
                HEX(color) AS color,
                access_level,
                applies_to_everyone,
                deletable,
                editable,
                disabled,
                created_at,
                updated_at
            FROM roles
            WHERE 
                role_id = :role_id
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':role_id', $roleId->asInt());
        $stmt->execute();

        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return Role::fromArray($data);
    }
}
