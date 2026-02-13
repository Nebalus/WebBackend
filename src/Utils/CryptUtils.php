<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Utils;

class CryptUtils
{
    public function generateAnonymousId(string $ipAddress, string $userAgent): string
    {
        $dailySalt = date('Y-m-d');
        return hash('sha256', $ipAddress . $userAgent . $dailySalt);
    }
}
