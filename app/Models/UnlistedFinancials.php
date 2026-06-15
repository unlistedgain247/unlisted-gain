<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnlistedFinancials extends Model
{
    protected $table      = 'unlisted_financials';
    protected $primaryKey = ['UL_FIN_FINCODE', 'UL_FIN_Period_end', 'UL_FIN_Type', 'UL_FIN_No_months'];
    public    $incrementing = false;
    public    $timestamps   = false;

    protected $fillable = [
        'UL_FIN_FINCODE',
        'UL_FIN_Period_end',
        'UL_FIN_Type',
        'UL_FIN_No_months',
        'UL_FIN_Unit',
        'UL_FIN_FV',
        'UL_FIN_NUM_SHARES',
        'UL_FIN_PROMOTERS_HOLDING',
        'UL_FIN_ANNUAL_DIVIDEND_PER_SHARE',
        'UL_FIN_NET_SALES',
        'UL_FIN_OTHER_INCOME',
        'UL_FIN_TOTAL_INCOME',
        'UL_FIN_TOTAL_EXPENDITURE',
        'UL_FIN_OPERATING_PROFIT',
        'UL_FIN_INTEREST',
        'UL_FIN_DEPRECIATION',
        'UL_FIN_EXCEPTIONAL_INCOME',
        'UL_FIN_PBT',
        'UL_FIN_TAX',
        'UL_FIN_PAT',
        'UL_FIN_ADJUSTMENTS',
        'UL_FIN_PROFIT_AFTER_ADJUSTMENTS',
        'UL_FIN_ADJUSTED_EPS',
        'UL_FIN_DPS',
        'UL_FIN_SHAREHOLDER_FUNDS',
        'UL_FIN_MINORITY_INTEREST',
        'UL_FIN_BORROWINGS',
        'UL_FIN_OTHER_NONCURRENT_LIABILITIES',
        'UL_FIN_TOTAL_CURRENT_LIABILITIES',
        'UL_FIN_TOTAL_LIABILITIES',
        'UL_FIN_FIXED_ASSETS',
        'UL_FIN_OTHER_NONCURRENT_ASSETS',
        'UL_FIN_TOTAL_CURRENT_ASSETS',
        'UL_FIN_TOTAL_ASSETS',
        'UL_FIN_TOTAL_DEBT',
        'UL_FIN_OPENING_CASH',
        'UL_FIN_CFO',
        'UL_FIN_CFI',
        'UL_FIN_CFF',
        'UL_FIN_NET_CASH_FLOW',
        'UL_FIN_CLOSING_CASH',
        'UL_FIN_CURRENT_LIABILITIES',
        'UL_FIN_NON_CURRENT_LIABILITIES',
        'UL_FIN_CURRENT_ASSETS',
        'UL_FIN_NON_CURRENT_ASSETS',
        'UL_FIN_CASH_FLOW_FROM_OPERATING_ACTIVITIES',
        'UL_FIN_CASH_FLOW_FORM_INVESTING_ACTIVITIES',
        'UL_FIN_CASH_FLOW_FROM_FINANCING_ACTIVITIES',
        'UL_FIN_FREE_CASH_FLOW',
        'UL_FIN_STATUS',
        'UL_FIN_INSERT_BY',
        'UL_FIN_INSERT_TIME',
        'UL_FIN_UPDATE_TIME',
    ];
}
