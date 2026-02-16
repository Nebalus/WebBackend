<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Repository\UserRepository;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\AccountRepository\MySqlAccountRepository;
use Nebalus\Webapi\Value\Account\InvitationToken\InvitationToken;
use Nebalus\Webapi\Value\User\AccessControl\Role\Role;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleCollection;
use Nebalus\Webapi\Value\User\AccessControl\Role\RoleId;
use Nebalus\Webapi\Value\User\UserAccount;
use Nebalus\Webapi\Value\User\Authentication\UserPassword;
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
    public function registerUser(UserAccount $user, InvitationToken $invitationToken): UserAccount
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
    private function insertUser(UserAccount $user): UserAccount
    {
        $sql = <<<SQL
            INSERT INTO users
                (username, profile_image_id, email, password, totp_secret_key, email_verified, disabled, disabled_by, disabled_reason, disabled_at, created_at, password_updated_at) 
            VALUES 
                (:username, :profile_image_id, :email, :password, :totp_secret_key, :email_verified, :disabled, :disabled_by, :disabled_reason, :disabled_at, :created_at, :password_updated_at)
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':username', $user->getUsername()->asString());
        $stmt->bindValue(':profile_image_id', $user->getProfileImageId(), $user->getProfileImageId() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':email', $user->getEmail()->asString());
        $stmt->bindValue(':password', $user->getPassword()->asString());
        $stmt->bindValue(':totp_secret_key', $user->getTotpSecretKey()->asString());
        $stmt->bindValue(':email_verified', $user->isEmailVerified());
        $stmt->bindValue(':disabled', $user->isDisabled());
        $stmt->bindValue(':disabled_by', $user->getDisabledBy()?->asInt(), $user->getDisabledBy() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':disabled_reason', $user->getDisabledReason(), $user->getDisabledReason() === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':disabled_at', $user->getDisabledAt()?->format("Y-m-d H:i:s"), $user->getDisabledAt() === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':created_at', $user->getCreatedAtDate()->format("Y-m-d H:i:s"));
        $stmt->bindValue(':password_updated_at', $user->getPasswordUpdatedAtDate()->format("Y-m-d H:i:s"));
        $stmt->execute();

        $userToArray = $user->asArray();
        $userToArray["user_id"] = UserId::from($this->pdo->lastInsertId())->asInt();

        return UserAccount::fromArray($userToArray);
    }

    /**
     * @throws ApiException
     */
    public function findUserFromId(UserId $userId): ?UserAccount
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

        return UserAccount::fromArray($data);
    }

    /**
     * @throws ApiException
     */
    public function findUserFromEmail(UserEmail $email): ?UserAccount
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

        return UserAccount::fromArray($data);
    }

    /**
     * @throws ApiException
     */
    public function findUserFromUsername(Username $username): ?UserAccount
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

        return UserAccount::fromArray($data);
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

    /**
     * @return UserAccount[]
     * @throws ApiException
     */
    public function getAllUsers(): array
    {
        $sql = "SELECT * FROM users";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $users = [];
        while ($row = $stmt->fetch()) {
            $users[] = UserAccount::fromArray($row);
        }

        return $users;
    }

    public function updateUsername(UserId $userId, Username $username): bool
    {
        $sql = <<<SQL
            UPDATE users 
            SET username = :username 
            WHERE user_id = :user_id
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':username', $username->asString());
        $stmt->bindValue(':user_id', $userId->asInt());
        $stmt->execute();

        return $stmt->rowCount() === 1;
    }

    public function updateEmail(UserId $userId, UserEmail $email): bool
    {
        $sql = <<<SQL
            UPDATE users 
            SET email = :email, email_verified = 0 
            WHERE user_id = :user_id
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email->asString());
        $stmt->bindValue(':user_id', $userId->asInt());
        $stmt->execute();

        return $stmt->rowCount() === 1;
    }

    public function updatePassword(UserId $userId, UserPassword $password): bool
    {
        $sql = <<<SQL
            UPDATE users 
            SET password = :password, password_updated_at = NOW() 
            WHERE user_id = :user_id
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':password', $password->asString());
        $stmt->bindValue(':user_id', $userId->asInt());
        $stmt->execute();

        return $stmt->rowCount() === 1;
    }

    public function updateProfileImageId(UserId $userId, int $profileImageId): bool
    {
        $sql = <<<SQL
            UPDATE users 
            SET profile_image_id = :profile_image_id 
            WHERE user_id = :user_id
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':profile_image_id', $profileImageId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId->asInt());
        $stmt->execute();

        return $stmt->rowCount() === 1;
    }

    public function disableUser(UserId $userId, UserId $disabledBy, string $reason): bool
    {
        $sql = <<<SQL
            UPDATE users 
            SET disabled = 1, disabled_by = :disabled_by, disabled_reason = :disabled_reason, disabled_at = NOW() 
            WHERE user_id = :user_id
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':disabled_by', $disabledBy->asInt());
        $stmt->bindValue(':disabled_reason', $reason);
        $stmt->bindValue(':user_id', $userId->asInt());
        $stmt->execute();

        return $stmt->rowCount() === 1;
    }

    public function enableUser(UserId $userId): bool
    {
        $sql = <<<SQL
            UPDATE users 
            SET disabled = 0, disabled_by = NULL, disabled_reason = NULL, disabled_at = NULL 
            WHERE user_id = :user_id
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId->asInt());
        $stmt->execute();

        return $stmt->rowCount() === 1;
    }

    public function deleteUser(UserId $userId): bool
    {
        $sql = <<<SQL
            DELETE FROM users 
            WHERE user_id = :user_id
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId->asInt());
        $stmt->execute();

        return $stmt->rowCount() === 1;
    }

    public function updateEmailVerified(UserId $userId, bool $emailVerified): bool
    {
        $sql = <<<SQL
            UPDATE users 
            SET email_verified = :email_verified 
            WHERE user_id = :user_id
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email_verified', $emailVerified, PDO::PARAM_BOOL);
        $stmt->bindValue(':user_id', $userId->asInt());
        $stmt->execute();

        return $stmt->rowCount() === 1;
    }
}
