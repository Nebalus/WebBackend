<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Value\Module\Referral;

use DateMalformedStringException;
use DateTimeImmutable;
use Nebalus\Webapi\Exception\ApiDateMalformedStringException;
use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\Url;
use Nebalus\Webapi\Value\User\UserId;

readonly class Referral
{
    private function __construct(
        private ReferralId $referralId,
        private UserId $ownerId,
        private ReferralCode $code,
        private Url $url,
        private ReferralName $name,
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
        $ownerId = UserId::from($data["owner_id"]);
        $code = ReferralCode::from($data["code"]);
        $url = Url::from($data["url"]);
        $name = ReferralName::from($data["name"]);
        $disabled = (bool) $data["disabled"];

        return new self(
            $referralId,
            $ownerId,
            $code,
            $url,
            $name,
            $disabled,
            $createdAtDate,
            $updatedAtDate
        );
    }
    public function asArray(): array
    {
        return [
            "referral_id" => $this->referralId->asInt(),
            "owner_id" => $this->ownerId->asInt(),
            "code" => $this->code->asString(),
            "url" => $this->url->asString(),
            "name" => $this->name->asString(),
            "disabled" => $this->disabled,
            "created_at" => $this->createdAtDate->format(DATE_ATOM),
            "updated_at" => $this->updatedAtDate->format(DATE_ATOM),
        ];
    }

    public function getReferralId(): ReferralId
    {
        return $this->referralId;
    }
    public function getOwnerId(): UserId
    {
        return $this->ownerId;
    }
    public function getCode(): ReferralCode
    {
        return $this->code;
    }
    public function getUrl(): Url
    {
        return $this->url;
    }
    public function getName(): ReferralName
    {
        return $this->name;
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
