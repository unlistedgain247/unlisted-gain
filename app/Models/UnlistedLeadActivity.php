<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnlistedLeadActivity extends Model
{
    protected $table      = 'unlisted_leads_activity';
    protected $primaryKey = 'UL_LEAD_ACTY_ID';
    public    $timestamps = false;

    protected $fillable = [
        'UL_LEAD_ACTY_LID',
        'UL_LEAD_ACTY_UID',
        'UL_LEAD_ACTY_TYPE',
        'UL_LEAD_ACTY_DISPOSITION',
        'UL_LEAD_ACTY_SUB_DISPOSITION',
        'UL_LEAD_ACTY_REASON_FOR_CALL',
        'UL_LEAD_ACTY_PRODUCT_PITCHED',
        'UL_LEAD_ACTY_COMMENT',
        'UL_LEAD_ACTY_CALLBACK_TIME',
        'UL_LEAD_ACTY_TIMESTAMP',
    ];
}
