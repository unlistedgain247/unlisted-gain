<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

// Backs both Privilege::get() and SessionAuth::valid() — they each need one
// column off the same `users` row keyed by session('uid'), so whichever runs
// first fetches it once per request and the other reuses it instead of
// issuing its own separate query for the same row.
class CurrentUserRow
{
    private static bool $loaded = false;
    private static ?object $row = null;

    public static function get(): ?object
    {
        if (!self::$loaded) {
            self::$loaded = true;
            $uid = session('uid');
            self::$row = $uid
                ? DB::table('users')->where('uid', $uid)->first(['privilege', 'session_token'])
                : null;
        }

        return self::$row;
    }
}
