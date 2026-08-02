<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnlistedDocument extends Model
{
    protected $table      = 'unlisted_documents';
    protected $primaryKey = 'UL_DOC_ID';
    public    $timestamps = false;

    public const DOC_TYPES = [
        'Annual Report', 'Quarterly Report', 'Investor Presentation', 'Announcement',
        'Announcement IPO', 'DRHP', 'RHP', 'Research Report', 'Anchor Investor', 'Valuation Report',
    ];

    public const RESEARCH_HOUSES = [
        'Arihant Capital', 'Ashika Research', 'Axis Direct', 'BOB Capital Markets', 'Bonanza',
        'Geojit BNP Paribas', 'ICICI Direct', 'ICICI Securities', 'IDBI Capital', 'KRChoksey',
        'LKP Securities', 'Motilal Oswal', 'Prabhudas Lilladhar', 'Sharekhan', 'SMC online',
        'Ventura', 'HDFC Securities', 'Edelweiss',
    ];

    protected $fillable = [
        'UL_DOC_FINCODE',
        'UL_DOC_TYPE',
        'UL_DOC_RESEARCH_HOUSE',
        'UL_DOC_DATE',
        'UL_DOC_PERIOD_MM',
        'UL_DOC_PERIOD_YY',
        'UL_DOC_DESCRIPTION',
        'UL_DOC_FILE_PATH',
        'UL_DOC_FILELINK',
        'UL_DOC_STATUS',
        'UL_DOC_INSERT_BY',
        'UL_DOC_INSERT_TIME',
        'UL_DOC_UPDATE_TIME',
    ];
}
