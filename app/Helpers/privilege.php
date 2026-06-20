<?php

use Illuminate\Support\Facades\DB;

/**
 * Fetch the current user's privilege array fresh from the DB.
 * Result is cached for the lifetime of the current PHP request.
 * With $key (dot-notation), returns a specific value; without, returns full array.
 */
function user_privilege(?string $key = null): mixed
{
    static $priv = null;

    if ($priv === null) {
        $uid = session('uid');
        if (!$uid) {
            $priv = [];
        } else {
            $raw  = DB::table('users')->where('uid', $uid)->value('privilege');
            $priv = is_string($raw) ? (json_decode($raw, true) ?? []) : ($raw ?? []);
        }
    }

    return $key === null ? $priv : data_get($priv, $key);
}
