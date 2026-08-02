<?php

namespace App\Helpers;

class Privilege
{
    private static ?array $cache = null;

    public static function get(?string $key = null): mixed
    {
        if (static::$cache === null) {
            $raw = CurrentUserRow::get()?->privilege;
            static::$cache = is_string($raw) ? (json_decode($raw, true) ?? []) : ($raw ?? []);
        }

        return $key === null ? static::$cache : data_get(static::$cache, $key);
    }
}
