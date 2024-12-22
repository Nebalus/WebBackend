<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Value\User;

use DateMalformedStringException;
use DateTimeImmutable;
use Nebalus\Webapi\Exception\ApiDateMalformedStringException;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\ID;
use Nebalus\Webapi\Value\User\Totp\TOTPSecretKey;

readonly class User
{
    private function __construct(
        private ?UserId $userId,
        private Username $username,
        private UserEmail $email,
        private UserPassword $password,
        private TOTPSecretKey $totpSecretKey,
        private bool $disabled,
        private DateTimeImmutable $createdAtDate,
        private DateTimeImmutable $updatedAtDate,
    ) {
    }

    /**
     * @throws ApiException
     */
    public static function create(
        Username $username,
        UserEmail $email,
        UserPassword $password
    ): self {
        $totpSecretKey = TOTPSecretKey::create();
        $createdAtDate = new DateTimeImmutable();
        $updatedAtDate = new DateTimeImmutable();
        return self::from(null, $username, $email, $password, $totpSecretKey, false, $createdAtDate, $updatedAtDate);
    }

    public static function from(
        ?UserId $userId,
        Username $username,
        UserEmail $email,
        UserPassword $password,
        TOTPSecretKey $totpSecretKey,
        bool $disabled,
        DateTimeImmutable $createdAtDate,
        DateTimeImmutable $updatedAtDate
    ): self {
        return new self($userId, $username, $email, $password, $totpSecretKey, $disabled, $createdAtDate, $updatedAtDate);
    }

    /**
     * @throws ApiException
     */
    public static function fromDatabase(array $data): self
    {
        try {
            $createdAtDate = new DateTimeImmutable($data['created_at']);
            $updatedAtDate = new DateTimeImmutable($data['updated_at']);
        } catch (DateMalformedStringException $exception) {
            throw new ApiDateMalformedStringException($exception);
        }

        $userId = empty($data['user_id']) ? null : UserId::from($data['user_id']);
        $username = Username::from($data['username']);
        $email = UserEmail::from($data['email']);
        $password = UserPassword::fromHash($data['password']);
        $totpSecretKey = TOTPSecretKey::from($data['totp_secret_key']);
        $disabled = (bool) $data['disabled'];

        return new self($userId, $username, $email, $password, $totpSecretKey, $disabled, $createdAtDate, $updatedAtDate);
    }

    /**
     * @throws ApiException
     */
    public static function fromArray(array $data): self
    {
        try {
            $createdAtDate = new DateTimeImmutable($data['created_at']);
            $updatedAtDate = new DateTimeImmutable($data['updated_at']);
        } catch (DateMalformedStringException $exception) {
            throw new ApiDateMalformedStringException($exception);
        }

        $userId = empty($data['user_id']) ? null : UserId::from($data['user_id']);
        $username = Username::from($data['username']);
        $email = UserEmail::from($data['email']);
        $password = UserPassword::fromHash($data['password']);
        $totpSecretKey = TOTPSecretKey::from($data['totp_secret_key']);
        $disabled = (bool) $data['disabled'];

        return new self($userId, $username, $email, $password, $totpSecretKey, $disabled, $createdAtDate, $updatedAtDate);
    }

    public function asArray(): array
    {
        return [
            'user_id' => $this->userId?->asInt(),
            'username' => $this->username->asString(),
            'email' => $this->email->asString(),
            'password' => $this->password->asString(),
            'totp_secret_key' => $this->totpSecretKey->asString(),
            'disabled' => $this->disabled,
            'created_at' => $this->createdAtDate->format(DATE_ATOM),
            'updated_at' => $this->updatedAtDate->format(DATE_ATOM),
        ];
    }

    public function getUserId(): ?UserId
    {
        return $this->userId;
    }

    public function getUsername(): Username
    {
        return $this->username;
    }

    public function getEmail(): UserEmail
    {
        return $this->email;
    }

    public function getPassword(): UserPassword
    {
        return $this->password;
    }

    public function getTotpSecretKey(): TOTPSecretKey
    {
        return $this->totpSecretKey;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function getCreatedAtDate(): DateTimeImmutable
    {
        return $this->createdAtDate;
    }

    public function getUpdatedAtDate(): DateTimeImmutable
    {
        return $this->updatedAtDate;
    }
}
