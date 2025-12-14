<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Repository\UserRepository;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\AccountRepository\MySqlAccountRepository;
use Nebalus\Webapi\Value\Account\InvitationToken\InvitationToken;
use Nebalus\Webapi\Value\User\AccessControl\Role\Role;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleCollection;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleId;
use Nebalus\Webapi\Value\User\User;
use Nebalus\Webapi\Value\User\UserEmail;
use Nebalus\Webapi\Value\User\UserId;
use Nebalus\Webapi\Value\User\Username;
use PDO;

readonly class MySqlUserRepository
{
    public function __construct(
        private PDO $pdo,
        private MySqlAccountRepository $accountRepository
    ) {
    }

    /**
     * @throws ApiException
     */
    public function registerUser(User $user, InvitationToken $invitationToken): User
    {
        $this->pdo->beginTransaction();
        $newUser = $this->insertUser($user);
        $newAccount = $this->accountRepository->insertAccount($newUser->getUserId());
        $preInvitationToken = $invitationToken->setInvitedId($newAccount);
        $this->accountRepository->updateInvitationToken($preInvitationToken);
        $this->pdo->commit();
        return $newUser;
    }

    /**
     * @throws ApiException
     */
    private function insertUser(User $user): User
    {
        $sql = <<<SQL
            INSERT INTO users
                (username, email, password, totp_secret_key, disabled, created_at, updated_at) 
            VALUES 
                (:username,:email,:password,:totp_secret_key,:disabled,:created_at,:updated_at)
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':username', $user->getUsername()->asString());
        $stmt->bindValue(':email', $user->getEmail()->asString());
        $stmt->bindValue(':password', $user->getPassword()->asString());
        $stmt->bindValue(':totp_secret_key', $user->getTotpSecretKey()->asString());
        $stmt->bindValue(':disabled', $user->isDisabled());
        $stmt->bindValue(':created_at', $user->getCreatedAtDate()->format("Y-m-d H:i:s"));
        $stmt->bindValue(':updated_at', $user->getUpdatedAtDate()->format("Y-m-d H:i:s"));
        $stmt->execute();

        $userToArray = $user->asArray();
        $userToArray["user_id"] = UserId::from($this->pdo->lastInsertId())->asInt();

        return User::fromArray($userToArray);
    }

    /**
     * @throws ApiException
     */
    public function findUserFromId(UserId $userId): ?User
    {
        $sql = <<<SQL
            SELECT 
                * 
            FROM users
            WHERE 
                users.user_id = :user_id
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId->asInt());
        $stmt->execute();

        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }

        return User::fromArray($data);
    }

    /**
     * @throws ApiException
     */
    public function findUserFromEmail(UserEmail $email): ?User
    {
        $sql = <<<SQL
            SELECT 
                * 
            FROM users 
            WHERE
                users.email = :email
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email->asString());
        $stmt->execute();

        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }

        return User::fromArray($data);
    }

    /**
     * @throws ApiException
     */
    public function findUserFromUsername(Username $username): ?User
    {
        $sql = <<<SQL
            SELECT
                * 
            FROM users
            WHERE 
                users.username = :username
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':username', $username->asString());
        $stmt->execute();

        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }

        return User::fromArray($data);
    }

    /**
     * @throws ApiException
     */
    public function getAllRolesFromUserByUserId(UserId $userId): RoleCollection
    {
        $sql = <<<SQL
            (
                SELECT
                    roles.role_id,
                    roles.name,
                    roles.description,
                    HEX(roles.color) AS color,
                    roles.access_level,
                    roles.applies_to_everyone,
                    roles.deletable,
                    roles.editable,
                    roles.disabled,
                    roles.created_at,
                    roles.updated_at
                FROM
                    user_role_map
                INNER JOIN roles ON roles.role_id = user_role_map.role_id
                WHERE user_role_map.user_id = :userId
            )
            UNION
            (
                SELECT
                    roles.role_id,
                    roles.name,
                    roles.description,
                    HEX(roles.color) AS color,
                    roles.access_level,
                    roles.applies_to_everyone,
                    roles.deletable,
                    roles.editable,
                    roles.disabled,
                    roles.created_at,
                    roles.updated_at
                FROM
                    roles
                WHERE roles.applies_to_everyone = 1
            )
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':userId', $userId->asInt(), PDO::PARAM_INT);
        $stmt->execute();

        $data = [];

        while ($row = $stmt->fetch()) {
            $data[] = Role::fromArray($row);
        }

        return RoleCollection::fromObjects(...$data);
    }

    public function insertRoleToUserByRoleId(UserId $userId, RoleId $roleId): bool
    {
        $sql = <<<SQL
            INSERT IGNORE INTO user_role_map
                (user_id, role_id) 
            VALUES 
                (:user_id,:role_id)
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId->asInt());
        $stmt->bindValue(':role_id', $roleId->asInt());
        $stmt->execute();

        return $stmt->rowCount() === 1;
    }

    public function removeRoleFromUserByRoleId(UserId $userId, RoleId $roleId): bool
    {
        $sql = <<<SQL
            DELETE FROM user_role_map 
            WHERE 
                user_id = :user_id 
                AND role_id = :role_id
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId->asInt());
        $stmt->bindValue(':role_id', $roleId->asInt());
        $stmt->execute();

        return $stmt->rowCount() === 1;
    }
}
