<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Repository\UserRepository;

use Nebalus\Webapi\Exception\ApiDatabaseException;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\AbstractRepository;
use Nebalus\Webapi\Repository\AccountRepository\MySqlAccountRepository;
use Nebalus\Webapi\Value\Account\InvitationToken\InvitationToken;
use Nebalus\Webapi\Value\User\User;
use Nebalus\Webapi\Value\User\UserEmail;
use Nebalus\Webapi\Value\User\UserId;
use Nebalus\Webapi\Value\User\Username;
use PDO;
use PDOException;

class MySqlUserRepository extends AbstractRepository
{
    public function __construct(
        private PDO $pdo,
        private MySqlAccountRepository $accountRepository
    ) {
        parent::__construct($pdo);
    }

    /**
     * @throws ApiException
     */
    public function registerUser(User $user, InvitationToken $invitationToken): User
    {
        $this->pdo->beginTransaction();
        $newUser = $this->insertUser($user);
        $newAccount = $this->accountRepository->insertAccount($newUser->getUserId());
        $preInvitationToken = $invitationToken->setInvitedAccountId($newAccount);
        $this->accountRepository->updateInvitationToken($preInvitationToken);
        $this->pdo->commit();
        return $newUser;
    }

    /**
     * @throws ApiException
     */
    private function insertUser(User $user): User
    {
        $sql = "INSERT INTO users(username, email, password, totp_secret_key, disabled, created_at, updated_at) VALUES (:username,:email,:password,:totp_secret_key,:disabled,:created_at,:updated_at)";

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

        return User::fromDatabase($userToArray);
    }

    /**
     * @throws ApiException
     */
    public function findUserFromId(UserId $userId): ?User
    {
        $sql = "SELECT * FROM users INNER JOIN accounts ON accounts.user_id = users.user_id WHERE users.user_id = :user_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId->asInt());
        $stmt->execute();

        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }

        return User::fromDatabase($data);
    }

    /**
     * @throws ApiException
     */
    public function findUserFromEmail(UserEmail $email): ?User
    {
        $sql = "SELECT * FROM users INNER JOIN accounts ON accounts.user_id = users.user_id WHERE users.email = :email";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email->asString());
        $stmt->execute();

        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }

        return User::fromDatabase($data);
    }

    /**
     * @throws ApiException
     */
    public function findUserFromUsername(Username $username): ?User
    {
        $sql = "SELECT * FROM users INNER JOIN accounts ON accounts.user_id = users.user_id WHERE users.username = :username";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':username', $username->asString());
        $stmt->execute();

        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }

        return User::fromDatabase($data);
    }
}
