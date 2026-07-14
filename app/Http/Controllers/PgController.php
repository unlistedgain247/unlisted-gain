<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\Privilege;

class PgController extends Controller
{
    // ─── Pages ───────────────────────────────────────────────────────────────

    public function margin()
    {
        if (!$this->canAccessMargin()) abort(403);
        return view('admin.pg.margin');
    }

    public function marginError()
    {
        if (!$this->canAccessMarginError()) abort(403);
        return view('admin.pg.margin-error');
    }

    // ─── AJAX: Margin Dashboard table ────────────────────────────────────────

    public function marginData(Request $request)
    {
        if (!$this->canAccessMargin()) abort(403);

        $fromDate      = $request->input('from_date', '');
        $toDate        = $request->input('to_date', '');
        $searchtext    = trim($request->input('searchtext', ''));
        $advisorSearch = trim($request->input('advisor_search', ''));

        $sql = "SELECT
                    MONTH(UL_ORD_DATE)  AS ord_month,
                    YEAR(UL_ORD_DATE)   AS ord_year,
                    COALESCE(ROUND(SUM(UL_ORD_INTERMEDIARY_COMMISSION)), 0)
                        AS BUY_COMMISSION,
                    COALESCE(ROUND(SUM(UL_ORD_AMOUNT)), 0)
                        AS BUY_ORDER_AMOUNT,
                    COALESCE(SUM(UL_ORD_QUANTITY), 0)
                        AS BUY_ORDER_QTY,
                    COALESCE(ROUND(SUM((UL_ORD_PRICE_PER_SHARE - UL_ORD_LP) * UL_ORD_QUANTITY)), 0)
                        AS MARGIN_LP,
                    COALESCE(ROUND(SUM((UL_ORD_PRICE_PER_SHARE - UL_ORD_MLP) * UL_ORD_QUANTITY)), 0)
                        AS MARGIN_MLP,
                    COALESCE(ROUND(SUM((UL_ORD_PRICE_PER_SHARE - UL_ORD_LP) * UL_ORD_QUANTITY)), 0)
                        - COALESCE(ROUND(SUM(UL_ORD_INTERMEDIARY_COMMISSION)), 0)
                        AS NET_MARGIN_LP,
                    COALESCE(ROUND(SUM((UL_ORD_PRICE_PER_SHARE - UL_ORD_MLP) * UL_ORD_QUANTITY)), 0)
                        - COALESCE(ROUND(SUM(UL_ORD_INTERMEDIARY_COMMISSION)), 0)
                        AS NET_MARGIN_MLP
                FROM unlisted_orders
                LEFT JOIN unlisted_stocks ON unlisted_orders.UL_ORD_FINCODE = unlisted_stocks.UL_STOCKS_FINCODE
                LEFT JOIN users           ON unlisted_orders.UL_ORD_ADDED_BY = users.uid
                WHERE UL_ORD_STATUS = 'Completed' AND UL_ORD_TYPE = 'Buy'";

        $params = [];

        if ($fromDate) {
            $sql     .= " AND UL_ORD_DATE >= ?";
            $params[] = $fromDate . ' 00:00:00';
        }
        if ($toDate) {
            $sql     .= " AND UL_ORD_DATE <= ?";
            $params[] = $toDate . ' 23:59:59';
        }
        if ($searchtext !== '') {
            if (is_numeric($searchtext)) {
                $sql     .= " AND unlisted_stocks.UL_STOCKS_FINCODE = ?";
                $params[] = (int) $searchtext;
            } else {
                $sql     .= " AND unlisted_stocks.UL_STOCKS_COMPNAME LIKE ?";
                $params[] = '%' . $searchtext . '%';
            }
        }
        if ($advisorSearch !== '') {
            if (is_numeric($advisorSearch)) {
                $sql     .= " AND users.uid = ?";
                $params[] = (int) $advisorSearch;
            } else {
                $sql     .= " AND users.name LIKE ?";
                $params[] = '%' . $advisorSearch . '%';
            }
        }

        $sql  .= " GROUP BY MONTH(UL_ORD_DATE), YEAR(UL_ORD_DATE)";
        $sql  .= " ORDER BY YEAR(UL_ORD_DATE), MONTH(UL_ORD_DATE)";

        $rows    = DB::select($sql, $params);
        $filters = compact('fromDate', 'toDate', 'searchtext', 'advisorSearch');

        return view('admin.pg.partials.margin-rows', compact('rows', 'filters'));
    }

    // ─── AJAX: Margin Dashboard modal ────────────────────────────────────────

    public function marginModal(Request $request)
    {
        if (!$this->canAccessMargin()) return response()->json(['html' => ''], 403);

        $isTotal       = $request->input('is_total', 'no');
        $month         = $request->input('month', '');
        $year          = $request->input('year', '');
        $fromDate      = $request->input('from_date', '');
        $toDate        = $request->input('to_date', '');
        $viewOption    = $request->input('view_option', 'advisor');
        $searchText    = trim($request->input('search_text', ''));
        $advisorSearch = trim($request->input('advisor_search', ''));

        // --- base WHERE ---
        $baseCond = " WHERE UL_ORD_STATUS = 'Completed' AND UL_ORD_TYPE = 'Buy'";
        $params   = [];

        if ($isTotal === 'no' && $month !== '' && $year !== '') {
            $baseCond .= " AND MONTH(UL_ORD_DATE) = ? AND YEAR(UL_ORD_DATE) = ?";
            $params[]  = (int) $month;
            $params[]  = (int) $year;
        } else {
            if ($fromDate) {
                $baseCond .= " AND UL_ORD_DATE >= ?";
                $params[]  = $fromDate . ' 00:00:00';
            }
            if ($toDate) {
                $baseCond .= " AND UL_ORD_DATE <= ?";
                $params[]  = $toDate . ' 23:59:59';
            }
        }
        if ($searchText !== '') {
            $baseCond .= " AND (inner_unlisted_stocks.UL_STOCKS_COMPNAME LIKE ? OR inner_unlisted_stocks.UL_STOCKS_FINCODE LIKE ?)";
            $params[]  = '%' . $searchText . '%';
            $params[]  = '%' . $searchText . '%';
        }
        if ($advisorSearch !== '') {
            $baseCond .= " AND (innerUser.name LIKE ? OR innerUser.uid LIKE ?)";
            $params[]  = '%' . $advisorSearch . '%';
            $params[]  = '%' . $advisorSearch . '%';
        }

        $thStyle = 'padding:9px 11px;font-size:11px;font-weight:700;color:#6c757d;'
                 . 'text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;background:#f8f9fa;';
        $tdStyle = 'padding:9px 11px;font-size:12px;color:#374151;border-bottom:1px solid #f1f3f5;';

        $html = '';

        // ── Advisor / Company / Customer views ─────────────────────────────
        if (in_array($viewOption, ['advisor', 'company', 'customer'])) {

            if ($viewOption === 'company') {
                $grpBy    = 'UL_ORD_FINCODE';
                $nameCol  = 'inner_unlisted_stocks.UL_STOCKS_COMPNAME';
                $leftJoin = 'LEFT JOIN unlisted_stocks AS inner_unlisted_stocks ON margin_calc.UL_ORD_FINCODE = inner_unlisted_stocks.UL_STOCKS_FINCODE';
                $head1    = 'Fincode';
                $head2    = 'Company Name';
            } elseif ($viewOption === 'advisor') {
                $grpBy    = 'UL_ORD_ADDED_BY';
                $nameCol  = 'innerUser.name';
                $leftJoin = 'LEFT JOIN users AS innerUser ON margin_calc.UL_ORD_ADDED_BY = innerUser.uid';
                $head1    = 'Order Added By';
                $head2    = 'Name';
            } else {
                $grpBy    = 'UL_ORD_USER_ID';
                $nameCol  = 'innerUser.name';
                $leftJoin = 'LEFT JOIN users AS innerUser ON margin_calc.UL_ORD_USER_ID = innerUser.uid';
                $head1    = 'UID';
                $head2    = 'Name';
            }

            $sql = "SELECT margin_calc.*, {$nameCol} AS display_name
                    FROM (
                        SELECT {$grpBy},
                            COALESCE(ROUND(SUM(UL_ORD_INTERMEDIARY_COMMISSION)), 0)                                                                          AS BUY_COMMISSION,
                            COALESCE(ROUND(SUM(UL_ORD_AMOUNT)), 0)                                                                                          AS BUY_ORDER_AMOUNT,
                            COALESCE(SUM(UL_ORD_QUANTITY), 0)                                                                                               AS BUY_ORDER_QTY,
                            COALESCE(ROUND(SUM((UL_ORD_PRICE_PER_SHARE - UL_ORD_LP)  * UL_ORD_QUANTITY)), 0)                                                AS MARGIN_LP,
                            COALESCE(ROUND(SUM((UL_ORD_PRICE_PER_SHARE - UL_ORD_MLP) * UL_ORD_QUANTITY)), 0)                                                AS MARGIN_MLP,
                            COALESCE(ROUND(SUM((UL_ORD_PRICE_PER_SHARE - UL_ORD_LP)  * UL_ORD_QUANTITY)), 0) - COALESCE(ROUND(SUM(UL_ORD_INTERMEDIARY_COMMISSION)), 0) AS NET_MARGIN_LP,
                            COALESCE(ROUND(SUM((UL_ORD_PRICE_PER_SHARE - UL_ORD_MLP) * UL_ORD_QUANTITY)), 0) - COALESCE(ROUND(SUM(UL_ORD_INTERMEDIARY_COMMISSION)), 0) AS NET_MARGIN_MLP
                        FROM unlisted_orders
                        LEFT JOIN users           AS innerUser             ON innerUser.uid             = UL_ORD_ADDED_BY
                        LEFT JOIN unlisted_stocks AS inner_unlisted_stocks ON UL_ORD_FINCODE            = inner_unlisted_stocks.UL_STOCKS_FINCODE
                        {$baseCond}
                        GROUP BY {$grpBy}
                    ) AS margin_calc
                    {$leftJoin}
                    ORDER BY NET_MARGIN_LP DESC";

            $rows = DB::select($sql, $params);

            $tBuyComm = $tBuyAmt = $tBuyQty = $tLp = $tMlp = $tNetLp = $tNetMlp = 0;
            $tableRows = '';

            foreach ($rows as $row) {
                $val1 = match ($viewOption) {
                    'company'  => $row->UL_ORD_FINCODE  ?? '—',
                    'advisor'  => $row->UL_ORD_ADDED_BY ?? '—',
                    default    => $row->UL_ORD_USER_ID  ?? '—',
                };
                $val2      = $row->display_name ?? '—';
                $buyAmt    = $row->BUY_ORDER_AMOUNT;
                $netLp     = $row->NET_MARGIN_LP;
                $marginPct = $buyAmt > 0 ? round(($netLp / $buyAmt) * 100, 1) : 0;

                $tableRows .= '<tr>
                    <td style="' . $tdStyle . '">' . htmlspecialchars((string)$val1) . '</td>
                    <td style="' . $tdStyle . '">' . htmlspecialchars((string)$val2) . '</td>
                    <td style="' . $tdStyle . 'text-align:right;">' . number_format($row->BUY_COMMISSION) . '</td>
                    <td style="' . $tdStyle . 'text-align:right;">' . number_format($buyAmt) . '</td>
                    <td style="' . $tdStyle . 'text-align:right;">' . number_format($row->BUY_ORDER_QTY) . '</td>
                    <td style="' . $tdStyle . 'text-align:right;">' . number_format($row->MARGIN_LP) . '</td>
                    <td style="' . $tdStyle . 'text-align:right;">' . number_format($row->MARGIN_MLP) . '</td>
                    <td style="' . $tdStyle . 'text-align:right;font-weight:600;color:#065f46;">' . number_format($netLp) . '</td>
                    <td style="' . $tdStyle . 'text-align:right;">' . $marginPct . '%</td>
                    <td style="' . $tdStyle . 'text-align:right;">' . number_format($row->NET_MARGIN_MLP) . '</td>
                </tr>';

                $tBuyComm += $row->BUY_COMMISSION;
                $tBuyAmt  += $buyAmt;
                $tBuyQty  += $row->BUY_ORDER_QTY;
                $tLp      += $row->MARGIN_LP;
                $tMlp     += $row->MARGIN_MLP;
                $tNetLp   += $netLp;
                $tNetMlp  += $row->NET_MARGIN_MLP;
            }

            $avgPct = $tBuyAmt > 0 ? round(($tNetLp / $tBuyAmt) * 100, 1) : 0;

            $html = '<div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;min-width:900px;">
                    <thead><tr>
                        <th style="' . $thStyle . '">' . $head1 . '</th>
                        <th style="' . $thStyle . '">' . $head2 . '</th>
                        <th style="' . $thStyle . 'text-align:right;">Buy Commission</th>
                        <th style="' . $thStyle . 'text-align:right;">Buy Order Amt</th>
                        <th style="' . $thStyle . 'text-align:right;">Buy Qty</th>
                        <th style="' . $thStyle . 'text-align:right;">LP</th>
                        <th style="' . $thStyle . 'text-align:right;">MLP</th>
                        <th style="' . $thStyle . 'text-align:right;">Net Margin LP</th>
                        <th style="' . $thStyle . 'text-align:right;">Margin %</th>
                        <th style="' . $thStyle . 'text-align:right;">Net Margin MLP</th>
                    </tr></thead>
                    <tbody>' . $tableRows . '
                        <tr style="background:#eff6ff;">
                            <td colspan="2" style="' . $tdStyle . 'font-weight:700;">Total</td>
                            <td style="' . $tdStyle . 'text-align:right;font-weight:700;">' . number_format($tBuyComm) . '</td>
                            <td style="' . $tdStyle . 'text-align:right;font-weight:700;">' . number_format($tBuyAmt) . '</td>
                            <td style="' . $tdStyle . 'text-align:right;font-weight:700;">' . number_format($tBuyQty) . '</td>
                            <td style="' . $tdStyle . 'text-align:right;font-weight:700;">' . number_format($tLp) . '</td>
                            <td style="' . $tdStyle . 'text-align:right;font-weight:700;">' . number_format($tMlp) . '</td>
                            <td style="' . $tdStyle . 'text-align:right;font-weight:700;color:#065f46;">' . number_format($tNetLp) . '</td>
                            <td style="' . $tdStyle . 'text-align:right;font-weight:700;">' . $avgPct . '%</td>
                            <td style="' . $tdStyle . 'text-align:right;font-weight:700;">' . number_format($tNetMlp) . '</td>
                        </tr>
                    </tbody>
                </table></div>';

        // ── Client Size (buyDeal) view ──────────────────────────────────────
        } elseif ($viewOption === 'buyDeal') {

            $innerSelect = "SELECT UL_ORD_USER_ID,
                            SUM(UL_ORD_INTERMEDIARY_COMMISSION)                                              AS BUY_COMMISSION,
                            SUM(UL_ORD_AMOUNT)                                                               AS BUY_ORDER_AMOUNT,
                            SUM(UL_ORD_QUANTITY)                                                             AS BUY_ORDER_QTY,
                            SUM((UL_ORD_PRICE_PER_SHARE - UL_ORD_LP)  * UL_ORD_QUANTITY)                    AS MARGIN_LP,
                            SUM((UL_ORD_PRICE_PER_SHARE - UL_ORD_MLP) * UL_ORD_QUANTITY)                    AS MARGIN_MLP,
                            SUM((UL_ORD_PRICE_PER_SHARE - UL_ORD_LP)  * UL_ORD_QUANTITY) - SUM(UL_ORD_INTERMEDIARY_COMMISSION) AS NET_MARGIN_LP,
                            SUM((UL_ORD_PRICE_PER_SHARE - UL_ORD_MLP) * UL_ORD_QUANTITY) - SUM(UL_ORD_INTERMEDIARY_COMMISSION) AS NET_MARGIN_MLP
                        FROM unlisted_orders
                        LEFT JOIN unlisted_stocks AS inner_unlisted_stocks ON UL_ORD_FINCODE = inner_unlisted_stocks.UL_STOCKS_FINCODE
                        LEFT JOIN users           AS innerUser             ON innerUser.uid   = UL_ORD_ADDED_BY
                        {$baseCond}
                        GROUP BY UL_ORD_USER_ID";

            $buySql = "SELECT
                    CASE
                        WHEN BUY_ORDER_AMOUNT > 2000000            THEN '> 20,00,000'
                        WHEN BUY_ORDER_AMOUNT BETWEEN 1000000 AND 2000000 THEN '10,00,000 - 20,00,000'
                        WHEN BUY_ORDER_AMOUNT BETWEEN 500000  AND 999999  THEN '5,00,000 - 10,00,000'
                        WHEN BUY_ORDER_AMOUNT BETWEEN 200000  AND 499999  THEN '2,00,000 - 5,00,000'
                        ELSE '< 2,00,000'
                    END AS BUY_AMOUNT_BRACKET,
                    COUNT(*)                          AS USER_COUNT,
                    ROUND(SUM(BUY_COMMISSION), 0)     AS TOTAL_COMMISSION,
                    ROUND(SUM(BUY_ORDER_AMOUNT), 0)   AS TOTAL_BUY_ORDER_AMOUNT,
                    ROUND(SUM(BUY_ORDER_QTY), 0)      AS TOTAL_BUY_ORDER_QTY,
                    ROUND(SUM(MARGIN_LP), 0)           AS TOTAL_MARGIN_LP,
                    ROUND(SUM(MARGIN_MLP), 0)          AS TOTAL_MARGIN_MLP,
                    ROUND(SUM(NET_MARGIN_LP), 0)       AS TOTAL_NET_MARGIN_LP,
                    ROUND(SUM(NET_MARGIN_MLP), 0)      AS TOTAL_NET_MARGIN_MLP
                FROM ({$innerSelect}) AS margin_calc
                GROUP BY BUY_AMOUNT_BRACKET
                ORDER BY TOTAL_BUY_ORDER_AMOUNT DESC";

            $dealSql = "SELECT
                    CASE
                        WHEN DEAL_COUNT >= 9               THEN '9+ deals'
                        WHEN DEAL_COUNT BETWEEN 5 AND 8   THEN '5-9 deals'
                        WHEN DEAL_COUNT IN (4, 5)         THEN '4-5 deals'
                        WHEN DEAL_COUNT BETWEEN 2 AND 3   THEN '2-3 deals'
                        ELSE '1 deal'
                    END AS DEAL_COUNT_BRACKET,
                    COUNT(*)                          AS USER_COUNT,
                    ROUND(SUM(BUY_COMMISSION), 0)     AS TOTAL_COMMISSION,
                    ROUND(SUM(BUY_ORDER_AMOUNT), 0)   AS TOTAL_BUY_ORDER_AMOUNT,
                    ROUND(SUM(BUY_ORDER_QTY), 0)      AS TOTAL_BUY_ORDER_QTY,
                    ROUND(SUM(MARGIN_LP), 0)           AS TOTAL_MARGIN_LP,
                    ROUND(SUM(MARGIN_MLP), 0)          AS TOTAL_MARGIN_MLP,
                    ROUND(SUM(NET_MARGIN_LP), 0)       AS TOTAL_NET_MARGIN_LP,
                    ROUND(SUM(NET_MARGIN_MLP), 0)      AS TOTAL_NET_MARGIN_MLP
                FROM (
                    SELECT UL_ORD_USER_ID, COUNT(*) AS DEAL_COUNT,
                            SUM(UL_ORD_INTERMEDIARY_COMMISSION)                                              AS BUY_COMMISSION,
                            SUM(UL_ORD_AMOUNT)                                                               AS BUY_ORDER_AMOUNT,
                            SUM(UL_ORD_QUANTITY)                                                             AS BUY_ORDER_QTY,
                            SUM((UL_ORD_PRICE_PER_SHARE - UL_ORD_LP)  * UL_ORD_QUANTITY)                    AS MARGIN_LP,
                            SUM((UL_ORD_PRICE_PER_SHARE - UL_ORD_MLP) * UL_ORD_QUANTITY)                    AS MARGIN_MLP,
                            SUM((UL_ORD_PRICE_PER_SHARE - UL_ORD_LP)  * UL_ORD_QUANTITY) - SUM(UL_ORD_INTERMEDIARY_COMMISSION) AS NET_MARGIN_LP,
                            SUM((UL_ORD_PRICE_PER_SHARE - UL_ORD_MLP) * UL_ORD_QUANTITY) - SUM(UL_ORD_INTERMEDIARY_COMMISSION) AS NET_MARGIN_MLP
                        FROM unlisted_orders
                        LEFT JOIN unlisted_stocks AS inner_unlisted_stocks ON UL_ORD_FINCODE = inner_unlisted_stocks.UL_STOCKS_FINCODE
                        LEFT JOIN users           AS innerUser             ON innerUser.uid   = UL_ORD_ADDED_BY
                        {$baseCond}
                        GROUP BY UL_ORD_USER_ID
                ) AS margin_calc
                GROUP BY DEAL_COUNT_BRACKET
                ORDER BY CASE DEAL_COUNT_BRACKET
                    WHEN '9+ deals'  THEN 1 WHEN '5-9 deals' THEN 2
                    WHEN '4-5 deals' THEN 3 WHEN '2-3 deals' THEN 4 ELSE 5
                END";

            $buyRows  = DB::select($buySql, $params);
            $dealRows = DB::select($dealSql, $params);

            $html = $this->buildClientSizeHtml($buyRows, $dealRows, $thStyle, $tdStyle);
        }

        return response()->json(['html' => $html]);
    }

    // ─── AJAX: Margin Error table ─────────────────────────────────────────────

    public function marginErrorData(Request $request)
    {
        if (!$this->canAccessMarginError()) abort(403);

        $rows = DB::select("SELECT * FROM (
                    SELECT
                        o.UL_ORD_FINCODE,
                        s.UL_STOCKS_COMPNAME,
                        COALESCE(ROUND(SUM(CASE WHEN o.UL_ORD_TYPE = 'Buy'  THEN o.UL_ORD_INTERMEDIARY_COMMISSION ELSE 0 END)), 0) AS BUY_COMMISSION,
                        COALESCE(ROUND(SUM(CASE WHEN o.UL_ORD_TYPE = 'Sell' THEN o.UL_ORD_INTERMEDIARY_COMMISSION ELSE 0 END)), 0) AS SELL_COMMISSION,
                        COALESCE(ROUND(SUM(CASE WHEN o.UL_ORD_TYPE = 'Buy'  THEN o.UL_ORD_AMOUNT                 ELSE 0 END)), 0) AS BUY_ORDER_AMOUNT,
                        COALESCE(ROUND(SUM(CASE WHEN o.UL_ORD_TYPE = 'Sell' THEN o.UL_ORD_AMOUNT                 ELSE 0 END)), 0) AS SELL_ORDER_AMOUNT,
                        COALESCE(SUM(CASE WHEN o.UL_ORD_TYPE = 'Buy'  THEN o.UL_ORD_QUANTITY ELSE 0 END), 0)                      AS BUY_ORDER_QTY,
                        COALESCE(SUM(CASE WHEN o.UL_ORD_TYPE = 'Sell' THEN o.UL_ORD_QUANTITY ELSE 0 END), 0)                      AS SELL_ORDER_QTY,
                        COALESCE(ROUND(SUM(CASE WHEN o.UL_ORD_TYPE = 'Buy'  THEN (o.UL_ORD_PRICE_PER_SHARE - o.UL_ORD_LP)  * o.UL_ORD_QUANTITY ELSE 0 END)), 0) AS MARGIN_LP,
                        COALESCE(ROUND(SUM(CASE WHEN o.UL_ORD_TYPE = 'Buy'  THEN (o.UL_ORD_PRICE_PER_SHARE - o.UL_ORD_MLP) * o.UL_ORD_QUANTITY ELSE 0 END)), 0) AS MARGIN_MLP,
                        ROUND(
                            COALESCE(SUM(CASE WHEN o.UL_ORD_TYPE = 'Buy'  THEN o.UL_ORD_AMOUNT ELSE 0 END), 0)
                            - COALESCE(SUM(CASE WHEN o.UL_ORD_TYPE = 'Sell' THEN o.UL_ORD_AMOUNT ELSE 0 END), 0)
                            - COALESCE(SUM(CASE WHEN o.UL_ORD_TYPE = 'Buy'  THEN (o.UL_ORD_PRICE_PER_SHARE - o.UL_ORD_LP) * o.UL_ORD_QUANTITY ELSE 0 END), 0)
                            - COALESCE(SUM(CASE WHEN o.UL_ORD_TYPE = 'Sell' THEN o.UL_ORD_INTERMEDIARY_COMMISSION ELSE 0 END), 0)
                        ) AS DIFFERENCE
                    FROM unlisted_orders AS o
                    LEFT JOIN unlisted_stocks AS s ON o.UL_ORD_FINCODE = s.UL_STOCKS_FINCODE
                    WHERE o.UL_ORD_STATUS = 'Completed'
                    GROUP BY o.UL_ORD_FINCODE, s.UL_STOCKS_COMPNAME
                ) AS merr
                ORDER BY ABS(DIFFERENCE) DESC");

        return view('admin.pg.partials.margin-error-rows', compact('rows'));
    }

    // ─── AJAX: Margin Error modal (month breakdown for one fincode) ───────────

    public function marginErrorModal(Request $request)
    {
        if (!$this->canAccessMarginError()) return response()->json(['html' => ''], 403);

        $fincode = (int) $request->input('fincode', 0);
        if (!$fincode) {
            return response()->json(['html' => '<p style="color:#b91c1c;padding:20px;">Invalid fincode.</p>']);
        }

        $rows = DB::select("SELECT
                    MONTH(UL_ORD_DATE)  AS ord_month,
                    YEAR(UL_ORD_DATE)   AS ord_year,
                    COALESCE(ROUND(SUM(CASE WHEN UL_ORD_TYPE = 'Buy'  THEN UL_ORD_INTERMEDIARY_COMMISSION ELSE 0 END)), 0) AS BUY_COMMISSION,
                    COALESCE(ROUND(SUM(CASE WHEN UL_ORD_TYPE = 'Sell' THEN UL_ORD_INTERMEDIARY_COMMISSION ELSE 0 END)), 0) AS SELL_COMMISSION,
                    COALESCE(ROUND(SUM(CASE WHEN UL_ORD_TYPE = 'Buy'  THEN UL_ORD_AMOUNT                 ELSE 0 END)), 0) AS BUY_ORDER_AMOUNT,
                    COALESCE(ROUND(SUM(CASE WHEN UL_ORD_TYPE = 'Sell' THEN UL_ORD_AMOUNT                 ELSE 0 END)), 0) AS SELL_ORDER_AMOUNT,
                    COALESCE(SUM(CASE WHEN UL_ORD_TYPE = 'Buy'  THEN UL_ORD_QUANTITY ELSE 0 END), 0)                      AS BUY_ORDER_QTY,
                    COALESCE(SUM(CASE WHEN UL_ORD_TYPE = 'Sell' THEN UL_ORD_QUANTITY ELSE 0 END), 0)                      AS SELL_ORDER_QTY,
                    COALESCE(ROUND(SUM(CASE WHEN UL_ORD_TYPE = 'Buy'  THEN (UL_ORD_PRICE_PER_SHARE - UL_ORD_LP)  * UL_ORD_QUANTITY ELSE 0 END)), 0) AS MARGIN_LP,
                    COALESCE(ROUND(SUM(CASE WHEN UL_ORD_TYPE = 'Buy'  THEN (UL_ORD_PRICE_PER_SHARE - UL_ORD_MLP) * UL_ORD_QUANTITY ELSE 0 END)), 0) AS MARGIN_MLP,
                    ROUND(
                        COALESCE(SUM(CASE WHEN UL_ORD_TYPE = 'Buy'  THEN UL_ORD_AMOUNT ELSE 0 END), 0)
                        - COALESCE(SUM(CASE WHEN UL_ORD_TYPE = 'Sell' THEN UL_ORD_AMOUNT ELSE 0 END), 0)
                        - COALESCE(SUM(CASE WHEN UL_ORD_TYPE = 'Buy'  THEN (UL_ORD_PRICE_PER_SHARE - UL_ORD_LP) * UL_ORD_QUANTITY ELSE 0 END), 0)
                        - COALESCE(SUM(CASE WHEN UL_ORD_TYPE = 'Sell' THEN UL_ORD_INTERMEDIARY_COMMISSION ELSE 0 END), 0)
                    ) AS DIFFERENCE
                FROM unlisted_orders
                WHERE UL_ORD_STATUS = 'Completed' AND UL_ORD_FINCODE = ?
                GROUP BY MONTH(UL_ORD_DATE), YEAR(UL_ORD_DATE)
                ORDER BY YEAR(UL_ORD_DATE), MONTH(UL_ORD_DATE)", [$fincode]);

        $months  = ['','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $thStyle = 'padding:9px 11px;font-size:11px;font-weight:700;color:#6c757d;'
                 . 'text-transform:uppercase;letter-spacing:0.05em;white-space:nowrap;background:#f8f9fa;';
        $tdStyle = 'padding:9px 11px;font-size:12px;color:#374151;border-bottom:1px solid #f1f3f5;';

        $tableRows = '';
        foreach ($rows as $row) {
            $diff      = $row->DIFFERENCE;
            $diffColor = $diff < 0 ? '#b91c1c' : ($diff > 0 ? '#065f46' : '#374151');
            $tableRows .= '<tr>
                <td style="' . $tdStyle . '">' . ($months[$row->ord_month] ?? $row->ord_month) . '</td>
                <td style="' . $tdStyle . '">' . $row->ord_year . '</td>
                <td style="' . $tdStyle . 'text-align:right;">' . number_format($row->BUY_COMMISSION) . '</td>
                <td style="' . $tdStyle . 'text-align:right;">' . number_format($row->SELL_COMMISSION) . '</td>
                <td style="' . $tdStyle . 'text-align:right;">' . number_format($row->BUY_ORDER_AMOUNT) . '</td>
                <td style="' . $tdStyle . 'text-align:right;">' . number_format($row->SELL_ORDER_AMOUNT) . '</td>
                <td style="' . $tdStyle . 'text-align:right;">' . number_format($row->BUY_ORDER_QTY) . '</td>
                <td style="' . $tdStyle . 'text-align:right;">' . number_format($row->SELL_ORDER_QTY) . '</td>
                <td style="' . $tdStyle . 'text-align:right;">' . number_format($row->MARGIN_LP) . '</td>
                <td style="' . $tdStyle . 'text-align:right;">' . number_format($row->MARGIN_MLP) . '</td>
                <td style="' . $tdStyle . 'text-align:right;font-weight:700;color:' . $diffColor . ';">' . number_format($diff) . '</td>
            </tr>';
        }

        if (empty($rows)) {
            $tableRows = '<tr><td colspan="11" style="text-align:center;padding:30px;color:#9ca3af;">No data found</td></tr>';
        }

        $html = '<div style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse;min-width:900px;">
            <thead><tr>
                <th style="' . $thStyle . '">Month</th>
                <th style="' . $thStyle . '">Year</th>
                <th style="' . $thStyle . 'text-align:right;">Buy Comm.</th>
                <th style="' . $thStyle . 'text-align:right;">Sell Comm.</th>
                <th style="' . $thStyle . 'text-align:right;">Buy Amt</th>
                <th style="' . $thStyle . 'text-align:right;">Sell Amt</th>
                <th style="' . $thStyle . 'text-align:right;">Buy Qty</th>
                <th style="' . $thStyle . 'text-align:right;">Sell Qty</th>
                <th style="' . $thStyle . 'text-align:right;">LP</th>
                <th style="' . $thStyle . 'text-align:right;">MLP</th>
                <th style="' . $thStyle . 'text-align:right;">Difference</th>
            </tr></thead>
            <tbody>' . $tableRows . '</tbody>
        </table></div>';

        return response()->json(['html' => $html]);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function buildClientSizeHtml(array $buyRows, array $dealRows, string $thStyle, string $tdStyle): string
    {
        $cols = [
            'Total Buy Amt', 'Buy Qty', 'Commission', 'Margin LP',
            'Margin MLP', 'Net Margin LP', 'Net Margin MLP', 'Users',
        ];

        $buildTable = function (array $rows, string $bracketKey, string $bracketHead) use ($thStyle, $tdStyle, $cols): string {
            $t = ['amt'=>0,'qty'=>0,'comm'=>0,'lp'=>0,'mlp'=>0,'netLp'=>0,'netMlp'=>0,'users'=>0];
            $body = '';
            foreach ($rows as $row) {
                $body .= '<tr>
                    <td style="' . $tdStyle . '">' . $row->$bracketKey . '</td>
                    <td style="' . $tdStyle . 'text-align:right;">' . number_format($row->TOTAL_BUY_ORDER_AMOUNT) . '</td>
                    <td style="' . $tdStyle . 'text-align:right;">' . number_format($row->TOTAL_BUY_ORDER_QTY) . '</td>
                    <td style="' . $tdStyle . 'text-align:right;">' . number_format($row->TOTAL_COMMISSION) . '</td>
                    <td style="' . $tdStyle . 'text-align:right;">' . number_format($row->TOTAL_MARGIN_LP) . '</td>
                    <td style="' . $tdStyle . 'text-align:right;">' . number_format($row->TOTAL_MARGIN_MLP) . '</td>
                    <td style="' . $tdStyle . 'text-align:right;font-weight:600;color:#065f46;">' . number_format($row->TOTAL_NET_MARGIN_LP) . '</td>
                    <td style="' . $tdStyle . 'text-align:right;">' . number_format($row->TOTAL_NET_MARGIN_MLP) . '</td>
                    <td style="' . $tdStyle . 'text-align:right;">' . $row->USER_COUNT . '</td>
                </tr>';
                $t['amt']    += $row->TOTAL_BUY_ORDER_AMOUNT;
                $t['qty']    += $row->TOTAL_BUY_ORDER_QTY;
                $t['comm']   += $row->TOTAL_COMMISSION;
                $t['lp']     += $row->TOTAL_MARGIN_LP;
                $t['mlp']    += $row->TOTAL_MARGIN_MLP;
                $t['netLp']  += $row->TOTAL_NET_MARGIN_LP;
                $t['netMlp'] += $row->TOTAL_NET_MARGIN_MLP;
                $t['users']  += $row->USER_COUNT;
            }
            $headers = '<th style="' . $thStyle . '">' . $bracketHead . '</th>';
            foreach ($cols as $c) {
                $headers .= '<th style="' . $thStyle . 'text-align:right;">' . $c . '</th>';
            }
            $totalRow = '<tr style="background:#eff6ff;">
                <td style="' . $tdStyle . 'font-weight:700;">Total</td>
                <td style="' . $tdStyle . 'text-align:right;font-weight:700;">' . number_format($t['amt']) . '</td>
                <td style="' . $tdStyle . 'text-align:right;font-weight:700;">' . number_format($t['qty']) . '</td>
                <td style="' . $tdStyle . 'text-align:right;font-weight:700;">' . number_format($t['comm']) . '</td>
                <td style="' . $tdStyle . 'text-align:right;font-weight:700;">' . number_format($t['lp']) . '</td>
                <td style="' . $tdStyle . 'text-align:right;font-weight:700;">' . number_format($t['mlp']) . '</td>
                <td style="' . $tdStyle . 'text-align:right;font-weight:700;color:#065f46;">' . number_format($t['netLp']) . '</td>
                <td style="' . $tdStyle . 'text-align:right;font-weight:700;">' . number_format($t['netMlp']) . '</td>
                <td style="' . $tdStyle . 'text-align:right;font-weight:700;">' . $t['users'] . '</td>
            </tr>';
            return '<div style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse;min-width:800px;">
                <thead><tr>' . $headers . '</tr></thead>
                <tbody>' . $body . $totalRow . '</tbody>
            </table></div>';
        };

        return '<h6 style="font-weight:700;margin:0 0 10px 0;font-size:13px;color:#374151;">Buy Orders</h6>'
             . $buildTable($buyRows, 'BUY_AMOUNT_BRACKET', 'Buy Amt Bracket')
             . '<h6 style="font-weight:700;margin:20px 0 10px 0;font-size:13px;color:#374151;">Deal Counts</h6>'
             . $buildTable($dealRows, 'DEAL_COUNT_BRACKET', 'Deal Count Bracket');
    }

    // ─── PG Dashboard ────────────────────────────────────────────────────────

    public function pgDashboard(Request $request)
    {
        if (!$this->canAccessPgDashboard()) abort(403);

        $fromDate = $request->input('from_date', '');
        $toDate   = $request->input('to_date', '');
        $hasDates = $fromDate && $toDate;

        $pgtDate  = $hasDates ? 'AND pgt_transaction_date >= ? AND pgt_transaction_date <= ?' : '';
        $ordDate  = $hasDates ? 'AND UL_ORD_DATE >= ? AND UL_ORD_DATE <= ?' : '';
        $dmatDate = $hasDates ? 'AND DEMAT_DATE >= ? AND DEMAT_DATE <= ?' : '';
        $reqDate  = $hasDates ? 'AND REQUEST_DATE >= ? AND REQUEST_DATE <= ?' : '';
        $o1Date   = $hasDates ? 'AND o1.UL_ORD_DATE >= ? AND o1.UL_ORD_DATE <= ?' : '';

        $pgtP  = $hasDates ? [$fromDate . ' 00:00:01', $toDate . ' 23:59:59'] : [];
        $ordP  = $hasDates ? [$fromDate . ' 00:00:01', $toDate . ' 23:59:59'] : [];
        $dmatP = $hasDates ? [$fromDate . ' 00:00:01', $toDate . ' 23:59:59'] : [];
        $reqP  = $hasDates ? [$fromDate . ' 00:00:01', $toDate . ' 23:59:59'] : [];

        // ── Open Transactions (unmapped, no user) ─────────────────────────────
        $openTransRows = DB::select("SELECT
            pgt_tid,
            pgt_in_out_amount,
            pgt_transaction_type,
            pgt_ref_no,
            CASE WHEN pgt_transaction_type = 'Flow In'  THEN pgt_in_out_amount END AS flow_in_amount,
            CASE WHEN pgt_transaction_type = 'Flow Out' THEN pgt_in_out_amount END AS flow_out_amount,
            DATEDIFF(CURDATE(), pgt_transaction_date) AS ageing
        FROM pg_transactions
        WHERE 1 {$pgtDate}
            AND pgt_transaction_for_user_id <= 0
            AND pgt_from_to != 'Company'
            AND NOT (pgt_from_to = 'PG'           AND pgt_bank_account = 'Bank')
            AND NOT (pgt_from_to = 'Bank'          AND pgt_bank_account = 'PG')
            AND NOT (pgt_from_to = 'ICICI Bank'    AND pgt_bank_account = 'PG')
            AND NOT (pgt_from_to = 'PG'            AND pgt_bank_account = 'ICICI Bank')
            AND NOT (pgt_from_to = 'ICICI Bank'    AND pgt_bank_account = 'Bank')
            AND NOT (pgt_from_to = 'Bank'          AND pgt_bank_account = 'ICICI Bank')
            AND NOT (pgt_from_to = 'Bandhan Bank'  AND pgt_bank_account = 'PG')
            AND NOT (pgt_from_to = 'PG'            AND pgt_bank_account = 'Bandhan Bank')
            AND NOT (pgt_from_to = 'Bandhan Bank'  AND pgt_bank_account = 'Bank')
            AND NOT (pgt_from_to = 'Bank'          AND pgt_bank_account = 'Bandhan Bank')
            AND NOT (pgt_from_to = 'Bandhan Bank'  AND pgt_bank_account = 'ICICI Bank')
            AND NOT (pgt_from_to = 'ICICI Bank'    AND pgt_bank_account = 'Bandhan Bank')", $pgtP);

        $openTransInTotal  = 0; $openTransInCount  = 0;
        $openTransOutTotal = 0; $openTransOutCount = 0;
        foreach ($openTransRows as $r) {
            if ($r->pgt_transaction_type === 'Flow In') {
                $openTransInTotal += $r->pgt_in_out_amount; $openTransInCount++;
            } else {
                $openTransOutTotal += $r->pgt_in_out_amount; $openTransOutCount++;
            }
        }

        // ── User Balance ──────────────────────────────────────────────────────
        $userBalSql = "SELECT masterTbl.*,
            ulLeads.UL_LEAD_ALLOCATED_TO AS UL_ORD_ADDED_BY,
            addedByName.name AS order_added_by_name,
            REQUEST_AMOUNT, users.name,
            users.bank_holder_name AS name_in_bank,
            users.bank_name, users.bank_account_no,
            users.bank_ifsc_code AS IFSC
        FROM (
            SELECT
                user_id,
                DATEDIFF(NOW(), MAX(date_time)) AS number_of_days_order,
                ROUND(SUM(debit_amount), 2) AS debits,
                ROUND(SUM(credit_amount), 2) AS credits,
                ROUND(SUM(credit_amount) - SUM(debit_amount), 2) AS balance
            FROM (
                SELECT UL_ORD_USER_ID AS user_id, UL_ORD_UPDATE_TIME AS date_time,
                    CASE WHEN UL_ORD_TYPE = 'Buy'  THEN UL_ORD_AMOUNT ELSE 0 END AS debit_amount,
                    CASE WHEN UL_ORD_TYPE = 'Sell' THEN UL_ORD_AMOUNT ELSE 0 END AS credit_amount
                FROM unlisted_orders WHERE UL_ORD_STATUS = 'Completed' {$ordDate}
                UNION ALL
                SELECT pgt_transaction_for_user_id AS user_id, pgt_created_on AS date_time,
                    CASE WHEN pgt_transaction_type = 'Flow Out' THEN pgt_in_out_amount ELSE 0 END AS debit_amount,
                    CASE WHEN pgt_transaction_type = 'Flow In'  THEN pgt_in_out_amount ELSE 0 END AS credit_amount
                FROM pg_transactions WHERE pgt_from_to = 'Customer' {$pgtDate}
                UNION ALL
                SELECT UL_ORD_INTERMEDIARY_USER_ID AS user_id, UL_ORD_UPDATE_TIME AS date_time,
                    0 AS debit_amount, UL_ORD_INTERMEDIARY_COMMISSION AS credit_amount
                FROM unlisted_orders WHERE UL_ORD_STATUS = 'Completed' AND UL_ORD_INTERMEDIARY_USER_ID > 0 {$ordDate}
            ) AS all_tx
            GROUP BY user_id ORDER BY balance DESC
        ) AS masterTbl
        LEFT JOIN unlisted_leads AS ulLeads ON ulLeads.UL_LEAD_UID = masterTbl.user_id
        LEFT JOIN users AS addedByName ON addedByName.uid = ulLeads.UL_LEAD_ALLOCATED_TO
        LEFT JOIN users ON users.uid = masterTbl.user_id
        LEFT JOIN (
            SELECT REQUEST_USER_ID, REQUEST_AMOUNT FROM withdrawal_request
            WHERE REQUEST_TYPE = 'Cash' AND (REQUEST_STATUS IS NULL OR REQUEST_STATUS = '' OR REQUEST_STATUS = 'Pending')
        ) AS wdraw ON masterTbl.user_id = wdraw.REQUEST_USER_ID";

        $userBalParams  = array_merge($ordP, $pgtP, $ordP);
        $userBalRows    = DB::select($userBalSql . ' WHERE (balance >= 100 OR balance < 0)', $userBalParams);
        $userBalPending = DB::select($userBalSql . ' WHERE balance < 0', $userBalParams);

        $totalLiabilityBal = 0; $totalLiabilityBalCnt = 0;
        foreach ($userBalRows as $r) {
            if (($r->balance <= -1 || $r->balance >= 1) && $r->user_id > 0) {
                $totalLiabilityBal += $r->balance; $totalLiabilityBalCnt++;
            }
        }

        // ── ICICI Bank Balance ────────────────────────────────────────────────
        $iciciBankBalance = round(DB::select(
            "SELECT COALESCE(pgt_balance,0) AS v FROM pg_transactions WHERE pgt_bank_account='ICICI Bank' ORDER BY pgt_tid DESC LIMIT 1",
            [])[0]->v ?? 0, 2);

        // ── Bandhan Bank Balance ──────────────────────────────────────────────
        $bandhanBankBalance = round(DB::select(
            "SELECT COALESCE(pgt_balance,0) AS v FROM pg_transactions WHERE pgt_bank_account='Bandhan Bank' ORDER BY pgt_tid DESC LIMIT 1",
            [])[0]->v ?? 0, 2);

        // ── PG & Bank Contra Pending ──────────────────────────────────────────
        $pgBankContra = round(DB::select(
            "SELECT SUM(CASE WHEN pgt_transaction_type='Flow Out' THEN pgt_in_out_amount ELSE 0 END)
                  - SUM(CASE WHEN pgt_transaction_type='Flow In'  THEN pgt_in_out_amount ELSE 0 END) AS v
             FROM pg_transactions
             WHERE ((pgt_from_to='PG' AND pgt_bank_account='Bank') OR (pgt_from_to='Bank' AND pgt_bank_account='PG')) {$pgtDate}",
            $pgtP)[0]->v ?? 0, 2);

        // ── Company Balance ───────────────────────────────────────────────────
        $companyBalRow = DB::select(
            "SELECT SUM(CASE WHEN pgt_transaction_type='Flow In' THEN pgt_in_out_amount ELSE -pgt_in_out_amount END) AS total_amount,
                    COUNT(*) AS records_count
             FROM pg_transactions
             WHERE (pgt_bank_account IN ('ICICI Bank','Bandhan Bank') AND pgt_from_to='Company') {$pgtDate}", $pgtP);
        $companyBalance      = round($companyBalRow[0]->total_amount ?? 0, 2);
        $companyBalanceCount = (int)($companyBalRow[0]->records_count ?? 0);

        // ── Totals ────────────────────────────────────────────────────────────
        $unusedTransBalIn  = round($openTransInTotal, 2);
        $unusedTransBalOut = round($openTransOutTotal, 2);
        $assetTotal        = $unusedTransBalOut + $iciciBankBalance + $bandhanBankBalance + $pgBankContra;
        $userBal           = round($totalLiabilityBal, 2);
        $plOrStock         = $assetTotal - ($unusedTransBalIn + $userBal + $companyBalance);
        $liabilityTotal    = $unusedTransBalIn + $userBal + $companyBalance + $plOrStock;

        // ── Order Report ──────────────────────────────────────────────────────
        $orderReportRows = DB::select(
            "SELECT UL_ORD_TYPE, SUM(UL_ORD_AMOUNT) AS total_amount,
                    SUM(UL_ORD_INTERMEDIARY_COMMISSION) AS total_commission
             FROM unlisted_orders WHERE UL_ORD_STATUS='Completed' {$ordDate}
             GROUP BY UL_ORD_TYPE", $ordP);

        // ── Company Holdings (company-wise net qty) ───────────────────────────
        $holdingsRows = DB::select(
            "SELECT masterTbl.*, ULP.last_traded_price, unlisted_stocks.UL_STOCKS_S_NAME
            FROM (
                SELECT company_id,
                    SUM(debit_qty) AS debits, SUM(credit_qty) AS credits,
                    SUM(credit_qty) - SUM(debit_qty) AS balance
                FROM (
                    SELECT UL_ORD_FINCODE AS company_id,
                        CASE WHEN UL_ORD_TYPE='Buy'  THEN UL_ORD_QUANTITY ELSE 0 END AS credit_qty,
                        CASE WHEN UL_ORD_TYPE='Sell' THEN UL_ORD_QUANTITY ELSE 0 END AS debit_qty
                    FROM unlisted_orders WHERE UL_ORD_STATUS='Completed' {$ordDate}
                ) AS d GROUP BY company_id HAVING balance != 0
            ) AS masterTbl
            LEFT JOIN (
                SELECT o1.UL_ORD_FINCODE, MAX(o1.UL_ORD_PRICE_PER_SHARE) AS last_traded_price
                FROM unlisted_orders o1
                INNER JOIN (
                    SELECT UL_ORD_FINCODE, MAX(UL_ORD_DATE) AS latest_order_date
                    FROM unlisted_orders WHERE UL_ORD_TYPE='Sell' AND UL_ORD_STATUS='Completed' {$ordDate}
                    GROUP BY UL_ORD_FINCODE
                ) latest ON o1.UL_ORD_FINCODE = latest.UL_ORD_FINCODE
                    AND o1.UL_ORD_DATE = latest.latest_order_date
                    AND o1.UL_ORD_TYPE = 'Sell' AND o1.UL_ORD_STATUS = 'Completed' {$o1Date}
                GROUP BY o1.UL_ORD_FINCODE
            ) AS ULP ON ULP.UL_ORD_FINCODE = masterTbl.company_id
            LEFT JOIN unlisted_stocks ON unlisted_stocks.UL_STOCKS_FINCODE = masterTbl.company_id",
            array_merge($ordP, $ordP, $ordP));

        $holdingsTotal = 0;
        foreach ($holdingsRows as $r) {
            $holdingsTotal += ($r->balance ?? 0) * ($r->last_traded_price ?? 0) * -1;
        }

        // ── User Wise Demat Balance ───────────────────────────────────────────
        $userWiseRows = DB::select(
            "SELECT masterTbl.*, UL_STOCKS_S_NAME, UL_STOCKS_COMPNAME, users.name,
                REQUEST_QTY, users.demat_dp_id AS DP_ID,
                users.bank_holder_name AS beneficiary_name,
                users.bank_name, users.bank_account_no, users.user_pan_no AS PAN_number
            FROM (
                SELECT user_id, company_id,
                    SUM(debit_qty) AS debits, SUM(credit_qty) AS credits,
                    SUM(credit_qty) - SUM(debit_qty) AS balance,
                    MAX(share_price_per_quantity) AS share_price_per_quantity
                FROM (
                    SELECT UL_ORD_USER_ID AS user_id, UL_ORD_FINCODE AS company_id,
                        UL_ORD_PRICE_PER_SHARE AS share_price_per_quantity,
                        CASE WHEN UL_ORD_TYPE='Buy'  THEN UL_ORD_QUANTITY ELSE 0 END AS credit_qty,
                        CASE WHEN UL_ORD_TYPE='Sell' THEN UL_ORD_QUANTITY ELSE 0 END AS debit_qty,
                        UL_ORD_UPDATE_TIME AS date_time
                    FROM unlisted_orders WHERE UL_ORD_STATUS='Completed' {$ordDate}
                    UNION ALL
                    SELECT DEMAT_USER_ID AS user_id, DEMAT_FINCODE AS company_id,
                        DEMAT_QTY AS share_price_per_quantity,
                        CASE WHEN DEMAT_IN_OUT_FLAG='Flow Out' THEN DEMAT_QTY ELSE 0 END AS credit_qty,
                        CASE WHEN DEMAT_IN_OUT_FLAG='Flow In'  THEN DEMAT_QTY ELSE 0 END AS debit_qty,
                        DEMAT_DATE AS date_time
                    FROM demat_transactions WHERE 1 {$dmatDate}
                ) AS all_demat
                GROUP BY user_id, company_id HAVING (SUM(credit_qty) - SUM(debit_qty)) != 0 ORDER BY user_id ASC
            ) AS masterTbl
            LEFT JOIN unlisted_stocks ON unlisted_stocks.UL_STOCKS_FINCODE = masterTbl.company_id
            LEFT JOIN users ON users.uid = masterTbl.user_id
            LEFT JOIN (
                SELECT REQUEST_USER_ID, REQUEST_FINCODE, REQUEST_QTY FROM withdrawal_request
                WHERE REQUEST_TYPE='Shares' AND (REQUEST_STATUS IS NULL OR REQUEST_STATUS='' OR REQUEST_STATUS='Pending')
            ) AS wdraw ON masterTbl.company_id = wdraw.REQUEST_FINCODE AND masterTbl.user_id = wdraw.REQUEST_USER_ID",
            array_merge($ordP, $dmatP));

        // ── Pending Withdrawal Requests ───────────────────────────────────────
        $pendingWdrawRows = DB::select(
            "SELECT withdrawal_request.*, users.name, unlisted_stocks.UL_STOCKS_S_NAME
             FROM withdrawal_request
             LEFT JOIN users ON users.uid = withdrawal_request.REQUEST_USER_ID
             LEFT JOIN unlisted_stocks ON unlisted_stocks.UL_STOCKS_FINCODE = withdrawal_request.REQUEST_FINCODE
             WHERE (REQUEST_STATUS IS NULL OR REQUEST_STATUS='' OR REQUEST_STATUS='Pending') {$reqDate}",
            $reqP);

        return view('admin.pg.dashboard', compact(
            'fromDate', 'toDate',
            'openTransRows', 'openTransInTotal', 'openTransOutTotal', 'openTransInCount', 'openTransOutCount',
            'userBalRows', 'totalLiabilityBal', 'totalLiabilityBalCnt',
            'userBalPending',
            'unusedTransBalIn', 'unusedTransBalOut',
            'iciciBankBalance', 'bandhanBankBalance',
            'pgBankContra', 'companyBalance', 'companyBalanceCount',
            'assetTotal', 'liabilityTotal', 'plOrStock', 'userBal',
            'orderReportRows',
            'holdingsRows', 'holdingsTotal',
            'userWiseRows',
            'pendingWdrawRows'
        ));
    }

    // ─── Export: User Balance with Bank Details ───────────────────────────────

    public function exportUserBalance(Request $request)
    {
        if (!$this->canAccessPgDashboard()) abort(403);

        $fromDate = $request->input('from_date', '');
        $toDate   = $request->input('to_date', '');
        $hasDates = $fromDate && $toDate;

        $pgtDate = $hasDates ? 'AND pgt_transaction_date >= ? AND pgt_transaction_date <= ?' : '';
        $ordDate = $hasDates ? 'AND UL_ORD_DATE >= ? AND UL_ORD_DATE <= ?' : '';
        $ordP    = $hasDates ? [$fromDate . ' 00:00:01', $toDate . ' 23:59:59'] : [];
        $pgtP    = $hasDates ? [$fromDate . ' 00:00:01', $toDate . ' 23:59:59'] : [];

        $sql = "SELECT masterTbl.*,
            ulLeads.UL_LEAD_ALLOCATED_TO AS UL_ORD_ADDED_BY,
            addedByName.name AS order_added_by_name,
            REQUEST_AMOUNT, users.name,
            users.bank_holder_name AS name_in_bank,
            users.bank_name, users.bank_account_no,
            users.bank_ifsc_code AS IFSC
        FROM (
            SELECT
                user_id,
                DATEDIFF(NOW(), MAX(date_time)) AS number_of_days_order,
                ROUND(SUM(debit_amount), 2) AS debits,
                ROUND(SUM(credit_amount), 2) AS credits,
                ROUND(SUM(credit_amount) - SUM(debit_amount), 2) AS balance
            FROM (
                SELECT UL_ORD_USER_ID AS user_id, UL_ORD_UPDATE_TIME AS date_time,
                    CASE WHEN UL_ORD_TYPE = 'Buy'  THEN UL_ORD_AMOUNT ELSE 0 END AS debit_amount,
                    CASE WHEN UL_ORD_TYPE = 'Sell' THEN UL_ORD_AMOUNT ELSE 0 END AS credit_amount
                FROM unlisted_orders WHERE UL_ORD_STATUS = 'Completed' {$ordDate}
                UNION ALL
                SELECT pgt_transaction_for_user_id AS user_id, pgt_created_on AS date_time,
                    CASE WHEN pgt_transaction_type = 'Flow Out' THEN pgt_in_out_amount ELSE 0 END AS debit_amount,
                    CASE WHEN pgt_transaction_type = 'Flow In'  THEN pgt_in_out_amount ELSE 0 END AS credit_amount
                FROM pg_transactions WHERE pgt_from_to = 'Customer' {$pgtDate}
                UNION ALL
                SELECT UL_ORD_INTERMEDIARY_USER_ID AS user_id, UL_ORD_UPDATE_TIME AS date_time,
                    0 AS debit_amount, UL_ORD_INTERMEDIARY_COMMISSION AS credit_amount
                FROM unlisted_orders WHERE UL_ORD_STATUS = 'Completed' AND UL_ORD_INTERMEDIARY_USER_ID > 0 {$ordDate}
            ) AS all_tx
            GROUP BY user_id ORDER BY balance DESC
        ) AS masterTbl
        LEFT JOIN unlisted_leads AS ulLeads ON ulLeads.UL_LEAD_UID = masterTbl.user_id
        LEFT JOIN users AS addedByName ON addedByName.uid = ulLeads.UL_LEAD_ALLOCATED_TO
        LEFT JOIN users ON users.uid = masterTbl.user_id
        LEFT JOIN (
            SELECT REQUEST_USER_ID, REQUEST_AMOUNT FROM withdrawal_request
            WHERE REQUEST_TYPE = 'Cash' AND (REQUEST_STATUS IS NULL OR REQUEST_STATUS = '' OR REQUEST_STATUS = 'Pending')
        ) AS wdraw ON masterTbl.user_id = wdraw.REQUEST_USER_ID
        WHERE (balance > 0 OR balance < 0)";

        $rows = DB::select($sql, array_merge($ordP, $pgtP, $ordP));

        $filename = 'user_balance_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $columns = [
            'User ID', 'Name', 'Bank Account Name', 'Bank Name',
            'Bank Account No', 'IFSC', 'Debits', 'Credits', 'Balance',
            'Days Since Last Order', 'RM', 'Pending Request Amount',
        ];

        $callback = function () use ($rows, $columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->user_id,
                    $r->name ?? '',
                    $r->name_in_bank ?? '',
                    $r->bank_name ?? '',
                    $r->bank_account_no ?? '',
                    $r->IFSC ?? '',
                    $r->debits,
                    $r->credits,
                    $r->balance,
                    $r->number_of_days_order,
                    $r->order_added_by_name ?? '',
                    $r->REQUEST_AMOUNT ?? 0,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportOrderReport(Request $request)
    {
        if (!$this->canAccessPgDashboard()) abort(403);

        $fromDate = $request->input('from_date', '');
        $toDate   = $request->input('to_date', '');
        $hasDates = $fromDate && $toDate;

        $ordDate = $hasDates ? 'AND UL_ORD_DATE >= ? AND UL_ORD_DATE <= ?' : '';
        $ordP    = $hasDates ? [$fromDate . ' 00:00:01', $toDate . ' 23:59:59'] : [];

        $rows = DB::select(
            "SELECT * FROM unlisted_orders WHERE UL_ORD_STATUS = 'Completed' {$ordDate}
             ORDER BY UL_ORD_DATE DESC",
            $ordP
        );

        $filename = 'order_report_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $columns = [
            'Order ID', 'User ID', 'Fincode', 'Type', 'Date',
            'Quantity', 'Price Per Share', 'Amount', 'Status', 'Sub Status',
            'Added By', 'LP', 'MLP', 'Intermediary User ID',
            'Intermediary Commission', 'Intermediary Margin', 'Direct Flag',
            'Insert Time', 'Update Time',
        ];

        $callback = function () use ($rows, $columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->UL_ORD_ID,
                    $r->UL_ORD_USER_ID,
                    $r->UL_ORD_FINCODE,
                    $r->UL_ORD_TYPE,
                    $r->UL_ORD_DATE,
                    $r->UL_ORD_QUANTITY,
                    $r->UL_ORD_PRICE_PER_SHARE,
                    $r->UL_ORD_AMOUNT,
                    $r->UL_ORD_STATUS,
                    $r->UL_ORD_SUB_STATUS ?? '',
                    $r->UL_ORD_ADDED_BY,
                    $r->UL_ORD_LP ?? '',
                    $r->UL_ORD_MLP ?? '',
                    $r->UL_ORD_INTERMEDIARY_USER_ID ?? '',
                    $r->UL_ORD_INTERMEDIARY_COMMISSION ?? 0,
                    $r->UL_ORD_INTERMEDIARY_MARGIN ?? 0,
                    $r->UL_ORD_DIRECT_FLAG ?? '',
                    $r->UL_ORD_INSERT_TIME ?? '',
                    $r->UL_ORD_UPDATE_TIME ?? '',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ─── AJAX: TDS / TCS data ─────────────────────────────────────────────────

    public function pgDashboardTdsTcs(Request $request)
    {
        if (!$this->canAccessPgDashboard()) abort(403);

        $taxType     = $request->input('type_of_tax', 'TDS');
        $fyRaw       = $request->input('financial_year', '');
        $filterMonth = (int)$request->input('filter_month', 0);
        $search      = trim($request->input('filter_searchtext', ''));
        $pageNo      = max(0, (int)$request->input('page_no', 0));
        $limit       = 30;

        [$fyFrom, $fyTo] = explode('&', $fyRaw . '&') + ['', ''];

        $where  = 'WHERE UL_ORD_STATUS = ? AND UL_ORD_TYPE = ?';
        $params = ['Completed', $taxType === 'TDS' ? 'Sell' : 'Buy'];

        if ($filterMonth >= 1 && $filterMonth <= 12 && $fyFrom) {
            // Determine correct year for this month within the FY
            $fyStartYear = (int)substr($fyFrom, 0, 4);
            $monthYear   = $filterMonth >= 4 ? $fyStartYear : $fyStartYear + 1;
            $monthFrom   = sprintf('%04d-%02d-01', $monthYear, $filterMonth);
            $monthTo     = date('Y-m-t', strtotime($monthFrom));
            $where   .= ' AND UL_ORD_DATE >= ? AND UL_ORD_DATE <= ?';
            $params[] = $monthFrom . ' 00:00:00';
            $params[] = $monthTo   . ' 23:59:59';
        } elseif ($fyFrom && $fyTo) {
            $where   .= ' AND UL_ORD_DATE >= ? AND UL_ORD_DATE <= ?';
            $params[] = $fyFrom . ' 00:00:00';
            $params[] = $fyTo   . ' 23:59:59';
        }

        $searchWhere = '';
        if ($search !== '') {
            $searchWhere  = ' AND (users.name LIKE ? OR users.uid LIKE ? OR users.phone LIKE ? OR users.email LIKE ?)';
            $searchParam  = '%' . $search . '%';
            $params       = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
        }

        $countSql = "SELECT COUNT(DISTINCT UL_ORD_USER_ID) AS cnt
            FROM unlisted_orders LEFT JOIN users ON users.uid = unlisted_orders.UL_ORD_USER_ID
            {$where} {$searchWhere}";
        $total = DB::select($countSql, $params)[0]->cnt ?? 0;

        $dataSql = "SELECT UL_ORD_USER_ID, users.name AS customer_name,
            SUM(UL_ORD_AMOUNT) AS order_amount
            FROM unlisted_orders LEFT JOIN users ON users.uid = unlisted_orders.UL_ORD_USER_ID
            {$where} {$searchWhere}
            GROUP BY UL_ORD_USER_ID, users.name
            ORDER BY order_amount DESC
            LIMIT {$limit} OFFSET " . ($pageNo * $limit);
        $rows = DB::select($dataSql, $params);

        $pages = $total > 0 ? ceil($total / $limit) : 1;
        $table  = "<div style='font-size:12px;color:#6c757d;margin-bottom:8px;'>{$total} results &mdash; page " . ($pageNo + 1) . " of {$pages}</div>";
        $table .= '<table class="table table-sm table-bordered" style="font-size:12px;">';
        $table .= '<thead><tr style="background:#f8f9fa;">
            <th>UID — Name</th>';
        if ($taxType === 'TDS') {
            $table .= '<th class="text-end">Sell Amt</th><th class="text-end">Taxable Amt (&gt;50L)</th><th class="text-end">TDS @ 0.1%</th>';
        } else {
            $table .= '<th class="text-end">Buy Amt</th><th class="text-end">Taxable Amt (&gt;50L)</th><th class="text-end">TCS @ 0.1%</th>';
        }
        $table .= '</tr></thead><tbody>';

        foreach ($rows as $row) {
            $amt      = $row->order_amount;
            $taxable  = max(0, $amt - 5000000);
            $taxAmt   = round($taxable * 0.001, 2);
            $table   .= '<tr><td>' . htmlspecialchars($row->UL_ORD_USER_ID . ' — ' . ($row->customer_name ?? '—')) . '</td>'
                      . '<td class="text-end">' . number_format($amt, 2) . '</td>'
                      . '<td class="text-end">' . number_format($taxable, 2) . '</td>'
                      . '<td class="text-end">' . number_format($taxAmt, 2) . '</td></tr>';
        }

        $table .= '</tbody></table>';

        $pagination = $this->buildPagination($total, $pageNo, $limit, 'getUserTdsTcsData');

        return response()->json(['table' => $table, 'pagination' => $pagination]);
    }

    // ─── AJAX: Commission data ────────────────────────────────────────────────

    public function pgDashboardCommission(Request $request)
    {
        if (!$this->canAccessPgDashboard()) abort(403);

        $from    = $request->input('from', '');
        $to      = $request->input('to', '');
        $search  = trim($request->input('searchby', ''));
        $pageNo  = max(0, (int)$request->input('page_no', 0));
        $limit   = 30;

        $where  = 'WHERE UL_ORD_STATUS = ? AND UL_ORD_INTERMEDIARY_USER_ID > 0';
        $params = ['Completed'];

        if ($from && $to) {
            $where   .= ' AND UL_ORD_DATE >= ? AND UL_ORD_DATE <= ?';
            $params[] = $from . ' 00:00:00';
            $params[] = $to   . ' 23:59:59';
        }
        if ($search !== '') {
            $where   .= ' AND (users.name LIKE ? OR unlisted_orders.UL_ORD_INTERMEDIARY_USER_ID LIKE ? OR unlisted_orders.UL_ORD_INTERMEDIARY_COMMISSION LIKE ?)';
            $sp       = '%' . $search . '%';
            $params   = array_merge($params, [$sp, $sp, $sp]);
        }

        $countSql = "SELECT COUNT(DISTINCT UL_ORD_INTERMEDIARY_USER_ID) AS cnt
            FROM unlisted_orders LEFT JOIN users ON users.uid = unlisted_orders.UL_ORD_INTERMEDIARY_USER_ID
            {$where}";
        $total = DB::select($countSql, $params)[0]->cnt ?? 0;

        $dataSql = "SELECT UL_ORD_INTERMEDIARY_USER_ID, users.name,
            SUM(UL_ORD_INTERMEDIARY_COMMISSION) AS total_commission
            FROM unlisted_orders LEFT JOIN users ON users.uid = unlisted_orders.UL_ORD_INTERMEDIARY_USER_ID
            {$where}
            GROUP BY UL_ORD_INTERMEDIARY_USER_ID, users.name
            ORDER BY total_commission DESC
            LIMIT {$limit} OFFSET " . ($pageNo * $limit);
        $rows = DB::select($dataSql, $params);

        $pages  = $total > 0 ? ceil($total / $limit) : 1;
        $table  = "<div style='font-size:12px;color:#6c757d;margin-bottom:8px;'>{$total} results &mdash; page " . ($pageNo + 1) . " of {$pages}</div>";
        $table .= '<table class="table table-sm table-bordered" style="font-size:12px;">';
        $table .= '<thead><tr style="background:#f8f9fa;">
            <th>Intermediary User</th>
            <th class="text-end">Commission Earned</th>
        </tr></thead><tbody>';

        foreach ($rows as $row) {
            $table .= '<tr><td>' . htmlspecialchars($row->UL_ORD_INTERMEDIARY_USER_ID . ' — ' . ($row->name ?? '—')) . '</td>'
                    . '<td class="text-end">' . number_format($row->total_commission, 2) . '</td></tr>';
        }

        $table .= '</tbody></table>';
        $pagination = $this->buildPagination($total, $pageNo, $limit, 'getUserCommision');

        return response()->json(['table' => $table, 'pagination' => $pagination]);
    }

    // ─── Search: Users autocomplete ───────────────────────────────────────────

    public function pgSearchUsers(Request $request)
    {
        if (!$this->canAccessPgDashboard()) abort(403);
        $q = trim($request->input('q', ''));
        if (strlen($q) < 1) return response()->json([]);

        $rows = DB::select(
            "SELECT uid, name, phone FROM users
             WHERE (name LIKE ? OR uid LIKE ? OR phone LIKE ?)
             ORDER BY name LIMIT 20",
            ['%'.$q.'%', '%'.$q.'%', '%'.$q.'%']
        );
        return response()->json(array_map(fn($r) => [
            'uid'   => $r->uid,
            'label' => $r->uid . ' — ' . $r->name . ($r->phone ? ' (' . $r->phone . ')' : ''),
        ], $rows));
    }

    // ─── Search: Stocks autocomplete ─────────────────────────────────────────

    public function pgSearchStocks(Request $request)
    {
        if (!$this->canAccessPgDashboard()) abort(403);
        $q = trim($request->input('q', ''));
        if (strlen($q) < 2) return response()->json([]);

        $rows = DB::select(
            "SELECT UL_STOCKS_FINCODE AS fincode, UL_STOCKS_COMPNAME AS name
             FROM unlisted_stocks WHERE UL_STOCKS_COMPNAME LIKE ?
             ORDER BY UL_STOCKS_COMPNAME LIMIT 20",
            ['%'.$q.'%']
        );
        return response()->json(array_map(fn($r) => [
            'fincode' => $r->fincode,
            'label'   => $r->name,
        ], $rows));
    }

    // ─── Add PG Transaction ───────────────────────────────────────────────────

    public function pgAddTransaction(Request $request)
    {
        if (!$this->canAccessPgDashboard()) abort(403);

        $account       = $request->input('pgt_bank_account');
        $flowType      = $request->input('pgt_transaction_type');
        $fromTo        = $request->input('pgt_from_to');
        $amount        = (float)$request->input('pgt_amount', 0);
        $date          = $request->input('pgt_transaction_date');
        $hour          = $request->input('pgt_hour', '00');
        $minute        = $request->input('pgt_minute', '00');
        $refNo         = trim($request->input('pgt_ref_no', ''));
        $remarks       = trim($request->input('pgt_remarks', ''));
        $userId        = (int)$request->input('pgt_transaction_for_user_id', 0);
        $directFlag    = $request->input('pgt_direct_flag', 0) ? 1 : 0;
        $commissionFlag= $request->input('pgt_commission_flag', 0) ? 1 : 0;
        $tdsFlag       = $request->input('pgt_TDS_flag', 0) ? 1 : 0;

        $allowedAccounts = ['ICICI Bank', 'Bandhan Bank'];
        $allowedFlows    = ['Flow In', 'Flow Out'];
        $allowedFromTo   = ['Customer', 'ICICI Bank', 'Bandhan Bank', 'Company'];

        if (!in_array($account, $allowedAccounts))
            return response()->json(['success' => false, 'message' => 'Invalid account.']);
        if (!in_array($flowType, $allowedFlows))
            return response()->json(['success' => false, 'message' => 'Invalid flow type.']);
        if (!in_array($fromTo, $allowedFromTo))
            return response()->json(['success' => false, 'message' => 'Invalid From/To.']);
        if ($amount <= 0)
            return response()->json(['success' => false, 'message' => 'Amount must be > 0.']);
        if (!$date)
            return response()->json(['success' => false, 'message' => 'Date is required.']);
        if (!$refNo)
            return response()->json(['success' => false, 'message' => 'Ref No. is required.']);

        $datetime = $date . ' ' . $hour . ':' . $minute . ':00';

        // Compute running balance for this account
        $lastBal = DB::select(
            "SELECT pgt_balance FROM pg_transactions
             WHERE pgt_bank_account = ? ORDER BY pgt_created_on DESC, pgt_tid DESC LIMIT 1",
            [$account]
        );
        $prevBal  = $lastBal ? (float)$lastBal[0]->pgt_balance : 0;
        $newBal   = $flowType === 'Flow In' ? $prevBal + $amount : $prevBal - $amount;

        DB::table('pg_transactions')->insert([
            'pgt_transaction_date'          => $date,
            'pgt_in_out_amount'             => $amount,
            'pgt_transaction_type'          => $flowType,
            'pgt_from_to'                   => $fromTo,
            'pgt_bank_account'              => $account,
            'pgt_balance'                   => round($newBal, 2),
            'pgt_transaction_for_user_id'   => $fromTo === 'Customer' ? $userId : 0,
            'pgt_ref_no'                    => $refNo,
            'pgt_remarks'                   => $remarks,
            'pgt_direct_flag'               => $directFlag,
            'pgt_commission_flag'           => $commissionFlag,
            'pgt_TDS_flag'                  => $tdsFlag,
            'pgt_created_on'                => $datetime,
        ]);

        return response()->json(['success' => true, 'message' => 'Transaction saved. New balance: ' . number_format($newBal, 2)]);
    }

    // ─── Map Transaction to User ──────────────────────────────────────────────

    public function pgMapTransaction(Request $request)
    {
        if (!$this->canAccessPgDashboard()) abort(403);

        $tid    = (int)$request->input('pgt_tid', 0);
        $userId = (int)$request->input('map_user_id', 0);

        if ($tid <= 0)
            return response()->json(['success' => false, 'message' => 'Invalid transaction ID.']);
        if ($userId <= 0)
            return response()->json(['success' => false, 'message' => 'Please select a user.']);

        $user = DB::select("SELECT uid, name FROM users WHERE uid = ? LIMIT 1", [$userId]);
        if (empty($user))
            return response()->json(['success' => false, 'message' => 'User not found.']);

        $txn = DB::select(
            "SELECT pgt_tid, pgt_transaction_type, pgt_transaction_for_user_id FROM pg_transactions WHERE pgt_tid = ? LIMIT 1",
            [$tid]
        );
        if (empty($txn))
            return response()->json(['success' => false, 'message' => 'Transaction not found.']);

        DB::table('pg_transactions')
            ->where('pgt_tid', $tid)
            ->update(['pgt_transaction_for_user_id' => $userId]);

        return response()->json([
            'success' => true,
            'message' => 'Transaction #' . $tid . ' mapped to ' . $user[0]->name . ' (UID ' . $userId . ').',
        ]);
    }

    // ─── Add Demat Transaction ────────────────────────────────────────────────

    public function pgAddDematTransaction(Request $request)
    {
        if (!$this->canAccessPgDashboard()) abort(403);

        $flag    = $request->input('DEMAT_IN_OUT_FLAG');
        $userId  = (int)$request->input('DEMAT_USER_ID', 0);
        $fincode = (int)$request->input('DEMAT_FINCODE', 0);
        $qty     = (float)$request->input('DEMAT_QTY', 0);
        $date    = $request->input('DEMAT_DATE');
        $hour    = $request->input('demat_hour', '19');
        $minute  = $request->input('demat_minute', '01');

        if (!in_array($flag, ['Flow In', 'Flow Out']))
            return response()->json(['success' => false, 'message' => 'Invalid flag.']);
        if ($userId <= 0)
            return response()->json(['success' => false, 'message' => 'Customer is required.']);
        if ($fincode <= 0)
            return response()->json(['success' => false, 'message' => 'Company is required.']);
        if ($qty <= 0)
            return response()->json(['success' => false, 'message' => 'Quantity must be > 0.']);
        if (!$date)
            return response()->json(['success' => false, 'message' => 'Date is required.']);

        DB::table('demat_transactions')->insert([
            'DEMAT_USER_ID'    => $userId,
            'DEMAT_FINCODE'    => $fincode,
            'DEMAT_DATE'       => $date . ' ' . $hour . ':' . $minute . ':00',
            'DEMAT_IN_OUT_FLAG'=> $flag,
            'DEMAT_QTY'        => $qty,
        ]);

        return response()->json(['success' => true, 'message' => 'Demat transaction saved successfully.']);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function buildPagination(int $total, int $pageNo, int $limit, string $jsFn): string
    {
        if ($total <= $limit) return '';
        $pages = ceil($total / $limit);
        $html  = '<nav><ul class="pagination pagination-sm justify-content-center mb-0">';
        for ($i = 0; $i < $pages; $i++) {
            $active = $i === $pageNo ? ' active' : '';
            $html  .= "<li class=\"page-item{$active}\"><button class=\"page-link\" onclick=\"{$jsFn}({$i})\">" . ($i + 1) . "</button></li>";
        }
        $html .= '</ul></nav>';
        return $html;
    }

    private function canAccessMargin(): bool
    {
        $pg = session('privilege.pg', []);
        return !empty(session('privilege.admin')) || !empty($pg['margin']);
    }

    private function canAccessMarginError(): bool
    {
        $pg = session('privilege.pg', []);
        return !empty(session('privilege.admin')) || !empty($pg['margin_error']);
    }

    private function canAccessPgDashboard(): bool
    {
        $pg = session('privilege.pg', []);
        return !empty(session('privilege.admin')) || !empty($pg['dashboard']);
    }

    // ─── Request Dashboard ────────────────────────────────────────────────────

    public function requestDashboard()
    {
        if (!$this->canAccessPgDashboard()) abort(403);
        return view('admin.pg.request-dashboard');
    }

    public function requestDashboardData(Request $request)
    {
        if (!$this->canAccessPgDashboard()) abort(403);

        $search   = trim($request->input('searchtext', ''));
        $type     = $request->input('REQUEST_TYPE', '');
        $status   = $request->input('REQUEST_STATUS', '');
        $fromDate = $request->input('from_date', '');
        $toDate   = $request->input('to_date', '');
        $pageNo   = max(0, (int) $request->input('page_no', 0));
        $limit    = 30;

        $q = DB::table('withdrawal_request')
            ->leftJoin('users', 'users.uid', '=', 'withdrawal_request.REQUEST_USER_ID')
            ->leftJoin('unlisted_stocks', 'unlisted_stocks.UL_STOCKS_FINCODE', '=', 'withdrawal_request.REQUEST_FINCODE')
            ->leftJoin('users as updater', 'updater.uid', '=', 'withdrawal_request.REQUEST_UPDATED_BY_ID')
            ->select([
                'withdrawal_request.*',
                'users.name',
                'users.phone',
                'unlisted_stocks.UL_STOCKS_S_NAME',
                DB::raw('updater.name AS updated_by_name'),
            ]);

        if ($search !== '' && $search !== null) {
            $q->where(function ($sub) use ($search) {
                $sub->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.phone', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%");
                if (is_numeric($search)) {
                    $sub->orWhere('withdrawal_request.REQUEST_USER_ID', (int) $search);
                }
            });
        }

        if ($request->filled('REQUEST_TYPE')) {
            $q->where('withdrawal_request.REQUEST_TYPE', $type);
        }

        if ($status === 'Pending') {
            $q->where(function ($sub) {
                $sub->whereNull('withdrawal_request.REQUEST_STATUS')
                    ->orWhere('withdrawal_request.REQUEST_STATUS', '')
                    ->orWhere('withdrawal_request.REQUEST_STATUS', 'Pending');
            });
        } elseif ($status !== '' && $status !== null) {
            $q->where('withdrawal_request.REQUEST_STATUS', $status);
        }

        if ($fromDate && $toDate) {
            $q->whereBetween('withdrawal_request.REQUEST_DATE', [$fromDate, $toDate]);
        }

        $total = (clone $q)->count();
        $rows  = $q->orderByDesc('withdrawal_request.REQUEST_ID')
                   ->limit($limit)
                   ->offset($pageNo * $limit)
                   ->get();

        return response()->json([
            'rows'         => $rows,
            'total'        => $total,
            'pages'        => (int) ceil($total / $limit),
            'current_page' => $pageNo,
            'limit'        => $limit,
        ]);
    }

    public function getWithdrawalRequest(int $requestId)
    {
        if (!$this->canAccessPgDashboard()) abort(403);

        $row = DB::table('withdrawal_request')->where('REQUEST_ID', $requestId)->first();
        if (!$row) return response()->json(['success' => false, 'message' => 'Not found'], 404);

        return response()->json(['success' => true, 'data' => $row]);
    }

    public function updateWithdrawalRequest(Request $request, int $requestId)
    {
        if (!$this->canAccessPgDashboard()) abort(403);

        $status   = $request->input('REQUEST_STATUS', '');
        $comments = trim($request->input('REQUEST_STATUS_COMMENTS', ''));

        if (!in_array($status, ['Pending', 'Completed', 'Cancelled'])) {
            return response()->json(['success' => false, 'message' => 'Invalid status.']);
        }

        DB::table('withdrawal_request')
            ->where('REQUEST_ID', $requestId)
            ->update([
                'REQUEST_STATUS'          => $status,
                'REQUEST_STATUS_COMMENTS' => $comments,
                'REQUEST_UPDATED_DATE'    => now(),
                'REQUEST_UPDATED_BY_ID'   => session('uid'),
            ]);

        return response()->json(['success' => true, 'message' => 'Request updated successfully.']);
    }
}
