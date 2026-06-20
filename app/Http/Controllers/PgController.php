<?php

namespace App\Http\Controllers;

use App\Helpers\Privilege;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    private function canAccessMargin(): bool
    {
        return !empty(Privilege::get('admin')) || !empty(Privilege::get('pg.margin'));
    }

    private function canAccessMarginError(): bool
    {
        return !empty(Privilege::get('admin')) || !empty(Privilege::get('pg.margin_error'));
    }
}
