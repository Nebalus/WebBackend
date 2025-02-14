<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Repository\ReferralRepository;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Repository\AbstractRepository;
use Nebalus\Webapi\Value\ID;
use Nebalus\Webapi\Value\Referral\Referral;
use Nebalus\Webapi\Value\Referral\ReferralCode;
use Nebalus\Webapi\Value\Referral\ReferralId;
use Nebalus\Webapi\Value\Referral\ReferralName;
use Nebalus\Webapi\Value\Referral\ReferralPointer;
use Nebalus\Webapi\Value\Referral\Referrals;
use Nebalus\Webapi\Value\User\UserId;
use PDO;

class MySqlReferralRepository extends AbstractRepository
{
    public function __construct(
        private PDO $pdo,
        private RedisReferralCachingRepository $redis
    ) {
        parent::__construct($pdo);
    }

    public function insertReferral(UserId $ownerUserId, ReferralCode $code, ReferralPointer $pointer, ReferralName $name, bool $disabled = true): bool
    {
        $sql = "INSERT INTO referrals(owner_user_id, code, pointer, name, disabled) VALUES (:owner_user_id, :code, :pointer, :name, :disabled)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':owner_user_id', $ownerUserId->asInt());
        $stmt->bindValue(':code', $code->asString());
        $stmt->bindValue(':pointer', $pointer->asString());
        $stmt->bindValue(':name', $name->asString());
        $stmt->bindValue(':disabled', $disabled);
        return $stmt->execute();
    }

    public function insertReferralClickEntry(ReferralId $referralId): bool
    {
        $sql = "INSERT INTO referral_click_metric(referral_id) VALUES (:referral_id)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':referral_id', $referralId->asInt());
        return $stmt->execute();
    }

    public function deleteReferralByCodeAndOwnerId(ReferralCode $code, UserId $ownerUserId): bool
    {
        $sql = "DELETE FROM referrals WHERE code = :code AND owner_user_id = :owner_user_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':code', $code->asString());
        $stmt->bindValue(':owner_user_id', $ownerUserId->asInt());
        $stmt->execute();

        return $stmt->rowCount() === 1;
    }

    public function updateReferral(Referral $referral, UserId $ownerUserId): bool
    {
        $sql = "UPDATE `referrals` SET `pointer`=:pointer,`name`=:name,`disabled`=:disabled WHERE `owner_user_id`=:owner_user_id AND `code`=:code";

//        $stmt = $this->pdo->prepare($sql);
//        $stmt->bindValue(':pointer', $referral->getPointer()->asString());
//        $stmt->bindValue(':name', $referral->getName()->asString());
//        $stmt->bindValue(':owner_user_id', $ownerUserId->asInt());
//        $stmt->bindValue("")
//        $stmt->execute();

        return false;
    }

    /**
     * @throws ApiException
     */
    public function getReferralsFromOwnerId(UserId $ownerUserId): Referrals
    {
        $sql = "SELECT * FROM referrals WHERE owner_user_id = :owner_user_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':owner_user_id', $ownerUserId->asInt());
        $stmt->execute();

        $data = [];

        while ($row = $stmt->fetch()) {
            $data[] = Referral::fromArray($row);
        }

        return Referrals::fromArray(...$data);
    }

    /**
     * @throws ApiException
     */
    public function findReferralByCode(ReferralCode $code): ?Referral
    {
        $sql = "SELECT * FROM referrals WHERE code = :code";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':code', $code->asString());
        $stmt->execute();

        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return Referral::fromArray($data);
    }

    /**
     * @throws ApiException
     */
    public function findReferralByCodeAndOwnerId(ReferralCode $code, UserId $ownerUserId): ?Referral
    {
        $sql = "SELECT * FROM referrals WHERE code = :code AND owner_user_id = :owner_user_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':code', $code->asString());
        $stmt->bindValue(':owner_user_id', $ownerUserId->asInt());
        $stmt->execute();

        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return Referral::fromArray($data);
    }
}
