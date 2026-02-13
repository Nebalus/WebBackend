<?php

namespace Nebalus\Webapi\Config;

use RuntimeException;

trait ConfigTrait
{
    private static function requireEnv(string $name): string
    {
        $value = getenv($name);
        if ($value === false || $value === '') {
            throw new RuntimeException("Required environment variable '$name' is not set");
        }
        return $value;
    }
}
