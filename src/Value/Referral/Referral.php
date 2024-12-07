<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Value\Referral;

use DateMalformedStringException;
use DateTimeImmutable;
use Nebalus\Webapi\Exception\ApiDateMalformedStringException;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\ID;
use Nebalus\Webapi\Value\User\UserId;

readonly class Referral
{
    private function __construct(
        private ReferralId $referralId,
        private UserId $userId,
        private ReferralCode $code,
        private string $pointer,
        private bool $disabled,
        private DateTimeImmutable $createdAtDate,
        private DateTimeImmutable $updatedAtDate,
    ) {
    }

    /**
     * @throws ApiException
     */
    public static function fromArray(array $data): self
    {
        try {
            $createdAtDate = new DateTimeImmutable($data["created_at"]);
            $updatedAtDate = new DateTimeImmutable($data["updated_at"]);
        } catch (DateMalformedStringException $exception) {
            throw new ApiDateMalformedStringException($exception);
        }

        $referralId = ReferralId::from($data["referral_id"]);
        $userId = UserId::from($data["user_id"]);
        $code = ReferralCode::from($data["code"]);
        $pointer = $data["pointer"];
        $disabled = (bool) $data["disabled"];

        return new self(
            $referralId,
            $userId,
            $code,
            $pointer,
            $disabled,
            $createdAtDate,
            $updatedAtDate
        );
    }
    public function asArray(): array
    {
        return [
            "referral_id" => $this->referralId->asInt(),
            "user_id" => $this->userId->asInt(),
            "code" => $this->code->asString(),
            "pointer" => $this->pointer,
            "disabled" => $this->disabled,
            "created_at" => $this->createdAtDate->format(DATE_ATOM),
            "updated_at" => $this->updatedAtDate->format(DATE_ATOM),
        ];
    }

    public function getReferralId(): ReferralId
    {
        return $this->referralId;
    }
    public function getUserId(): UserId
    {
        return $this->userId;
    }
    public function getCode(): ReferralCode
    {
        return $this->code;
    }
    public function getPointer(): string
    {
        return $this->pointer;
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
