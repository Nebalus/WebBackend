<?php

declare(strict_types=1);

namespace Nebalus\Webapi\Utils;

use Nebalus\Webapi\Value\Hash\SHA256Hash;

class CryptUtils
{
    public function generateAnonymousIdentityHash(string $ipAddress, string $userAgent): SHA256Hash
    {
        $dailySalt = date('Y-m-d');
        return SHA256Hash::from($ipAddress . $userAgent . $dailySalt);
    }
}
