<?php

namespace App\Support;

/**
 * Strips secrets/authorization data from provider payloads before they are
 * persisted or logged.
 */
class CourierSanitizer
{
    private const SENSITIVE_KEYS = [
        'api_key',
        'apikey',
        'secret_key',
        'secretkey',
        'secret',
        'authorization',
        'auth',
        'token',
        'access_token',
        'password',
    ];

    public static function clean(array $data): array
    {
        $clean = [];

        foreach ($data as $key => $value) {
            if (is_string($key) && in_array(strtolower($key), self::SENSITIVE_KEYS, true)) {
                continue;
            }

            $clean[$key] = is_array($value) ? self::clean($value) : $value;
        }

        return $clean;
    }
}
