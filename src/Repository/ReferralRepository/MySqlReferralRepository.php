<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Repository\ReferralRepository;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Utils\IpUtils;
use Nebalus\Webapi\Value\Hash\SHA256Hash;
use Nebalus\Webapi\Value\Module\Referral\Click\ReferralClick;
use Nebalus\Webapi\Value\Module\Referral\Click\ReferralClickCollection;
use Nebalus\Webapi\Value\Module\Referral\Referral;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;
use Nebalus\Webapi\Value\Module\Referral\ReferralCollection;
use Nebalus\Webapi\Value\Module\Referral\ReferralId;
use Nebalus\Webapi\Value\Module\Referral\ReferralLabel;
use Nebalus\Webapi\Value\Url;
use Nebalus\Webapi\Value\User\UserId;
use PDO;

readonly class MySqlReferralRepository
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    public function insertReferral(UserId $ownerId, ReferralCode $code, Url $url, ReferralLabel $label, bool $disabled = true): bool
    {
        $sql = <<<SQL
            INSERT INTO referrals
                (owner_id, code, url, label, disabled)
            VALUES 
                (:owner_id, :code, :url, :label, :disabled)
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':owner_id', $ownerId->asInt());
        $stmt->bindValue(':code', $code->asString());
        $stmt->bindValue(':url', $url->asString());
        $stmt->bindValue(':label', $label->asString());
        $stmt->bindValue(':disabled', $disabled, PDO::PARAM_BOOL);
        return $stmt->execute();
    }

    public function insertReferralClickEntry(ReferralId $referralId, SHA256Hash $anonymousIdentityHash): bool
    {
        $sql = <<<SQL
            INSERT INTO referral_click_metric (referral_id, anonymous_identity_hash) 
            VALUES (:referral_id, 0x:anonymous_identity_hash)
        SQL;

        echo $anonymousIdentityHash->asString();
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':referral_id', $referralId->asInt());
        $stmt->bindValue(':anonymous_identity_hash', $anonymousIdentityHash->asString());
        return $stmt->execute();
    }

    /**
     * @throws ApiException
     */
    public function getReferralClicksFromRange(UserId $ownerId, ReferralCode $code, int $range): ReferralClickCollection
    {
        $data = [];
        $sql = <<<SQL
            SELECT
                DATE(referral_click_metric.clicked_at) AS clicked_at, COUNT(clicked_at) AS click_count,
                COUNT(DISTINCT referral_click_metric.anonymous_identity_hash) AS unique_visitors
            FROM referral_click_metric
            INNER JOIN referrals 
                ON referrals.referral_id = referral_click_metric.referral_id
            WHERE 
                referrals.code = :referralCode 
                AND referrals.owner_id = :ownerId
            GROUP BY 
                DATE(referral_click_metric.clicked_at)
            HAVING
                clicked_at >= DATE(NOW() - INTERVAL :range DAY)
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":referralCode", $code->asString());
        $stmt->bindValue(":ownerId", $ownerId->asInt());
        $stmt->bindValue(":range", $range);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $data[] = ReferralClick::fromArray($row);
        }

        return ReferralClickCollection::fromObjects(...$data);
    }

    public function deleteReferralByCodeFromOwner(UserId $ownerId, ReferralCode $code): bool
    {
        $sql = <<<SQL
            DELETE FROM referrals 
            WHERE 
                owner_id = :owner_id 
                AND code = :code
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':owner_id', $ownerId->asInt());
        $stmt->bindValue(':code', $code->asString());
        $stmt->execute();

        return $stmt->rowCount() === 1;
    }

    /**
     * @throws ApiException
     */
    public function updateReferralFromOwner(UserId $ownerId, ReferralCode $code, Url $url, ReferralLabel $label, bool $disabled): ?Referral
    {
        $sql = <<<SQL
            UPDATE referrals 
            SET 
                url = :url, 
                label = :label, 
                disabled = :disabled 
            WHERE 
                owner_id = :owner_id 
                AND code = :code
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':url', $url->asString());
        $stmt->bindValue(':label', $label->asString());
        $stmt->bindValue(':disabled', $disabled, PDO::PARAM_BOOL);
        $stmt->bindValue(':owner_id', $ownerId->asInt(), PDO::PARAM_INT);
        $stmt->bindValue(':code', $code->asString());
        $stmt->execute();

        return $this->findReferralByCode($code);
    }

    /**
     * @throws ApiException
     */
    public function findReferralsFromOwner(UserId $ownerUserId): ReferralCollection
    {
        $sql = <<<SQL
            SELECT 
                * 
            FROM referrals
            WHERE
                owner_id = :owner_id
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':owner_id', $ownerUserId->asInt());
        $stmt->execute();

        $data = [];

        while ($row = $stmt->fetch()) {
            $data[] = Referral::fromArray($row);
        }

        return ReferralCollection::fromObjects(...$data);
    }

    /**
     * @throws ApiException
     */
    public function findReferralByCode(ReferralCode $code): ?Referral
    {
        $sql = <<<SQL
            SELECT 
                * 
            FROM referrals 
            WHERE 
                code = :code
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':code', $code->asString());
        $stmt->execute();

        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return Referral::fromArray($data);
    }
}
