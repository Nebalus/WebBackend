<?php

namespace Nebalus\Webapi\Factory;

use Nebalus\Webapi\Config\ResendConfig;
use Resend;

readonly class ResendFactory
{
    public function __construct(
        private ResendConfig $resendConfig,
    ) {
    }

    public function __invoke(): Resend\Client
    {
        return Resend::client($this->resendConfig->getApiKey());
    }
}
