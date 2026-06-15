<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnlistedThesis extends Model
{
    protected $table      = 'unlisted_thesis';
    protected $primaryKey = 'UL_THESIS_ID';
    public    $timestamps = false;

    protected $fillable = [
        'UL_THESIS_FINCODE',
        'UL_THESIS_CONTENT',
        'UL_THESIS_ACTIVE',
        'UL_THESIS_INSERT_TIME',
        'UL_THESIS_UPDATE_TIME',
    ];
}
