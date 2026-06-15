<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StocksController extends Controller
{
    private function stocksQuery(string $q = '', string $sort = 'mcap'): array
    {
        $orderBy = match ($sort) {
            'asc'   => 'ORDER BY main.UL_STOCKS_COMPNAME ASC',
            'desc'  => 'ORDER BY main.UL_STOCKS_COMPNAME DESC',
            default => 'ORDER BY main.MCAP DESC, main.UL_STOCKS_COMPNAME ASC',
        };

        $bindings = [];
        $search   = '';
        if ($q !== '') {
            $search   = 'AND LOWER(main.UL_STOCKS_COMPNAME) LIKE ?';
            $bindings = ['%' . strtolower($q) . '%'];
        }

        $sql = "
            SELECT
                main.UL_STOCKS_FINCODE,
                main.UL_STOCKS_COMPNAME,
                main.UL_STOCKS_SLUG,
                main.UL_STOCKS_LOGO_LINK,
                main.UL_STOCKS_INDUSTRY,
                main.UL_PD_BID_PRICE AS current_price,
                main.MCAP             AS market_cap,
                main.UL_STOCKS_LOT_SIZE AS lot_size,
                ROUND(
                    main.MCAP / NULLIF(main.UL_FIN_PAT * main.UL_FIN_Unit / 10000000, 0),
                    1
                ) AS pe_ratio
            FROM (
                SELECT
                    s.UL_STOCKS_FINCODE,
                    s.UL_STOCKS_COMPNAME,
                    s.UL_STOCKS_SLUG,
                    s.UL_STOCKS_LOGO_LINK,
                    s.UL_STOCKS_INDUSTRY,
                    s.UL_STOCKS_LOT_SIZE,
                    p.UL_PD_BID_PRICE,
                    f.UL_FIN_PAT,
                    f.UL_FIN_Unit,
                    ROUND((f.UL_FIN_NUM_SHARES * p.UL_PD_BID_PRICE) / 10000000, 1) AS MCAP
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
                WHERE s.UL_STOCKS_STATUS = 1
            ) AS main
            WHERE main.UL_PD_BID_PRICE IS NOT NULL
            {$search}
            {$orderBy}
        ";

        return ['sql' => $sql, 'bindings' => $bindings];
    }

    public function buy(Request $request)
    {
        $q    = trim($request->input('q', ''));
        $sort = $request->input('sort', 'mcap');

        ['sql' => $sql, 'bindings' => $bindings] = $this->stocksQuery($q, $sort);
        $stocks = DB::select($sql, $bindings);

        if ($request->ajax()) {
            return view('partials.stocks-rows', compact('stocks'));
        }

        return view('public.buy', compact('stocks'));
    }

    private function priceListResults(string $q = '', string $sort = 'mcap'): \Illuminate\Support\Collection
    {
        $orderBy = match ($sort) {
            'asc'   => 'ORDER BY s.UL_STOCKS_COMPNAME ASC',
            'desc'  => 'ORDER BY s.UL_STOCKS_COMPNAME DESC',
            default => 'ORDER BY market_cap DESC, s.UL_STOCKS_COMPNAME ASC',
        };

        $search   = '';
        $bindings = [];
        if ($q !== '') {
            $search     = 'AND LOWER(s.UL_STOCKS_COMPNAME) LIKE ?';
            $bindings[] = '%' . strtolower($q) . '%';
        }

        return collect(DB::select("
            SELECT
                s.UL_STOCKS_FINCODE,
                s.UL_STOCKS_COMPNAME,
                s.UL_STOCKS_SLUG,
                s.UL_STOCKS_LOGO_LINK,
                s.UL_STOCKS_LOT_SIZE,
                p.UL_PD_BID_PRICE  AS current_price,
                f.UL_FIN_FV        AS face_value,
                ROUND(
                    (f.UL_FIN_SHAREHOLDER_FUNDS * f.UL_FIN_Unit) / NULLIF(f.UL_FIN_NUM_SHARES, 0),
                    2
                )                  AS book_value,
                ROUND((f.UL_FIN_NUM_SHARES * p.UL_PD_BID_PRICE) / 10000000, 1) AS market_cap,
                ROUND(
                    ((f.UL_FIN_NUM_SHARES * p.UL_PD_BID_PRICE) / 10000000)
                    / NULLIF(f.UL_FIN_PAT * f.UL_FIN_Unit / 10000000, 0),
                    1
                )                  AS pe_ratio
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
            WHERE s.UL_STOCKS_STATUS = 1
              AND p.UL_PD_BID_PRICE IS NOT NULL
              {$search}
            {$orderBy}
        ", $bindings));
    }

    private function makePricePaginator(Request $request): \Illuminate\Pagination\LengthAwarePaginator
    {
        $q       = trim($request->input('q', ''));
        $sort    = $request->input('sort', 'mcap');
        $page    = max(1, (int) $request->input('page', 1));
        $perPage = 25;

        $all = $this->priceListResults($q, $sort);

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $all->forPage($page, $perPage)->values(),
            $all->count(),
            $perPage,
            $page
        );
    }

    private function makeStocksPaginator(Request $request): \Illuminate\Pagination\LengthAwarePaginator
    {
        $q       = trim($request->input('q', ''));
        $sort    = $request->input('sort', 'mcap');
        $page    = max(1, (int) $request->input('page', 1));
        $perPage = 25;

        ['sql' => $sql, 'bindings' => $bindings] = $this->stocksQuery($q, $sort);
        $all = collect(DB::select($sql, $bindings));

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $all->forPage($page, $perPage)->values(),
            $all->count(),
            $perPage,
            $page
        );
    }

    public function preIpo(Request $request)
    {
        $stocks = $this->makeStocksPaginator($request);
        $q      = trim($request->input('q', ''));
        $sort   = $request->input('sort', 'mcap');

        return view('public.pre-ipo', compact('stocks', 'q', 'sort'));
    }

    public function preIpoData(Request $request)
    {
        $stocks = $this->makeStocksPaginator($request);

        return view('public.partials.pre-ipo-rows', compact('stocks'));
    }

    public function priceList(Request $request)
    {
        $stocks = $this->makePricePaginator($request);
        $q      = trim($request->input('q', ''));
        $sort   = $request->input('sort', 'mcap');

        return view('public.price-list', compact('stocks', 'q', 'sort'));
    }

    public function priceListData(Request $request)
    {
        $stocks = $this->makePricePaginator($request);

        return view('public.partials.price-list-rows', compact('stocks'));
    }

    public function stocks()
    {
        ['sql' => $sql, 'bindings' => $bindings] = $this->stocksQuery();

        return response()->json(DB::select($sql, $bindings))
            ->header('Access-Control-Allow-Origin', '*');
    }
}
