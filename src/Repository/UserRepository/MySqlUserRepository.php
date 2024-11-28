<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Repository\UserRepository;

use Nebalus\Webapi\Exception\ApiDatabaseException;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\ID;
use Nebalus\Webapi\Value\User\User;
use Nebalus\Webapi\Value\User\UserEmail;
use Nebalus\Webapi\Value\User\Username;
use PDO;
use PDOException;

class MySqlUserRepository
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * @throws ApiException
     */
    public function insertUser(User $user): User
    {
        try {
            $sql = "INSERT INTO `users`(`username`, `email`, `password`, `totp_secret_key`, `description`, `is_admin`, `disabled`, `created_at`, `updated_at`) 
                                VALUES (:username,:email,:password,:totp_secret_key,:description,:is_admin,:disabled,:created_at,:updated_at)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':username', $user->getUsername()->asString());
            $stmt->bindValue(':email', $user->getEmail()->asString());
            $stmt->bindValue(':password', $user->getPassword()->asString());
            $stmt->bindValue(':totp_secret_key', $user->getTotpSecretKey()->asString());
            $stmt->bindValue(':description', $user->getDescription()->asString());
            $stmt->bindValue(':is_admin', $user->isAdmin());
            $stmt->bindValue(':disabled', $user->isDisabled());
            $stmt->bindValue(':created_at', $user->getCreatedAtDate()->format("Y-m-d H:i:s"));
            $stmt->bindValue(':updated_at', $user->getUpdatedAtDate()->format("Y-m-d H:i:s"));
            $stmt->execute();

            $userToArray = $user->toArray();
            $userToArray["user_id"] = ID::fromString($this->pdo->lastInsertId())->asInt();

            return User::fromDatabase($userToArray);
        } catch (PDOException $e) {
            throw new ApiDatabaseException(
                "Failed to insert a new user",
                500,
                $e
            );
        }
    }

    /**
     * @throws ApiException
     */
    public function findUserFromId(ID $userId): ?User
    {
        try {
            $sql = "SELECT * FROM `users` WHERE `user_id` = :user_id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':user_id', $userId->asInt());
            $stmt->execute();

            $data = $stmt->fetch();
            if (!$data) {
                return null;
            }

            return User::fromDatabase($data);
        } catch (PDOException $e) {
            throw new ApiDatabaseException(
                "Failed to retrieve user data from userid",
                500,
                $e
            );
        }
    }

    /**
     * @throws ApiException
     */
    public function findUserFromEmail(UserEmail $email): ?User
    {
        try {
            $sql = "SELECT * FROM `users` WHERE `email` = :email";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':email', $email->asString());
            $stmt->execute();

            $data = $stmt->fetch();
            if (!$data) {
                return null;
            }

            return User::fromDatabase($data);
        } catch (PDOException $e) {
            throw new ApiDatabaseException(
                "Failed to retrieve user data from email",
                500,
                $e
            );
        }
    }

    /**
     * @throws ApiException
     */
    public function findUserFromUsername(Username $username): ?User
    {
        try {
            $sql = "SELECT * FROM `users` WHERE `username` = :username";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':username', $username->asString());
            $stmt->execute();

            $data = $stmt->fetch();
            if (!$data) {
                return null;
            }

            return User::fromDatabase($data);
        } catch (PDOException $e) {
            throw new ApiDatabaseException(
                "Failed to retrieve user data from username",
                500,
                $e
            );
        }
    }
}
