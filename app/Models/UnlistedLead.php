<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnlistedLead extends Model
{
    protected $table      = 'unlisted_leads';
    protected $primaryKey = 'UL_LEAD_ID';
    public    $timestamps = false;

    protected $fillable = [
        'UL_LEAD_UID',
        'UL_LEAD_TYPE',
        'UL_LEAD_INSERT_TIME',
        'UL_LEAD_UPDATE_TIME',
        'UL_LEAD_CUSTOMER_LAST_VISITED_TIME',
        'UL_LEAD_DISPOSITION',
        'UL_LEAD_SUB_DISPOSITION',
        'UL_LEAD_REASON_FOR_CALL',
        'UL_LEAD_PRODUCT_PITCHED',
        'UL_LEAD_DISPOSITION_TIME',
        'UL_LEAD_DISPOSITION_COMMENT',
        'UL_LEAD_DISPOSITION_COUNT',
        'UL_LEAD_CALLBACK_TIME',
        'UL_LEAD_SOURCE_ID',
        'UL_LEAD_ALLOCATED_TO',
        'UL_LEAD_USER_TYPE',
        'UL_LEAD_COMPANY',
        'UL_LEAD_LANDING_PAGE',
        'UL_LEAD_REQUEST_FOR_CALL',
    ];
}
