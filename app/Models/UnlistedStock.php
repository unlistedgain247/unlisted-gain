<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnlistedStock extends Model
{
    protected $table      = 'unlisted_stocks';
    protected $primaryKey = 'UL_STOCKS_FINCODE';

    const CREATED_AT = 'UL_STOCKS_INSERT_TIME';
    const UPDATED_AT = 'UL_STOCKS_UPDATE_TIME';

    protected $fillable = [
        'UL_STOCKS_COMPNAME',
        'UL_STOCKS_SLUG',
        'UL_STOCKS_LOGO_LINK',
        'UL_STOCKS_IND_CODE',
        'UL_STOCKS_INDUSTRY',
        'UL_STOCKS_ISIN',
        'UL_STOCKS_S_NAME',
        'UL_STOCKS_CATEGORY',
        'UL_STOCKS_INC_MONTH',
        'UL_STOCKS_INC_YEAR',
        'UL_STOCKS_WEBSITE',
        'UL_STOCKS_STATUS',
        'UL_STOCKS_COMPNAME_TYPE',
        'UL_STOCKS_COMP_RATING',
        'UL_STOCKS_VALUATION_RATING',
        'UL_STOCKS_BUY_SELL_FLAG',
        'UL_STOCKS_LOT_SIZE',
        'UL_STOCKS_ROFR_FLAG',
        'UL_STOCKS_DEMAT_ACCOUNT_REQ',
        'UL_STOCKS_Qtr_Data_Publish',
        'UL_STOCKS_ABOUT',
        'UL_STOCKS_INSERT_BY',
    ];
}
