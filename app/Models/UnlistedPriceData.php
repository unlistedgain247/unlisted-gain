<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnlistedPriceData extends Model
{
    protected $table      = 'unlisted_price_data';
    protected $primaryKey = ['UL_PD_FINCODE', 'UL_PD_DATE'];
    public    $incrementing = false;
    public    $timestamps   = false;

    protected $fillable = [
        'UL_PD_FINCODE',
        'UL_PD_DATE',
        'UL_PD_BID_PRICE',
        'UL_PD_INVALID_FLAG',
        'UL_PD_UPDTIME',
    ];
}
