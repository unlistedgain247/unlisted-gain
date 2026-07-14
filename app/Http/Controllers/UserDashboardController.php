<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserDashboardController extends Controller
{
    public function profile(string $uid)
    {
        $user = DB::table('users')->where('uid', $uid)->first();
        if (!$user) abort(404);

        $lead = DB::table('unlisted_leads')
            ->where('UL_LEAD_UID', $uid)
            ->orderByDesc('UL_LEAD_ID')
            ->first();

        $allocatedName = null;
        if ($lead && $lead->UL_LEAD_ALLOCATED_TO) {
            $allocatedName = DB::table('users')
                ->where('uid', $lead->UL_LEAD_ALLOCATED_TO)
                ->value('name');
        }

        $balance = DB::table('pg_transactions')
            ->where('pgt_transaction_for_user_id', $uid)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN pgt_transaction_type = 'Flow In'  THEN pgt_in_out_amount ELSE 0 END), 0) -
                COALESCE(SUM(CASE WHEN pgt_transaction_type = 'Flow Out' THEN pgt_in_out_amount ELSE 0 END), 0)
                AS balance
            ")
            ->value('balance') ?? 0;

        $ordersResult = DB::table('unlisted_orders')
            ->where('UL_ORD_USER_ID', $uid)
            ->where('UL_ORD_STATUS', 'Completed')
            ->selectRaw('COALESCE(SUM(UL_ORD_AMOUNT), 0) AS total, COUNT(*) AS cnt')
            ->first();

        return view('admin.partials.user-dashboard.profile',
            compact('user', 'lead', 'allocatedName', 'balance', 'ordersResult'));
    }

    public function orders(string $uid)
    {
        $user = DB::table('users')->where('uid', $uid)->first();
        if (!$user) abort(404);

        $orders = DB::table('unlisted_orders')
            ->where('UL_ORD_USER_ID', $uid)
            ->leftJoin('unlisted_stocks', 'unlisted_stocks.UL_STOCKS_FINCODE', '=', 'unlisted_orders.UL_ORD_FINCODE')
            ->leftJoin('users AS dealer', 'dealer.uid', '=', 'unlisted_orders.UL_ORD_INTERMEDIARY_USER_ID')
            ->select(
                'unlisted_orders.*',
                'unlisted_stocks.UL_STOCKS_S_NAME',
                'unlisted_stocks.UL_STOCKS_COMPNAME',
                'dealer.name AS dealer_name'
            )
            ->orderByDesc('UL_ORD_ID')
            ->limit(100)
            ->get();

        return view('admin.partials.user-dashboard.orders', compact('orders', 'uid'));
    }

    public function demat(string $uid)
    {
        $user = DB::table('users')->where('uid', $uid)->first();
        if (!$user) abort(404);

        $holdings = DB::table('unlisted_orders')
            ->where('UL_ORD_USER_ID', $uid)
            ->where('UL_ORD_STATUS', 'Completed')
            ->leftJoin('unlisted_stocks', 'unlisted_stocks.UL_STOCKS_FINCODE', '=', 'unlisted_orders.UL_ORD_FINCODE')
            ->select(
                'UL_ORD_FINCODE',
                'unlisted_stocks.UL_STOCKS_S_NAME',
                'unlisted_stocks.UL_STOCKS_COMPNAME',
                DB::raw("SUM(CASE WHEN UL_ORD_TYPE = 'buy' THEN UL_ORD_QUANTITY ELSE -UL_ORD_QUANTITY END) AS net_qty")
            )
            ->groupBy('UL_ORD_FINCODE', 'unlisted_stocks.UL_STOCKS_S_NAME', 'unlisted_stocks.UL_STOCKS_COMPNAME')
            ->havingRaw('net_qty > 0')
            ->get();

        return view('admin.partials.user-dashboard.demat', compact('holdings', 'uid'));
    }

    public function portfolio(string $uid)
    {
        $user = DB::table('users')->where('uid', $uid)->first();
        if (!$user) abort(404);

        $portfolio = DB::table('unlisted_orders')
            ->where('UL_ORD_USER_ID', $uid)
            ->where('UL_ORD_STATUS', 'Completed')
            ->leftJoin('unlisted_stocks', 'unlisted_stocks.UL_STOCKS_FINCODE', '=', 'unlisted_orders.UL_ORD_FINCODE')
            ->select(
                'UL_ORD_FINCODE',
                'unlisted_stocks.UL_STOCKS_S_NAME',
                'unlisted_stocks.UL_STOCKS_COMPNAME',
                DB::raw("SUM(CASE WHEN UL_ORD_TYPE = 'buy' THEN UL_ORD_QUANTITY ELSE -UL_ORD_QUANTITY END) AS net_qty"),
                DB::raw("SUM(CASE WHEN UL_ORD_TYPE = 'buy' THEN UL_ORD_AMOUNT ELSE -UL_ORD_AMOUNT END) AS net_cost"),
                DB::raw("SUM(CASE WHEN UL_ORD_TYPE = 'buy' THEN UL_ORD_QUANTITY ELSE 0 END) AS buy_qty"),
                DB::raw("SUM(CASE WHEN UL_ORD_TYPE = 'buy' THEN UL_ORD_AMOUNT ELSE 0 END) AS buy_cost")
            )
            ->groupBy('UL_ORD_FINCODE', 'unlisted_stocks.UL_STOCKS_S_NAME', 'unlisted_stocks.UL_STOCKS_COMPNAME')
            ->havingRaw('net_qty > 0')
            ->get();

        return view('admin.partials.user-dashboard.portfolio', compact('portfolio', 'uid'));
    }

    public function transactions(string $uid)
    {
        $user = DB::table('users')->where('uid', $uid)->first();
        if (!$user) abort(404);

        $transactions = DB::table('pg_transactions')
            ->where('pgt_transaction_for_user_id', $uid)
            ->orderByDesc('pgt_tid')
            ->limit(50)
            ->get();

        return view('admin.partials.user-dashboard.transactions', compact('transactions', 'uid'));
    }

    public function requestHistory(string $uid)
    {
        $user = DB::table('users')->where('uid', $uid)->first();
        if (!$user) abort(404);

        $requests = DB::table('withdrawal_request')
            ->where('REQUEST_USER_ID', $uid)
            ->leftJoin('unlisted_stocks', 'unlisted_stocks.UL_STOCKS_FINCODE', '=', 'withdrawal_request.REQUEST_FINCODE')
            ->select('withdrawal_request.*', 'unlisted_stocks.UL_STOCKS_S_NAME')
            ->orderByDesc('REQUEST_ID')
            ->limit(50)
            ->get();

        return view('admin.partials.user-dashboard.request-history', compact('requests', 'user', 'uid'));
    }

    public function bankDemat(string $uid)
    {
        $user = DB::table('users')->where('uid', $uid)->first();
        if (!$user) abort(404);

        return view('admin.partials.user-dashboard.bank-demat', compact('user'));
    }

    public function getCommunication(string $uid)
    {
        $user = DB::table('users')->where('uid', $uid)->first();
        if (!$user) abort(404);

        $restriction = DB::table('user_communication_restrictions')
            ->where('user_id', $uid)
            ->first();

        return view('admin.partials.user-dashboard.communication', compact('restriction', 'uid'));
    }

    public function saveCommunication(Request $request, string $uid)
    {
        DB::table('user_communication_restrictions')->updateOrInsert(
            ['user_id' => $uid],
            [
                'whatsapp'   => $request->input('whatsapp', 0) ? 1 : 0,
                'email'      => $request->input('email', 0)    ? 1 : 0,
                'sms'        => $request->input('sms', 0)      ? 1 : 0,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Communication preferences saved.']);
    }

    public function cancelRequest(string $uid, int $requestId)
    {
        $affected = DB::table('withdrawal_request')
            ->where('REQUEST_ID', $requestId)
            ->where('REQUEST_USER_ID', $uid)
            ->whereNotIn('REQUEST_STATUS', ['Completed', 'Cancelled'])
            ->update(['REQUEST_STATUS' => 'Cancelled']);

        if ($affected) {
            return response()->json(['success' => true, 'message' => 'Request cancelled.']);
        }
        return response()->json(['success' => false, 'message' => 'Request not found or already processed.']);
    }

    public function withdrawForm(string $uid)
    {
        $user = DB::table('users')->where('uid', $uid)->first();
        if (!$user) abort(404);

        return view('admin.partials.user-dashboard.withdraw', compact('uid'));
    }

    public function saveWithdraw(Request $request, string $uid)
    {
        $type = $request->input('type');
        if (!in_array($type, ['Cash', 'Shares'])) {
            return response()->json(['success' => false, 'message' => 'Invalid request type.']);
        }

        if ($type === 'Cash') {
            $amount = (float) $request->input('amount', 0);
            if ($amount <= 0) {
                return response()->json(['success' => false, 'message' => 'Enter a valid amount.']);
            }
            DB::table('withdrawal_request')->insert([
                'REQUEST_USER_ID' => $uid,
                'REQUEST_TYPE'    => 'Cash',
                'REQUEST_AMOUNT'  => $amount,
                'REQUEST_STATUS'  => 'Pending',
                'REQUEST_DATE'    => now()->toDateString(),
            ]);
        } else {
            $fincode = (int) $request->input('fincode', 0);
            $qty     = (float) $request->input('qty', 0);
            if (!$fincode || $qty <= 0) {
                return response()->json(['success' => false, 'message' => 'Select a stock and enter valid quantity.']);
            }
            DB::table('withdrawal_request')->insert([
                'REQUEST_USER_ID' => $uid,
                'REQUEST_TYPE'    => 'Shares',
                'REQUEST_FINCODE' => $fincode,
                'REQUEST_QTY'     => $qty,
                'REQUEST_STATUS'  => 'Pending',
                'REQUEST_DATE'    => now()->toDateString(),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Withdrawal request submitted.']);
    }
}
