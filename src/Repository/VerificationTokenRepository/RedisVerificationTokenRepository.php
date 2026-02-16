<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Repository\VerificationTokenRepository;

use Fig\Http\Message\StatusCodeInterface;
use Nebalus\Webapi\Exception\ApiServiceUnavailableException;
use Redis;
use RedisException;

readonly class RedisVerificationTokenRepository
{
    private const string KEY_PREFIX = 'verify:';
    private const int DEFAULT_TTL_SECONDS = 900; // 15 minutes

    public function __construct(
        private Redis $redis,
    ) {
    }

    /**
     * @throws ApiServiceUnavailableException
     */
    public function storeToken(string $type, string $token, array $data, int $ttlSeconds = self::DEFAULT_TTL_SECONDS): void
    {
        $key = self::KEY_PREFIX . $type . ':' . $token;

        try {
            $this->redis->setex($key, $ttlSeconds, json_encode($data));
        } catch (RedisException) {
            throw new ApiServiceUnavailableException('This feature is currently disabled', StatusCodeInterface::STATUS_SERVICE_UNAVAILABLE);
        }
    }

    /**
     * @throws ApiServiceUnavailableException
     */
    public function getTokenData(string $type, string $token): ?array
    {
        $key = self::KEY_PREFIX . $type . ':' . $token;

        try {
            $data = $this->redis->get($key);
            if ($data === false) {
                return null;
            }
            return json_decode($data, true);
        } catch (RedisException) {
            throw new ApiServiceUnavailableException('This feature is currently disabled', StatusCodeInterface::STATUS_SERVICE_UNAVAILABLE);
        }
    }

    /**
     * @throws ApiServiceUnavailableException
     */
    public function deleteToken(string $type, string $token): void
    {
        $key = self::KEY_PREFIX . $type . ':' . $token;

        try {
            $this->redis->del($key);
        } catch (RedisException) {
            throw new ApiServiceUnavailableException('This feature is currently disabled', StatusCodeInterface::STATUS_SERVICE_UNAVAILABLE);
        }
    }
}
