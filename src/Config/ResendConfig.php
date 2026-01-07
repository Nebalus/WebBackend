<?php

namespace Nebalus\Webapi\Config;

class ResendConfig
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = getenv('RESEND_API_KEY');
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }
}
