<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Value\User;

use DateMalformedStringException;
use DateTimeImmutable;
use Nebalus\Webapi\Exception\ApiDateMalformedStringException;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\User\Authentication\Totp\TOTPSecretKey;
use Nebalus\Webapi\Value\User\Authentication\UserPassword;

readonly class UserAccount
{
    private function __construct(
        private ?UserId $userId,
        private Username $username,
        private ?int $profileImageId,
        private UserEmail $email,
        private UserPassword $password,
        private TOTPSecretKey $totpSecretKey,
        private bool $emailVerified,
        private bool $disabled,
        private ?UserId $disabledBy,
        private ?string $disabledReason,
        private ?DateTimeImmutable $disabledAt,
        private DateTimeImmutable $createdAtDate,
        private DateTimeImmutable $passwordUpdatedAtDate,
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
        $passwordUpdatedAtDate = new DateTimeImmutable();
        return self::from(null, $username, null, $email, $password, $totpSecretKey, false, false, null, null, null, $createdAtDate, $passwordUpdatedAtDate);
    }

    public static function from(
        ?UserId $userId,
        Username $username,
        ?int $profileImageId,
        UserEmail $email,
        UserPassword $password,
        TOTPSecretKey $totpSecretKey,
        bool $emailVerified,
        bool $disabled,
        ?UserId $disabledBy,
        ?string $disabledReason,
        ?DateTimeImmutable $disabledAt,
        DateTimeImmutable $createdAtDate,
        DateTimeImmutable $passwordUpdatedAtDate
    ): self {
        return new self($userId, $username, $profileImageId, $email, $password, $totpSecretKey, $emailVerified, $disabled, $disabledBy, $disabledReason, $disabledAt, $createdAtDate, $passwordUpdatedAtDate);
    }

    /**
     * @throws ApiException
     */
    public static function fromArray(array $data): self
    {
        try {
            $createdAtDate = new DateTimeImmutable($data['created_at']);
            $passwordUpdatedAtDate = new DateTimeImmutable($data['password_updated_at']);
            $disabledAt = empty($data['disabled_at']) ? null : new DateTimeImmutable($data['disabled_at']);
            return new self(
                empty($data['user_id']) ? null : UserId::from($data['user_id']),
                Username::from($data['username']),
                empty($data['profile_image_id']) ? null : (int) $data['profile_image_id'],
                UserEmail::from($data['email']),
                UserPassword::fromHash($data['password']),
                TOTPSecretKey::from($data['totp_secret_key']),
                (bool) $data['email_verified'],
                (bool) $data['disabled'],
                empty($data['disabled_by']) ? null : UserId::from($data['disabled_by']),
                empty($data['disabled_reason']) ? null : (string) $data['disabled_reason'],
                $disabledAt,
                $createdAtDate,
                $passwordUpdatedAtDate
            );
        } catch (DateMalformedStringException $exception) {
            throw new ApiDateMalformedStringException($exception);
        }
    }

    public function asArray(): array
    {
        return [
            'user_id' => $this->userId?->asInt(),
            'username' => $this->username->asString(),
            'profile_image_id' => $this->profileImageId,
            'email' => $this->email->asString(),
            'password' => $this->password->asString(),
            'totp_secret_key' => $this->totpSecretKey->asString(),
            'email_verified' => $this->emailVerified,
            'disabled' => $this->disabled,
            'disabled_by' => $this->disabledBy?->asInt(),
            'disabled_reason' => $this->disabledReason,
            'disabled_at' => $this->disabledAt?->format(DATE_ATOM),
            'created_at' => $this->createdAtDate->format(DATE_ATOM),
            'password_updated_at' => $this->passwordUpdatedAtDate->format(DATE_ATOM),
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

    public function getProfileImageId(): ?int
    {
        return $this->profileImageId;
    }

    public function isEmailVerified(): bool
    {
        return $this->emailVerified;
    }

    public function getDisabledBy(): ?UserId
    {
        return $this->disabledBy;
    }

    public function getDisabledReason(): ?string
    {
        return $this->disabledReason;
    }

    public function getDisabledAt(): ?DateTimeImmutable
    {
        return $this->disabledAt;
    }

    public function getCreatedAtDate(): DateTimeImmutable
    {
        return $this->createdAtDate;
    }

    public function getPasswordUpdatedAtDate(): DateTimeImmutable
    {
        return $this->passwordUpdatedAtDate;
    }
}
