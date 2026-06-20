<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class Privilege
{
    private static ?array $cache = null;

    public static function get(?string $key = null): mixed
    {
        if (static::$cache === null) {
            $uid = session('uid');
            if (!$uid) {
                static::$cache = [];
            } else {
                $raw = DB::table('users')->where('uid', $uid)->value('privilege');
                static::$cache = is_string($raw) ? (json_decode($raw, true) ?? []) : ($raw ?? []);
            }
        }

        return $key === null ? static::$cache : data_get(static::$cache, $key);
    }
}
