<?php

namespace Nebalus\Webapi\Config;

class ResendConfig
{
    use ConfigTrait;

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = self::requireEnv('RESEND_API_KEY');
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}
