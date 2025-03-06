<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Repository\Referral;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Utils\IpUtils;
use Nebalus\Webapi\Value\Module\Referral\Click\ReferralClick;
use Nebalus\Webapi\Value\Module\Referral\Click\ReferralClicks;
use Nebalus\Webapi\Value\Module\Referral\Referral;
use Nebalus\Webapi\Value\Module\Referral\ReferralCode;
use Nebalus\Webapi\Value\Module\Referral\ReferralId;
use Nebalus\Webapi\Value\Module\Referral\ReferralName;
use Nebalus\Webapi\Value\Module\Referral\Referrals;
use Nebalus\Webapi\Value\Url;
use Nebalus\Webapi\Value\User\UserId;
use PDO;

readonly class MySqlReferralRepository
{
    public function __construct(
        private PDO $pdo,
        private IpUtils $ipUtils
    ) {
    }

    public function insertReferral(UserId $ownerUserId, ReferralCode $code, Url $url, ReferralName $name, bool $disabled = true): bool
    {
        $sql = <<<SQL
            INSERT INTO referrals
                (owner_user_id, code, url, name, disabled) 
            VALUES 
                (:owner_user_id, :code, :url, :name, :disabled)
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':owner_user_id', $ownerUserId->asInt());
        $stmt->bindValue(':code', $code->asString());
        $stmt->bindValue(':url', $url->asString());
        $stmt->bindValue(':name', $name->asString());
        $stmt->bindValue(':disabled', $disabled, PDO::PARAM_BOOL);
        return $stmt->execute();
    }

    public function insertReferralClickEntry(ReferralId $referralId): bool
    {
        $ipAddress = $this->ipUtils->getClientIP();

        $sql = <<<SQL
            INSERT INTO referral_click_metric (referral_id, ip_address) 
            VALUES (:referral_id, NULL)
        SQL;

        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $sql = <<<SQL
                INSERT INTO referral_click_metric (referral_id, ip_address) 
                VALUES (:referral_id, INET_ATON(:ip_address))
            SQL;
        }

        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $sql = <<<SQL
                INSERT INTO referral_click_metric (referral_id, ip_address) 
                VALUES (:referral_id, INET6_ATON(:ip_address))
            SQL;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':referral_id', $referralId->asInt());
        $stmt->bindValue(':ip_address', $ipAddress);
        return $stmt->execute();
    }

    /**
     * @throws ApiException
     */
    public function getReferralClicksFromRange(UserId $ownerUserId, ReferralCode $referralCode, int $range): ReferralClicks
    {
        $data = [];
        $sql = <<<SQL
            SELECT
                DATE(referral_click_metric.clicked_at) AS clicked_at, COUNT(clicked_at) AS click_count,
                COUNT(DISTINCT referral_click_metric.ip_address) AS unique_visitors
            FROM referral_click_metric
            INNER JOIN referrals 
                ON referrals.referral_id = referral_click_metric.referral_id
            WHERE 
                referrals.code = :referralCode 
                AND referrals.owner_user_id = :ownerUserId
            GROUP BY 
                DATE(referral_click_metric.clicked_at)
            HAVING
                clicked_at >= DATE(NOW() - INTERVAL :range DAY)
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":referralCode", $referralCode->asString());
        $stmt->bindValue(":ownerUserId", $ownerUserId->asInt());
        $stmt->bindValue(":range", $range);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $data[] = ReferralClick::fromArray($row);
        }

        return ReferralClicks::fromArray(...$data);
    }

    public function deleteReferralByCodeFromOwner(UserId $ownerUserId, ReferralCode $code): bool
    {
        $sql = <<<SQL
            DELETE FROM referrals 
            WHERE 
                owner_user_id = :owner_user_id 
                AND code = :code
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':owner_user_id', $ownerUserId->asInt());
        $stmt->bindValue(':code', $code->asString());
        $stmt->execute();

        return $stmt->rowCount() === 1;
    }

    /**
     * @throws ApiException
     */
    public function updateReferralFromOwner(UserId $ownerUserId, ReferralCode $referralCode, Url $referralUrl, ReferralName $referralName, bool $isDisabled): ?Referral
    {
        $sql = <<<SQL
            UPDATE referrals 
            SET 
                url = :url, 
                name = :name, 
                disabled = :disabled 
            WHERE 
                owner_user_id = :owner_user_id 
                AND code = :code
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':url', $referralUrl->asString());
        $stmt->bindValue(':name', $referralName->asString());
        $stmt->bindValue(':disabled', $isDisabled, PDO::PARAM_BOOL);
        $stmt->bindValue(':owner_user_id', $ownerUserId->asInt(), PDO::PARAM_INT);
        $stmt->bindValue(':code', $referralCode->asString());
        $stmt->execute();

        return $this->findReferralByCodeFromOwner($ownerUserId, $referralCode);
    }

    /**
     * @throws ApiException
     */
    public function getReferralsFromOwner(UserId $ownerUserId): Referrals
    {
        $sql = <<<SQL
            SELECT 
                * 
            FROM referrals
            WHERE
                owner_user_id = :owner_user_id
        SQL;

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

    /**
     * @throws ApiException
     */
    public function findReferralByCodeFromOwner(UserId $ownerUserId, ReferralCode $code): ?Referral
    {
        $sql = <<<SQL
            SELECT 
                * 
            FROM referrals
            WHERE 
                owner_user_id = :owner_user_id
                AND code = :code
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':owner_user_id', $ownerUserId->asInt());
        $stmt->bindValue(':code', $code->asString());
        $stmt->execute();

        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return Referral::fromArray($data);
    }
}
