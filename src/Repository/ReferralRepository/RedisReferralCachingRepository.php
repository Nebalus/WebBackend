<?php

namespace Nebalus\Webapi\Repository\ReferralRepository;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\Module\Referral\Referral;
use Nebalus\Webapi\Value\Module\Referral\ReferralCollection;
use Nebalus\Webapi\Value\Module\Referral\ReferralId;
use Redis;
use RedisException;

readonly class RedisReferralCachingRepository
{
    public const string HASH_KEY = 'referrals';

    public function __construct(
        private Redis $redis,
    ) {
    }

    public function addReferral(Referral $referral): void
    {
        try {
            $this->redis->hset(
                self::HASH_KEY,
                $referral->getReferralId()->asString(),
                json_encode($referral->asArray())
            );
        } catch (RedisException) {
        }
    }

    public function deleteReferral(ReferralId $referralId): void
    {
        try {
            $this->redis->hdel(self::HASH_KEY, $referralId->asString());
        } catch (RedisException) {
        }
    }

    public function updateReferral(Referral $item): bool
    {
        try {
            $existingItem = $this->getReferral($item->getReferralId());
            if ($existingItem) {
                $this->addReferral($item);
                return true;
            }
        } catch (RedisException) {
        }
        return false;
    }

    public function getReferral(ReferralId $referralId): ?Referral
    {
        try {
            $itemData = $this->redis->hget(self::HASH_KEY, $referralId->asString());
            if ($itemData) {
                $dataArray = json_decode($itemData, true);
                return Referral::fromArray($dataArray);
            }
        } catch (RedisException | ApiException) {
        }
        return null;
    }

    public function getAllReferrals(): ReferralCollection
    {
        try {
            $items = $this->redis->hgetall(self::HASH_KEY);
        } catch (RedisException) {
        }

        $referrals = [];
        foreach ($items as $referralId => $referralData) {
            try {
                $referrals[] = Referral::fromArray($referralData);
            } catch (ApiException | RedisException) {
            }
        }

        return ReferralCollection::fromObjects(...$referrals);
    }

    public function deleteAllItems(): void
    {
        try {
            $this->redis->del([self::HASH_KEY]);
        } catch (RedisException) {
        }
    }
}
