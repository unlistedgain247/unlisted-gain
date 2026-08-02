<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    /**
     * Top rows for the homepage "Live Unlisted Market" widget.
     * Change is computed against the nearest valid price on/before (today - N days).
     */
    private function marketWidgetRows(string $tab, string $range): array
    {
        $days = match ($range) {
            '1d' => 1,
            '1w' => 7,
            '1m' => 30,
            '3m' => 90,
            '6m' => 180,
            '1y' => 365,
            default => 7,
        };
        $cutoff = now()->subDays($days)->format('Y-m-d 23:59:59');

        $rows = collect(DB::select("
            SELECT
                s.UL_STOCKS_COMPNAME AS name,
                s.UL_STOCKS_SLUG     AS slug,
                p.UL_PD_BID_PRICE    AS price,
                cp.UL_PD_BID_PRICE   AS prior_price,
                ROUND((f.UL_FIN_NUM_SHARES * p.UL_PD_BID_PRICE) / 10000000, 1) AS mcap
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
                SELECT pd.*
                FROM unlisted_price_data pd
                INNER JOIN (
                    SELECT UL_PD_FINCODE, MAX(UL_PD_DATE) AS cutoff_date
                    FROM unlisted_price_data
                    WHERE UL_PD_INVALID_FLAG = 0 AND UL_PD_DATE <= ?
                    GROUP BY UL_PD_FINCODE
                ) cpl ON cpl.UL_PD_FINCODE = pd.UL_PD_FINCODE
                        AND pd.UL_PD_DATE  = cpl.cutoff_date
                WHERE pd.UL_PD_INVALID_FLAG = 0
            ) cp ON cp.UL_PD_FINCODE = s.UL_STOCKS_FINCODE
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
                WHERE uf.UL_FIN_STATUS = 1 AND uf.UL_FIN_No_months = '12'
            ) f ON f.UL_FIN_FINCODE = s.UL_STOCKS_FINCODE
            WHERE s.UL_STOCKS_STATUS = '1' AND p.UL_PD_BID_PRICE > 0
        ", [$cutoff]))->map(function ($row) {
            $price = (float) $row->price;
            $prior = $row->prior_price !== null ? (float) $row->prior_price : null;
            $chg   = $prior ? round($price - $prior, 2) : 0.0;
            $pct   = $prior ? round(($chg / $prior) * 100, 2) : 0.0;

            return [
                'name'  => $row->name,
                'slug'  => $row->slug,
                'price' => $price,
                'mcap'  => (float) ($row->mcap ?? 0),
                'chg'   => $chg,
                'pct'   => $pct,
            ];
        });

        $sorted = match ($tab) {
            'gainers' => $rows->filter(fn ($r) => $r['pct'] > 0)->sortByDesc('pct'),
            'losers'  => $rows->filter(fn ($r) => $r['pct'] < 0)->sortBy('pct'),
            default   => $rows->sortByDesc('mcap'),
        };

        return $sorted->take(8)->values()->all();
    }

    public function marketWidgetData(Request $request)
    {
        $tab   = $request->input('tab', 'trending');
        $range = $request->input('range', '1w');

        if (!in_array($tab, ['trending', 'gainers', 'losers'], true)) {
            $tab = 'trending';
        }
        if (!in_array($range, ['1d', '1w', '1m', '3m', '6m', '1y'], true)) {
            $range = '1w';
        }

        $rows = $this->marketWidgetRows($tab, $range);

        return view('partials.live-market-rows', compact('rows'));
    }

    public function welcome()
    {
        // $topStocks (top 10 by mcap) and the site-wide mcap total used to run as
        // two separate queries that each rebuilt the identical "latest price per
        // stock" + "latest annual financials per stock" derived joins from
        // scratch. They're the same base dataset at two different aggregations,
        // so fetch it once (no LIMIT) and derive both in PHP instead.
        $allActiveStocks = collect(DB::select("
            SELECT
                s.UL_STOCKS_COMPNAME  AS name,
                s.UL_STOCKS_S_NAME    AS s_name,
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
                WHERE uf.UL_FIN_STATUS = 1 AND uf.UL_FIN_No_months = '12'
            ) f ON f.UL_FIN_FINCODE = s.UL_STOCKS_FINCODE
            WHERE s.UL_STOCKS_STATUS = '1' AND p.UL_PD_BID_PRICE > 0
        "));

        $topStocks = $allActiveStocks
            ->sortByDesc(fn ($r) => (float) $r->mcap)
            ->take(10)
            ->values();

        $totalMcap = round($allActiveStocks->sum(fn ($r) => (float) $r->mcap), 0);

        $totalStocks = DB::table('unlisted_stocks')->where('UL_STOCKS_STATUS', 1)->count();

        $marketWidget = $this->marketWidgetRows('trending', '1w');

        $latestArticles = Article::published()
            ->orderByDesc('published_at')
            ->limit(10)
            ->get(['id', 'title', 'slug', 'published_at']);

        return view('public.welcome', compact(
            'topStocks', 'totalStocks', 'totalMcap', 'marketWidget', 'latestArticles'
        ));
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
