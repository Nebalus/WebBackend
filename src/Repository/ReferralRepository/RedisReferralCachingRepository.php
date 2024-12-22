<?php

namespace Nebalus\Webapi\Repository\ReferralRepository;

use Nebalus\Webapi\Exception\ApiException;
use Nebalus\Webapi\Value\ID;
use Nebalus\Webapi\Value\Referral\Referral;
use Nebalus\Webapi\Value\Referral\Referrals;
use Redis;
use RedisException;

class RedisReferralCachingRepository
{
    public const string HASH_KEY = 'referral_items';

    public function __construct(
        private readonly Redis $redis,
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

    public function deleteReferral(ID $referralId): void
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

    public function getReferral(ID $referralId): ?Referral
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

    public function getAllReferrals(): Referrals
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

        return Referrals::fromArray(...$referrals);
    }

    public function deleteAllItems(): void
    {
        try {
            $this->redis->del([self::HASH_KEY]);
        } catch (RedisException) {
        }
    }
}
