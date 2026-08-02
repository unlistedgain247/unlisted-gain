<?php

namespace App\Http\Controllers;

use App\Helpers\Privilege;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UnlistedReportController extends Controller
{
    private function canAccess(): bool
    {
        // Privilege::get() re-reads the DB each request (unlike a cached
        // session value), so a revoked grant takes effect immediately instead
        // of staying valid until the session naturally expires.
        return !empty(Privilege::get('admin')) || !empty(Privilege::get('unlisted.unlisted_reports'));
    }

    public function index()
    {
        if (!$this->canAccess()) abort(403);
        return view('admin.unlisted.reports');
    }

    // Renders one "Month/Employee-wise Performance" table. $rows are plain
    // arrays of [label, customers, orders, amount] already picked out by the
    // caller for whichever side (Buy/Sell) is being displayed.
    private function renderPerformanceTable(string $title, string $labelHeader, array $rows): string
    {
        $html = '<div class="col-md-6"><p class="rpt-sub-label">' . $title . '</p>';
        $html .= '<table class="table table-sm table-striped table-bordered font-13-data-table"><thead><tr>
            <th>' . $labelHeader . '</th><th>Customers</th><th>Orders</th><th>Amount</th><th>Avg/Cust</th><th>Avg/Order</th>
            </tr></thead><tbody>';

        if (empty($rows)) {
            $html .= '<tr><td colspan="6" class="text-center text-muted">No data</td></tr>';
        } else {
            foreach ($rows as [$label, $customers, $orders, $amount]) {
                $avgPerCust  = $customers > 0 ? round($amount / $customers) : 0;
                $avgPerOrder = $orders > 0 ? round($amount / $orders) : 0;
                $html .= '<tr>
                    <td>' . $label . '</td>
                    <td>' . $customers . '</td>
                    <td>' . $orders . '</td>
                    <td>₹' . number_format($amount, 0) . '</td>
                    <td>₹' . number_format($avgPerCust, 0) . '</td>
                    <td>₹' . number_format($avgPerOrder, 0) . '</td>
                </tr>';
            }
        }

        return $html . '</tbody></table></div>';
    }

    // Orders Report — month-wise & employee-wise performance (Buy / Sell)
    public function ordersReport(Request $request)
    {
        if (!$this->canAccess()) abort(403);

        $fromDate = $request->input('from_date', '');
        $toDate   = $request->input('to_date', '');

        $dateWhere    = '';
        $dateBindings = [];

        if ($fromDate && $toDate) {
            $dateWhere    = " AND UL_ORD_DATE >= ? AND UL_ORD_DATE <= ?";
            $dateBindings = [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'];
        }

        // Buy and Sell used to be two near-identical queries (the Sell one built
        // by str_replace()-ing 'Buy' into 'Sell' inside the Buy query's SQL
        // string) — fragile, and doubles the DB round-trips for no reason.
        // One conditionally-aggregated query gives both sides at once.
        $monthRows = DB::select("SELECT MONTH(UL_ORD_DATE) AS mnth, YEAR(UL_ORD_DATE) AS yr,
            COUNT(DISTINCT CASE WHEN UL_ORD_TYPE = 'Buy'  THEN UL_ORD_USER_ID END) AS buy_customers,
            SUM(CASE WHEN UL_ORD_TYPE = 'Buy'  THEN 1 ELSE 0 END)               AS buy_orders,
            SUM(CASE WHEN UL_ORD_TYPE = 'Buy'  THEN UL_ORD_AMOUNT ELSE 0 END)   AS buy_amount,
            COUNT(DISTINCT CASE WHEN UL_ORD_TYPE = 'Sell' THEN UL_ORD_USER_ID END) AS sell_customers,
            SUM(CASE WHEN UL_ORD_TYPE = 'Sell' THEN 1 ELSE 0 END)               AS sell_orders,
            SUM(CASE WHEN UL_ORD_TYPE = 'Sell' THEN UL_ORD_AMOUNT ELSE 0 END)   AS sell_amount
            FROM unlisted_orders
            WHERE UL_ORD_STATUS = 'Completed' $dateWhere
            GROUP BY YEAR(UL_ORD_DATE), MONTH(UL_ORD_DATE) ORDER BY yr, mnth", $dateBindings);

        $empRows = DB::select("SELECT u.name AS emp_name, o.UL_ORD_ADDED_BY,
            COUNT(DISTINCT CASE WHEN o.UL_ORD_TYPE = 'Buy'  THEN o.UL_ORD_USER_ID END) AS buy_customers,
            SUM(CASE WHEN o.UL_ORD_TYPE = 'Buy'  THEN 1 ELSE 0 END)               AS buy_orders,
            SUM(CASE WHEN o.UL_ORD_TYPE = 'Buy'  THEN o.UL_ORD_AMOUNT ELSE 0 END) AS buy_amount,
            COUNT(DISTINCT CASE WHEN o.UL_ORD_TYPE = 'Sell' THEN o.UL_ORD_USER_ID END) AS sell_customers,
            SUM(CASE WHEN o.UL_ORD_TYPE = 'Sell' THEN 1 ELSE 0 END)               AS sell_orders,
            SUM(CASE WHEN o.UL_ORD_TYPE = 'Sell' THEN o.UL_ORD_AMOUNT ELSE 0 END) AS sell_amount
            FROM unlisted_orders AS o
            LEFT JOIN users AS u ON u.uid = o.UL_ORD_ADDED_BY
            WHERE o.UL_ORD_STATUS = 'Completed' $dateWhere
            GROUP BY o.UL_ORD_ADDED_BY, u.name", $dateBindings);

        $monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $monthLabel = fn ($r) => ($monthNames[$r->mnth] ?? $r->mnth) . ' ' . $r->yr;
        $empLabel   = fn ($r) => e($r->emp_name ?: 'UID:' . $r->UL_ORD_ADDED_BY);

        // The old separate Buy/Sell queries filtered by type *before* grouping,
        // so a month/employee with zero orders on one side simply never
        // produced a row there. Grouping both sides in one query means every
        // month/employee with completed orders shows up regardless — filter
        // out the zero-order side per table to match the original output.
        $monthBuyRows  = array_values(array_filter($monthRows, fn ($r) => $r->buy_orders > 0));
        $monthSellRows = array_values(array_filter($monthRows, fn ($r) => $r->sell_orders > 0));
        $empBuyRows  = collect($empRows)->filter(fn ($r) => $r->buy_orders > 0)->sortByDesc('buy_amount')->values();
        $empSellRows = collect($empRows)->filter(fn ($r) => $r->sell_orders > 0)->sortByDesc('sell_amount')->values();

        $table = '<div class="row">'
            . $this->renderPerformanceTable('Month-wise Performance (Buy)', 'Month',
                array_map(fn ($r) => [$monthLabel($r), $r->buy_customers, $r->buy_orders, $r->buy_amount], $monthBuyRows))
            . $this->renderPerformanceTable('Month-wise Performance (Sell)', 'Month',
                array_map(fn ($r) => [$monthLabel($r), $r->sell_customers, $r->sell_orders, $r->sell_amount], $monthSellRows))
            . '</div><div class="row">'
            . $this->renderPerformanceTable('Employee-wise Performance (Buy)', 'Employee',
                $empBuyRows->map(fn ($r) => [$empLabel($r), $r->buy_customers, $r->buy_orders, $r->buy_amount])->all())
            . $this->renderPerformanceTable('Employee-wise Performance (Sell)', 'Employee',
                $empSellRows->map(fn ($r) => [$empLabel($r), $r->sell_customers, $r->sell_orders, $r->sell_amount])->all())
            . '</div>';

        return response()->json(['table' => $table]);
    }

    // Customer Orders — buy/sell summary per customer (Completed only)
    public function customerOrders(Request $request)
    {
        if (!$this->canAccess()) abort(403);

        try {
            $limit    = 50;
            $page     = max(0, (int) $request->input('page', 0));
            $offset   = $page * $limit;
            $search   = $request->input('searchtext', '');
            $fromDate = $request->input('from_date', '');
            $toDate   = $request->input('to_date', '');

            $where    = "WHERE o.UL_ORD_STATUS = 'Completed'";
            $bindings = [];

            if ($fromDate) {
                $where    .= " AND o.UL_ORD_DATE >= ?";
                $bindings[] = $fromDate . ' 00:00:00';
            }
            if ($toDate) {
                $where    .= " AND o.UL_ORD_DATE <= ?";
                $bindings[] = $toDate . ' 23:59:59';
            }
            if ($search) {
                $where    .= " AND (u.name LIKE ? OR u.phone_number LIKE ? OR u.user_email LIKE ? OR CAST(u.uid AS CHAR) LIKE ?)";
                $like      = '%' . $search . '%';
                $bindings  = array_merge($bindings, [$like, $like, $like, $like]);
            }

            $baseSql = "SELECT o.UL_ORD_USER_ID,
                u.name AS customer_name,
                SUM(CASE WHEN o.UL_ORD_TYPE = 'Buy'  THEN 1 ELSE 0 END) AS no_buy,
                SUM(CASE WHEN o.UL_ORD_TYPE = 'Buy'  THEN o.UL_ORD_AMOUNT ELSE 0 END) AS amt_buy,
                SUM(CASE WHEN o.UL_ORD_TYPE = 'Sell' THEN 1 ELSE 0 END) AS no_sell,
                SUM(CASE WHEN o.UL_ORD_TYPE = 'Sell' THEN o.UL_ORD_AMOUNT ELSE 0 END) AS amt_sell,
                al.name AS allocated_name
                FROM unlisted_orders AS o
                LEFT JOIN users AS u  ON u.uid = o.UL_ORD_USER_ID
                LEFT JOIN unlisted_leads AS lt ON lt.UL_LEAD_UID = o.UL_ORD_USER_ID
                LEFT JOIN users AS al ON al.uid = lt.UL_LEAD_ALLOCATED_TO
                $where
                GROUP BY o.UL_ORD_USER_ID, u.name, al.name";

            // Count via a wrapped query instead of materializing every grouped
            // row into PHP just to call count() on the array — avoids pulling
            // the full result set (and every SUM/CASE column) over the wire
            // twice on every page/pagination click.
            $total = DB::selectOne("SELECT COUNT(*) AS cnt FROM ($baseSql) AS agg", $bindings)->cnt;
            $rows  = DB::select($baseSql . " ORDER BY amt_buy DESC LIMIT $limit OFFSET $offset", $bindings);

            $table = '<table class="table table-sm table-striped table-bordered font-13-data-table w-100">
                <thead><tr>
                    <th>Customer</th><th>UID</th><th>Allocated To</th>
                    <th># Buy</th><th>Buy Amount</th><th># Sell</th><th>Sell Amount</th>
                </tr></thead><tbody>';

            if (empty($rows)) {
                $table .= '<tr><td colspan="7" class="text-center text-muted py-3">No records found</td></tr>';
            } else {
                foreach ($rows as $r) {
                    $table .= '<tr>
                        <td>' . e($r->customer_name ?: '—') . '</td>
                        <td>' . $r->UL_ORD_USER_ID . '</td>
                        <td>' . e($r->allocated_name ?: '—') . '</td>
                        <td>' . $r->no_buy . '</td>
                        <td>₹' . number_format($r->amt_buy, 0) . '</td>
                        <td>' . $r->no_sell . '</td>
                        <td>₹' . number_format($r->amt_sell, 0) . '</td>
                    </tr>';
                }
            }

            $table .= '</tbody></table>';

            return response()->json([
                'table' => $table,
                'total' => $total,
                'page'  => $page,
                'limit' => $limit,
            ]);
        } catch (\Throwable $e) {
            Log::error('UnlistedReport customerOrders error', ['msg' => $e->getMessage(), 'sql' => $e->getTraceAsString()]);
            return response()->json(['table' => '<p class="text-danger p-3">Error: ' . e($e->getMessage()) . '</p>', 'total' => 0, 'page' => 0, 'limit' => 50], 500);
        }
    }

    // Company Orders — buy/sell summary per company (Completed only)
    public function companyOrders(Request $request)
    {
        if (!$this->canAccess()) abort(403);

        try {
            $limit    = 50;
            $page     = max(0, (int) $request->input('page', 0));
            $offset   = $page * $limit;
            $search   = $request->input('searchtext', '');
            $fromDate = $request->input('from_date', '');
            $toDate   = $request->input('to_date', '');

            $where    = "WHERE o.UL_ORD_STATUS = 'Completed'";
            $bindings = [];

            if ($fromDate) {
                $where    .= " AND o.UL_ORD_DATE >= ?";
                $bindings[] = $fromDate . ' 00:00:00';
            }
            if ($toDate) {
                $where    .= " AND o.UL_ORD_DATE <= ?";
                $bindings[] = $toDate . ' 23:59:59';
            }
            if ($search) {
                $where    .= " AND (us.UL_STOCKS_S_NAME LIKE ? OR us.UL_STOCKS_COMPNAME LIKE ? OR CAST(o.UL_ORD_FINCODE AS CHAR) LIKE ?)";
                $like      = '%' . $search . '%';
                $bindings  = array_merge($bindings, [$like, $like, $like]);
            }

            $baseSql = "SELECT o.UL_ORD_FINCODE,
                us.UL_STOCKS_S_NAME AS company_name,
                SUM(CASE WHEN o.UL_ORD_TYPE = 'Buy'  THEN 1 ELSE 0 END) AS no_buy,
                SUM(CASE WHEN o.UL_ORD_TYPE = 'Buy'  THEN o.UL_ORD_AMOUNT ELSE 0 END) AS amt_buy,
                SUM(CASE WHEN o.UL_ORD_TYPE = 'Sell' THEN 1 ELSE 0 END) AS no_sell,
                SUM(CASE WHEN o.UL_ORD_TYPE = 'Sell' THEN o.UL_ORD_AMOUNT ELSE 0 END) AS amt_sell
                FROM unlisted_orders AS o
                LEFT JOIN unlisted_stocks AS us ON us.UL_STOCKS_FINCODE = o.UL_ORD_FINCODE
                $where
                GROUP BY o.UL_ORD_FINCODE, us.UL_STOCKS_S_NAME";

            Log::info('UnlistedReport companyOrders SQL', ['sql' => $baseSql, 'bindings' => $bindings]);

            $total = DB::selectOne("SELECT COUNT(*) AS cnt FROM ($baseSql) AS agg", $bindings)->cnt;
            $rows  = DB::select($baseSql . " ORDER BY amt_buy DESC LIMIT $limit OFFSET $offset", $bindings);

            $table = '<table class="table table-sm table-striped table-bordered font-13-data-table w-100">
                <thead><tr>
                    <th>Company</th><th>Fincode</th>
                    <th># Buy</th><th>Buy Amount</th><th># Sell</th><th>Sell Amount</th>
                </tr></thead><tbody>';

            if (empty($rows)) {
                $table .= '<tr><td colspan="6" class="text-center text-muted py-3">No records found</td></tr>';
            } else {
                foreach ($rows as $r) {
                    $table .= '<tr>
                        <td>' . e($r->company_name ?: '—') . '</td>
                        <td>' . $r->UL_ORD_FINCODE . '</td>
                        <td>' . $r->no_buy . '</td>
                        <td>₹' . number_format($r->amt_buy, 0) . '</td>
                        <td>' . $r->no_sell . '</td>
                        <td>₹' . number_format($r->amt_sell, 0) . '</td>
                    </tr>';
                }
            }

            $table .= '</tbody></table>';

            return response()->json([
                'table' => $table,
                'total' => $total,
                'page'  => $page,
                'limit' => $limit,
            ]);
        } catch (\Throwable $e) {
            Log::error('UnlistedReport companyOrders error', ['msg' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['table' => '<p class="text-danger p-3">Error: ' . e($e->getMessage()) . '</p>', 'total' => 0, 'page' => 0, 'limit' => 50], 500);
        }
    }

    // Combined Order & Financial Report — per company with P&L, B/S, CF availability matrix
    public function combinedFinancial(Request $request)
    {
        if (!$this->canAccess()) abort(403);

        $limit  = 50;
        $page   = max(0, (int) $request->input('page', 0));
        $offset = $page * $limit;
        $search = $request->input('searchtext', '');

        // Compute FY period anchors
        $curMonth = (int) date('n');
        $curYear  = (int) date('Y');

        $fyEndYear  = ($curMonth <= 3) ? $curYear - 1 : $curYear;
        $y1 = ($fyEndYear * 100) + 3;
        $y2 = (($fyEndYear - 1) * 100) + 3;
        $y3 = (($fyEndYear - 2) * 100) + 3;
        $y4 = (($fyEndYear - 3) * 100) + 3;
        $y5 = (($fyEndYear - 4) * 100) + 3;

        // Quarterly anchors
        if ($curMonth <= 3)       { $anchorM = 12; $anchorY = $curYear - 1; }
        elseif ($curMonth <= 6)   { $anchorM = 3;  $anchorY = $curYear; }
        elseif ($curMonth <= 9)   { $anchorM = 6;  $anchorY = $curYear; }
        else                      { $anchorM = 9;  $anchorY = $curYear; }

        $anchor = \DateTime::createFromFormat('Y-n-d', "$anchorY-$anchorM-01");
        $q1 = (int) $anchor->format('Ym');
        $q2 = (int) (clone $anchor)->modify('-3 months')->format('Ym');
        $q3 = (int) (clone $anchor)->modify('-6 months')->format('Ym');
        $q4 = (int) (clone $anchor)->modify('-9 months')->format('Ym');

        $searchCond = '';
        $bindings   = [$y1, $y2, $y3, $y4, $y5, $q1, $q2, $q3, $q4];

        if ($search) {
            $searchCond = " AND (us.UL_STOCKS_S_NAME LIKE ? OR us.UL_STOCKS_COMPNAME LIKE ? OR CAST(o.UL_ORD_FINCODE AS CHAR) LIKE ?)";
            $like       = '%' . $search . '%';
            $bindings   = array_merge($bindings, [$like, $like, $like]);
        }

        $sql = "SELECT us.UL_STOCKS_S_NAME AS company_name,
            o.UL_ORD_FINCODE,
            us.UL_STOCKS_Qtr_Data_Publish,
            o.latest_order_date,

            f.pnl_y1, f.pnl_y2, f.pnl_y3, f.pnl_y4, f.pnl_y5,
            f.pnl_q1, f.pnl_q2, f.pnl_q3, f.pnl_q4,
            f.bs_y1,  f.bs_y2,  f.bs_y3,  f.bs_y4,  f.bs_y5,
            f.cf_y1,  f.cf_y2,  f.cf_y3,  f.cf_y4,  f.cf_y5

            FROM (
                SELECT UL_ORD_FINCODE, MAX(UL_ORD_DATE) AS latest_order_date
                FROM unlisted_orders WHERE UL_ORD_STATUS = 'Completed'
                GROUP BY UL_ORD_FINCODE
            ) o
            LEFT JOIN unlisted_stocks AS us ON us.UL_STOCKS_FINCODE = o.UL_ORD_FINCODE
            LEFT JOIN (
                SELECT UL_FIN_FINCODE,
                    MAX(CASE WHEN UL_FIN_TOTAL_EXPENDITURE IS NOT NULL AND UL_FIN_TOTAL_EXPENDITURE != 0 AND UL_FIN_No_months = 12 AND UL_FIN_Period_end = ? THEN 1 ELSE 0 END) AS pnl_y1,
                    MAX(CASE WHEN UL_FIN_TOTAL_EXPENDITURE IS NOT NULL AND UL_FIN_TOTAL_EXPENDITURE != 0 AND UL_FIN_No_months = 12 AND UL_FIN_Period_end = ? THEN 1 ELSE 0 END) AS pnl_y2,
                    MAX(CASE WHEN UL_FIN_TOTAL_EXPENDITURE IS NOT NULL AND UL_FIN_TOTAL_EXPENDITURE != 0 AND UL_FIN_No_months = 12 AND UL_FIN_Period_end = ? THEN 1 ELSE 0 END) AS pnl_y3,
                    MAX(CASE WHEN UL_FIN_TOTAL_EXPENDITURE IS NOT NULL AND UL_FIN_TOTAL_EXPENDITURE != 0 AND UL_FIN_No_months = 12 AND UL_FIN_Period_end = ? THEN 1 ELSE 0 END) AS pnl_y4,
                    MAX(CASE WHEN UL_FIN_TOTAL_EXPENDITURE IS NOT NULL AND UL_FIN_TOTAL_EXPENDITURE != 0 AND UL_FIN_No_months = 12 AND UL_FIN_Period_end = ? THEN 1 ELSE 0 END) AS pnl_y5,
                    MAX(CASE WHEN UL_FIN_TOTAL_EXPENDITURE IS NOT NULL AND UL_FIN_TOTAL_EXPENDITURE != 0 AND UL_FIN_No_months = 3  AND UL_FIN_Period_end = ? THEN 1 ELSE 0 END) AS pnl_q1,
                    MAX(CASE WHEN UL_FIN_TOTAL_EXPENDITURE IS NOT NULL AND UL_FIN_TOTAL_EXPENDITURE != 0 AND UL_FIN_No_months = 3  AND UL_FIN_Period_end = ? THEN 1 ELSE 0 END) AS pnl_q2,
                    MAX(CASE WHEN UL_FIN_TOTAL_EXPENDITURE IS NOT NULL AND UL_FIN_TOTAL_EXPENDITURE != 0 AND UL_FIN_No_months = 3  AND UL_FIN_Period_end = ? THEN 1 ELSE 0 END) AS pnl_q3,
                    MAX(CASE WHEN UL_FIN_TOTAL_EXPENDITURE IS NOT NULL AND UL_FIN_TOTAL_EXPENDITURE != 0 AND UL_FIN_No_months = 3  AND UL_FIN_Period_end = ? THEN 1 ELSE 0 END) AS pnl_q4,
                    MAX(CASE WHEN UL_FIN_SHAREHOLDER_FUNDS IS NOT NULL AND UL_FIN_SHAREHOLDER_FUNDS != 0 AND UL_FIN_No_months = 12 AND UL_FIN_Period_end = ? THEN 1 ELSE 0 END) AS bs_y1,
                    MAX(CASE WHEN UL_FIN_SHAREHOLDER_FUNDS IS NOT NULL AND UL_FIN_SHAREHOLDER_FUNDS != 0 AND UL_FIN_No_months = 12 AND UL_FIN_Period_end = ? THEN 1 ELSE 0 END) AS bs_y2,
                    MAX(CASE WHEN UL_FIN_SHAREHOLDER_FUNDS IS NOT NULL AND UL_FIN_SHAREHOLDER_FUNDS != 0 AND UL_FIN_No_months = 12 AND UL_FIN_Period_end = ? THEN 1 ELSE 0 END) AS bs_y3,
                    MAX(CASE WHEN UL_FIN_SHAREHOLDER_FUNDS IS NOT NULL AND UL_FIN_SHAREHOLDER_FUNDS != 0 AND UL_FIN_No_months = 12 AND UL_FIN_Period_end = ? THEN 1 ELSE 0 END) AS bs_y4,
                    MAX(CASE WHEN UL_FIN_SHAREHOLDER_FUNDS IS NOT NULL AND UL_FIN_SHAREHOLDER_FUNDS != 0 AND UL_FIN_No_months = 12 AND UL_FIN_Period_end = ? THEN 1 ELSE 0 END) AS bs_y5,
                    MAX(CASE WHEN UL_FIN_CFO IS NOT NULL AND UL_FIN_CFO != 0 AND UL_FIN_No_months = 12 AND UL_FIN_Period_end = ? THEN 1 ELSE 0 END) AS cf_y1,
                    MAX(CASE WHEN UL_FIN_CFO IS NOT NULL AND UL_FIN_CFO != 0 AND UL_FIN_No_months = 12 AND UL_FIN_Period_end = ? THEN 1 ELSE 0 END) AS cf_y2,
                    MAX(CASE WHEN UL_FIN_CFO IS NOT NULL AND UL_FIN_CFO != 0 AND UL_FIN_No_months = 12 AND UL_FIN_Period_end = ? THEN 1 ELSE 0 END) AS cf_y3,
                    MAX(CASE WHEN UL_FIN_CFO IS NOT NULL AND UL_FIN_CFO != 0 AND UL_FIN_No_months = 12 AND UL_FIN_Period_end = ? THEN 1 ELSE 0 END) AS cf_y4,
                    MAX(CASE WHEN UL_FIN_CFO IS NOT NULL AND UL_FIN_CFO != 0 AND UL_FIN_No_months = 12 AND UL_FIN_Period_end = ? THEN 1 ELSE 0 END) AS cf_y5
                FROM unlisted_financials
                GROUP BY UL_FIN_FINCODE
            ) f ON f.UL_FIN_FINCODE = o.UL_ORD_FINCODE
            WHERE 1=1 $searchCond";

        // Bind for financials sub-query: 5 PnL yearly + 4 quarterly + 5 BS + 5 CF = 19 bindings
        $finBindings = [$y1,$y2,$y3,$y4,$y5, $q1,$q2,$q3,$q4, $y1,$y2,$y3,$y4,$y5, $y1,$y2,$y3,$y4,$y5];
        $allBindings = array_merge($finBindings, $search ? ['%'.$search.'%','%'.$search.'%','%'.$search.'%'] : []);

        $rows  = DB::select($sql . " LIMIT $limit OFFSET $offset", $allBindings);
        $total = DB::selectOne("SELECT COUNT(*) AS cnt FROM ($sql) AS agg", $allBindings)->cnt;

        // Sort: recent orders (within 6 months) first
        $cutoff = date('Y-m-d', strtotime('-6 months'));
        $recent = array_filter($rows, fn($r) => !empty($r->latest_order_date) && $r->latest_order_date >= $cutoff);
        $old    = array_filter($rows, fn($r) => empty($r->latest_order_date) || $r->latest_order_date < $cutoff);
        $rows   = array_merge(array_values($recent), array_values($old));

        $tick = '<i class="bx bx-check" style="color:#28a745;font-size:16px;"></i>';
        $cross = '<i class="bx bx-x" style="color:#dc3545;font-size:16px;"></i>';
        $na   = '<span class="text-muted" style="font-size:11px;">NA</span>';

        $qtrIcon = function ($publish, $hasData) use ($tick, $cross, $na) {
            if (strtolower(trim($publish ?? '')) === 'no') return $na;
            return $hasData ? $tick : $cross;
        };

        $table = '<div class="combined-report-wrapper"><table class="combined-report-table table table-bordered">
            <thead>
                <tr>
                    <th rowspan="2">Company</th>
                    <th rowspan="2">Fincode</th>
                    <th colspan="9" class="text-center">P&amp;L</th>
                    <th colspan="5" class="text-center">B/S</th>
                    <th colspan="5" class="text-center">Cash Flow</th>
                </tr>
                <tr>
                    <th style="display:none;"></th><th style="display:none;"></th>
                    <th>Y1</th><th>Y2</th><th>Y3</th><th>Y4</th><th>Y5</th>
                    <th>Q1</th><th>Q2</th><th>Q3</th><th>Q4</th>
                    <th>Y1</th><th>Y2</th><th>Y3</th><th>Y4</th><th>Y5</th>
                    <th>Y1</th><th>Y2</th><th>Y3</th><th>Y4</th><th>Y5</th>
                </tr>
            </thead><tbody>';

        if (empty($rows)) {
            $table .= '<tr><td colspan="21" class="text-center text-muted py-3">No records found</td></tr>';
        } else {
            foreach ($rows as $r) {
                $pub = $r->UL_STOCKS_Qtr_Data_Publish ?? 'Yes';
                $table .= '<tr>
                    <td>' . e($r->company_name ?: '—') . '</td>
                    <td>' . $r->UL_ORD_FINCODE . '</td>
                    <td class="text-center">' . ($r->pnl_y1 ? $tick : $cross) . '</td>
                    <td class="text-center">' . ($r->pnl_y2 ? $tick : $cross) . '</td>
                    <td class="text-center">' . ($r->pnl_y3 ? $tick : $cross) . '</td>
                    <td class="text-center">' . ($r->pnl_y4 ? $tick : $cross) . '</td>
                    <td class="text-center">' . ($r->pnl_y5 ? $tick : $cross) . '</td>
                    <td class="text-center">' . $qtrIcon($pub, $r->pnl_q1) . '</td>
                    <td class="text-center">' . $qtrIcon($pub, $r->pnl_q2) . '</td>
                    <td class="text-center">' . $qtrIcon($pub, $r->pnl_q3) . '</td>
                    <td class="text-center">' . $qtrIcon($pub, $r->pnl_q4) . '</td>
                    <td class="text-center">' . ($r->bs_y1 ? $tick : $cross) . '</td>
                    <td class="text-center">' . ($r->bs_y2 ? $tick : $cross) . '</td>
                    <td class="text-center">' . ($r->bs_y3 ? $tick : $cross) . '</td>
                    <td class="text-center">' . ($r->bs_y4 ? $tick : $cross) . '</td>
                    <td class="text-center">' . ($r->bs_y5 ? $tick : $cross) . '</td>
                    <td class="text-center">' . ($r->cf_y1 ? $tick : $cross) . '</td>
                    <td class="text-center">' . ($r->cf_y2 ? $tick : $cross) . '</td>
                    <td class="text-center">' . ($r->cf_y3 ? $tick : $cross) . '</td>
                    <td class="text-center">' . ($r->cf_y4 ? $tick : $cross) . '</td>
                    <td class="text-center">' . ($r->cf_y5 ? $tick : $cross) . '</td>
                </tr>';
            }
        }

        $table .= '</tbody></table></div>';

        return response()->json([
            'table' => $table,
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
        ]);
    }

    // Last Insert Report — recently inserted financial records
    public function lastInsert(Request $request)
    {
        if (!$this->canAccess()) abort(403);

        $limit    = 50;
        $page     = max(0, (int) $request->input('page', 0));
        $offset   = $page * $limit;
        $search   = $request->input('searchtext', '');
        $fromDate = $request->input('from_date') ?: date('Y-m-d', strtotime('-30 days'));
        $toDate   = $request->input('to_date') ?: date('Y-m-d');

        $where    = "WHERE uf.UL_FIN_INSERT_TIME >= ? AND uf.UL_FIN_INSERT_TIME <= ?";
        $bindings = [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'];

        if ($search) {
            $where    .= " AND (us.UL_STOCKS_COMPNAME LIKE ? OR CAST(us.UL_STOCKS_FINCODE AS CHAR) LIKE ?)";
            $like      = '%' . $search . '%';
            $bindings  = array_merge($bindings, [$like, $like]);
        }

        $baseSql = "SELECT us.UL_STOCKS_COMPNAME, us.UL_STOCKS_FINCODE,
            uf.UL_FIN_Period_end, uf.UL_FIN_No_months, uf.UL_FIN_Type, uf.UL_FIN_INSERT_TIME
            FROM unlisted_financials AS uf
            LEFT JOIN unlisted_stocks AS us ON us.UL_STOCKS_FINCODE = uf.UL_FIN_FINCODE
            $where";

        $total = DB::selectOne("SELECT COUNT(*) AS cnt FROM ($baseSql) AS agg", $bindings)->cnt;
        $rows  = DB::select($baseSql . " ORDER BY uf.UL_FIN_INSERT_TIME DESC LIMIT $limit OFFSET $offset", $bindings);

        $periodLabel = function ($noMonths) {
            return match ((int) $noMonths) {
                3  => 'Quarterly',
                6  => 'Half Yearly',
                9  => '9 Monthly',
                12 => 'Yearly',
                default => $noMonths . ' Monthly',
            };
        };

        $table = '<table class="table table-sm table-striped table-bordered font-13-data-table w-100">
            <thead><tr>
                <th>Insert Date</th><th>Company</th><th>Fincode</th><th>Period</th>
            </tr></thead><tbody>';

        if (empty($rows)) {
            $table .= '<tr><td colspan="4" class="text-center text-muted py-3">No records found</td></tr>';
        } else {
            foreach ($rows as $r) {
                $insertDate = $r->UL_FIN_INSERT_TIME ? date('d-M-Y', strtotime($r->UL_FIN_INSERT_TIME)) : '—';
                $period     = $r->UL_FIN_Period_end
                    ? date('M Y', strtotime(substr($r->UL_FIN_Period_end, 0, 4) . '-' . substr($r->UL_FIN_Period_end, 4, 2) . '-01'))
                    : '—';
                $table .= '<tr>
                    <td>' . $insertDate . '</td>
                    <td>' . e($r->UL_STOCKS_COMPNAME ?: '—') . '</td>
                    <td>' . $r->UL_STOCKS_FINCODE . '</td>
                    <td>' . $period . ' <small class="text-muted">(' . $periodLabel($r->UL_FIN_No_months) . ')</small></td>
                </tr>';
            }
        }

        $table .= '</tbody></table>';

        return response()->json([
            'table'     => $table,
            'total'     => $total,
            'page'      => $page,
            'limit'     => $limit,
            'from_date' => $fromDate,
            'to_date'   => $toDate,
        ]);
    }
}
