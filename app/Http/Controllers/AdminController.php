<?php

namespace App\Http\Controllers;

use App\Helpers\Privilege;
use App\Models\Article;
use App\Models\User;
use App\Models\UnlistedLead;
use App\Models\UnlistedOrder;
use App\Models\UnlistedStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function redirectToDashboard()
    {
        if (!empty(Privilege::get('admin'))) {
            return redirect()->route('admin.dashboard');
        }
        if (!empty(Privilege::get('user_master'))) {
            return redirect()->route('admin.users');
        }
        if (!empty(Privilege::get('unlisted.stocks'))) {
            return redirect()->route('admin.unlisted');
        }
        if (!empty(Privilege::get('unlisted.leads')) || !empty(Privilege::get('unlisted.leads_allocation'))) {
            return redirect()->route('admin.unlisted.leads');
        }
        if (!empty(Privilege::get('pg.dashboard'))) {
            return redirect()->route('admin.pg.dashboard');
        }
        if (!empty(Privilege::get('pg.margin'))) {
            return redirect()->route('admin.pg.margin');
        }
        if (!empty(Privilege::get('pg.margin_error'))) {
            return redirect()->route('admin.pg.margin-error');
        }

        abort(403, 'You do not have admin access.');
    }

    public function dashboard()
    {
        // ── Users ────────────────────────────────────────────────────────────
        $totalUsers      = User::count();
        $adminUsers      = User::whereNotNull('privilege')->get()->filter(fn($u) => !empty($u->privilege['admin']))->count();
        $unlistedUsers   = User::where('user_type', 'unlisted')->count();
        $channelPartners = User::where('user_type', 'channel_partner')->count();
        $newUsers30d     = User::where('created_at', '>=', now()->subDays(30))->count();

        // ── Stocks ───────────────────────────────────────────────────────────
        $totalStocks  = UnlistedStock::count();
        $activeStocks = UnlistedStock::where('UL_STOCKS_STATUS', '1')->count();

        // ── Orders ───────────────────────────────────────────────────────────
        $totalOrders     = UnlistedOrder::count();
        $completedOrders = UnlistedOrder::where('UL_ORD_STATUS', 'Completed')->count();
        $totalOrderValue = (float) UnlistedOrder::where('UL_ORD_STATUS', 'Completed')->sum('UL_ORD_AMOUNT');

        $buyCount  = UnlistedOrder::where('UL_ORD_STATUS', 'Completed')->where('UL_ORD_TYPE', 'Buy')->count();
        $sellCount = UnlistedOrder::where('UL_ORD_STATUS', 'Completed')->where('UL_ORD_TYPE', 'Sell')->count();

        // Completed order volume for the last 6 months (chart)
        $months = collect(range(5, 0))->map(fn($i) => now()->subMonths($i)->format('Y-m'));

        $monthlyRaw = UnlistedOrder::where('UL_ORD_STATUS', 'Completed')
            ->where('UL_ORD_DATE', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(UL_ORD_DATE, '%Y-%m') as ym, COUNT(*) as cnt, SUM(UL_ORD_AMOUNT) as amt")
            ->groupBy('ym')
            ->pluck('cnt', 'ym');

        $monthlyAmtRaw = UnlistedOrder::where('UL_ORD_STATUS', 'Completed')
            ->where('UL_ORD_DATE', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(UL_ORD_DATE, '%Y-%m') as ym, SUM(UL_ORD_AMOUNT) as amt")
            ->groupBy('ym')
            ->pluck('amt', 'ym');

        $orderMonthLabels = $months->map(fn($ym) => Carbon::createFromFormat('Y-m', $ym)->format('M Y'))->values();
        $orderMonthCounts  = $months->map(fn($ym) => (int) ($monthlyRaw[$ym] ?? 0))->values();
        $orderMonthAmounts = $months->map(fn($ym) => round((float) ($monthlyAmtRaw[$ym] ?? 0)))->values();

        // Top 5 stocks by completed order value
        $topStocks = UnlistedOrder::query()
            ->join('unlisted_stocks', 'unlisted_stocks.UL_STOCKS_FINCODE', '=', 'unlisted_orders.UL_ORD_FINCODE')
            ->where('unlisted_orders.UL_ORD_STATUS', 'Completed')
            ->selectRaw('unlisted_stocks.UL_STOCKS_COMPNAME as name, SUM(unlisted_orders.UL_ORD_AMOUNT) as total')
            ->groupBy('unlisted_stocks.UL_STOCKS_COMPNAME')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Recent orders
        $recentOrders = UnlistedOrder::query()
            ->leftJoin('unlisted_stocks', 'unlisted_stocks.UL_STOCKS_FINCODE', '=', 'unlisted_orders.UL_ORD_FINCODE')
            ->leftJoin('users', 'users.uid', '=', 'unlisted_orders.UL_ORD_USER_ID')
            ->orderByDesc('unlisted_orders.UL_ORD_ID')
            ->limit(8)
            ->get([
                'unlisted_orders.UL_ORD_ID',
                'unlisted_orders.UL_ORD_TYPE',
                'unlisted_orders.UL_ORD_QUANTITY',
                'unlisted_orders.UL_ORD_AMOUNT',
                'unlisted_orders.UL_ORD_STATUS',
                'unlisted_orders.UL_ORD_DATE',
                'unlisted_stocks.UL_STOCKS_COMPNAME',
                'users.name as user_name',
            ]);

        // ── Leads ────────────────────────────────────────────────────────────
        $totalLeads = UnlistedLead::count();
        $leads30d   = UnlistedLead::where('UL_LEAD_INSERT_TIME', '>=', now()->subDays(30))->count();

        // ── PG / withdrawal requests ─────────────────────────────────────────
        $pendingWithdrawals = DB::table('withdrawal_request')
            ->where(function ($q) {
                $q->whereNull('REQUEST_STATUS')->orWhere('REQUEST_STATUS', '')->orWhere('REQUEST_STATUS', 'Pending');
            })
            ->count();

        // ── CMS / Articles ───────────────────────────────────────────────────
        $totalArticles     = Article::count();
        $publishedArticles = Article::where('status', 'published')->count();
        $draftArticles     = Article::where('status', 'draft')->count();
        $articles30d       = Article::where('status', 'published')
            ->where('published_at', '>=', now()->subDays(30))
            ->count();

        return view('admin.dashboard', compact(
            'totalUsers', 'adminUsers', 'unlistedUsers', 'channelPartners', 'newUsers30d',
            'totalStocks', 'activeStocks',
            'totalOrders', 'completedOrders', 'totalOrderValue', 'buyCount', 'sellCount',
            'orderMonthLabels', 'orderMonthCounts', 'orderMonthAmounts',
            'topStocks', 'recentOrders',
            'totalLeads', 'leads30d',
            'pendingWithdrawals',
            'totalArticles', 'publishedArticles', 'draftArticles', 'articles30d'
        ));
    }
}
