<?php

namespace App\Helpers;

class SessionAuth
{
    private static ?bool $valid = null;

    // A browser session is only genuinely authenticated if its session_token
    // still matches the one on the user's row — logging in elsewhere (which
    // rotates it) or logging out (which nulls it) must invalidate every other
    // session immediately, not just the one that performed the action.
    public static function valid(): bool
    {
        if (self::$valid === null) {
            $token = session('session_token');
            $row   = CurrentUserRow::get();

            self::$valid = (bool) (session('uid') && $token && $row && $row->session_token === $token);
        }

        return self::$valid;
    }
}
