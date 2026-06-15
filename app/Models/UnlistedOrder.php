<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnlistedOrder extends Model
{
    protected $table      = 'unlisted_orders';
    protected $primaryKey = 'UL_ORD_ID';
    public    $timestamps = false;

    protected $fillable = [
        'UL_ORD_USER_ID',
        'UL_ORD_INTERMEDIARY_USER_ID',
        'UL_ORD_INTERMEDIARY_MARGIN',
        'UL_ORD_INTERMEDIARY_COMMISSION',
        'UL_ORD_FINCODE',
        'UL_ORD_TYPE',
        'UL_ORD_QUANTITY',
        'UL_ORD_PRICE_PER_SHARE',
        'UL_ORD_AMOUNT',
        'UL_ORD_STATUS',
        'UL_ORD_SUB_STATUS',
        'UL_ORD_OTHER_DEAL_TERMS',
        'UL_ORD_INSERT_TIME',
        'UL_ORD_UPDATE_TIME',
        'UL_ORD_DATE',
        'UL_ORD_ADDED_BY',
        'UL_ORD_LP',
        'UL_ORD_MLP',
        'UL_ORD_PAYMENT_PROOF_ID',
        'UL_ORD_DIRECT_FLAG',
    ];
}
