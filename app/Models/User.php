<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'uid';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'user_type',
        'unlisted_user_type',
        'session_token',
        'privilege',
        'failed_login_attempts',
        'login_locked_until',
        'lockout_count',
        // Bank
        'bank_account_no',
        'bank_ifsc_code',
        'bank_holder_name',
        'bank_name',
        'bank_cancelled_check',
        'bank_verified',
        // Demat
        'demat_dp_id',
        'demat_dp_name',
        'demat_cml_copy',
        'demat_verified',
        // PAN
        'user_pan_no',
        'user_pan_image',
        'user_pan_verified',
    ];

    protected $hidden = [
        'password',
        'session_token',
    ];

    protected function casts(): array
    {
        return [
            'password'           => 'hashed',
            'privilege'          => 'array',
            'login_locked_until' => 'datetime',
        ];
    }
}
