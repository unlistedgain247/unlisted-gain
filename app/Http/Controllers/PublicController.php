<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    public function welcome()
    {
        $topStocks = collect(DB::select("
            SELECT
                s.UL_STOCKS_COMPNAME  AS name,
                s.UL_STOCKS_SLUG      AS slug,
                s.UL_STOCKS_LOGO_LINK AS logo,
                s.UL_STOCKS_INDUSTRY  AS industry,
                p.UL_PD_BID_PRICE     AS price,
                ROUND((f.UL_FIN_NUM_SHARES * p.UL_PD_BID_PRICE) / 10000000, 1)  AS mcap,
                ROUND(
                    ((f.UL_FIN_NUM_SHARES * p.UL_PD_BID_PRICE) / 10000000)
                    / NULLIF(f.UL_FIN_PAT * f.UL_FIN_Unit / 10000000, 0),
                    1
                ) AS pe
            FROM unlisted_stocks s
            LEFT JOIN (
                SELECT pd.*
                FROM unlisted_price_data pd
                INNER JOIN (
                    SELECT UL_PD_FINCODE, MAX(UL_PD_DATE) AS max_date
                    FROM unlisted_price_data
                    WHERE UL_PD_INVALID_FLAG = 0
                    GROUP BY UL_PD_FINCODE
                ) lp ON lp.UL_PD_FINCODE = pd.UL_PD_FINCODE
                       AND pd.UL_PD_DATE  = lp.max_date
                WHERE pd.UL_PD_INVALID_FLAG = 0
            ) p ON p.UL_PD_FINCODE = s.UL_STOCKS_FINCODE
            LEFT JOIN (
                SELECT uf.*
                FROM unlisted_financials uf
                INNER JOIN (
                    SELECT UL_FIN_FINCODE, MAX(UL_FIN_Period_end) AS max_period
                    FROM unlisted_financials
                    WHERE UL_FIN_STATUS = 1 AND UL_FIN_No_months = '12'
                    GROUP BY UL_FIN_FINCODE
                ) lf ON lf.UL_FIN_FINCODE      = uf.UL_FIN_FINCODE
                       AND uf.UL_FIN_Period_end = lf.max_period
                WHERE uf.UL_FIN_STATUS = 1 AND UL_FIN_No_months = '12'
            ) f ON f.UL_FIN_FINCODE = s.UL_STOCKS_FINCODE
            WHERE s.UL_STOCKS_STATUS = '1' AND p.UL_PD_BID_PRICE > 0
            ORDER BY mcap DESC
            LIMIT 10
        "));

        $totalStocks = DB::table('unlisted_stocks')->where('UL_STOCKS_STATUS', 1)->count();

        $totalMcapCr = DB::select("
            SELECT ROUND(SUM((f.UL_FIN_NUM_SHARES * p.UL_PD_BID_PRICE) / 10000000), 0) AS total
            FROM unlisted_stocks s
            LEFT JOIN (
                SELECT pd.*
                FROM unlisted_price_data pd
                INNER JOIN (
                    SELECT UL_PD_FINCODE, MAX(UL_PD_DATE) AS max_date
                    FROM unlisted_price_data WHERE UL_PD_INVALID_FLAG = 0 GROUP BY UL_PD_FINCODE
                ) lp ON lp.UL_PD_FINCODE = pd.UL_PD_FINCODE AND pd.UL_PD_DATE = lp.max_date
                WHERE pd.UL_PD_INVALID_FLAG = 0
            ) p ON p.UL_PD_FINCODE = s.UL_STOCKS_FINCODE
            LEFT JOIN (
                SELECT uf.*
                FROM unlisted_financials uf
                INNER JOIN (
                    SELECT UL_FIN_FINCODE, MAX(UL_FIN_Period_end) AS max_period
                    FROM unlisted_financials WHERE UL_FIN_STATUS = 1 AND UL_FIN_No_months = '12' GROUP BY UL_FIN_FINCODE
                ) lf ON lf.UL_FIN_FINCODE = uf.UL_FIN_FINCODE AND uf.UL_FIN_Period_end = lf.max_period
                WHERE uf.UL_FIN_STATUS = 1 AND UL_FIN_No_months = '12'
            ) f ON f.UL_FIN_FINCODE = s.UL_STOCKS_FINCODE
            WHERE s.UL_STOCKS_STATUS = '1' AND p.UL_PD_BID_PRICE > 0
        ");
        $totalMcap = $totalMcapCr[0]->total ?? 0;

        return view('public.welcome', compact('topStocks', 'totalStocks', 'totalMcap'));
    }

    public function about()
    {
        return view('public.about');
    }

    public function connect()
    {
        return view('public.connect');
    }

    public function privacyPolicy()
    {
        return view('public.privacy-policy');
    }

    public function termsOfUse()
    {
        return view('public.terms-of-use');
    }

    public function offMarketAnnexure()
    {
        return view('public.off-market-annexure');
    }

    public function panUnlistedShares()
    {
        return view('public.pan-unlisted-shares');
    }

    public function sebiGuidelines()
    {
        return view('public.sebi-guidelines');
    }

    public function knowledgeCentre()
    {
        return view('public.knowledge-centre');
    }

    public function faq()
    {
        return view('public.faq');
    }
}
