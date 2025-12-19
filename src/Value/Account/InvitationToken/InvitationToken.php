<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Value\Account\InvitationToken;

use DateMalformedStringException;
use DateTimeImmutable;
use Nebalus\Webapi\Exception\ApiDateMalformedStringException;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\Account\AccountId;

readonly class InvitationToken
{
    private function __construct(
        private AccountId $ownerId,
        private ?AccountId $invitedId,
        private InvitationTokenValue $invitationTokenValue,
        private DateTimeImmutable $createdAtDate,
        private ?DateTimeImmutable $usedAtDate
    ) {
    }

    public static function from(
        AccountId $ownerId,
        ?AccountId $invitedId,
        InvitationTokenValue $pureInvitationToken,
        DateTimeImmutable $creationTimestamp,
        ?DateTimeImmutable $usedTimestamp
    ): self {
        return new self(
            $ownerId,
            $invitedId,
            $pureInvitationToken,
            $creationTimestamp,
            $usedTimestamp
        );
    }

    /**
     * @throws ApiException
     */
    public static function fromDatabase(array $data): self
    {
        try {
            $createdAtDate = new DateTimeImmutable($data['created_at']);
            $usedAtDate = empty($data['used_at']) ? null : new DateTimeImmutable($data['used_at']);
        } catch (DateMalformedStringException $exception) {
            throw new ApiDateMalformedStringException($exception);
        }

        $ownerId = AccountId::from($data['owner_id']);
        $invitedId = empty($data['invited_id']) ? null : AccountId::from($data['invited_id']);
        $pureInvitationToken = InvitationTokenValue::from($data['token']);

        return new self($ownerId, $invitedId, $pureInvitationToken, $createdAtDate, $usedAtDate);
    }

    /**
     * @throws ApiException
     */
    public static function fromArray(array $data): self
    {
        try {
            $createdAtDate = new DateTimeImmutable($data['created_at']);
            $usedAtDate = empty($data['used_at']) ? null : new DateTimeImmutable($data['used_at']);
        } catch (DateMalformedStringException $exception) {
            throw new ApiDateMalformedStringException($exception);
        }

        $ownerId = AccountId::from($data['owner_id']);
        $invitedId = empty($data['invited_id']) ? null : AccountId::from($data['invited_id']);
        $pureInvitationToken = InvitationTokenValue::from($data['token']);

        return new self($ownerId, $invitedId, $pureInvitationToken, $createdAtDate, $usedAtDate);
    }

    public function asArray(): array
    {
        return [
            'owner_id' => $this->ownerId->asInt(),
            'invited_id' => $this->invitedId->asInt(),
            'token' => $this->invitationTokenValue->asString(),
            "created_at" => $this->createdAtDate->format(DATE_ATOM),
            "used_at" => $this->usedAtDate->format(DATE_ATOM),
        ];
    }

    public function getOwnerId(): AccountId
    {
        return $this->ownerId;
    }

    public function getInvitedId(): ?AccountId
    {
        return $this->invitedId;
    }

    public function getInvitationTokenValue(): InvitationTokenValue
    {
        return $this->invitationTokenValue;
    }

    public function getCreatedAtDate(): DateTimeImmutable
    {
        return $this->createdAtDate;
    }

    public function getUsedAtDate(): ?DateTimeImmutable
    {
        return $this->usedAtDate;
    }

    public function setInvitedId(AccountId $newInvitedAccountId): self
    {
        return new self($this->ownerId, $newInvitedAccountId, $this->invitationTokenValue, $this->createdAtDate, new DateTimeImmutable());
    }

    public function isExpired(): bool
    {
        return $this->invitedId !== null || $this->usedAtDate !== null;
    }
}
