<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnlistedOrdersController extends Controller
{
    public function orders()
    {
        if (!$this->canAccess()) abort(403);

        $adminUsers = User::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(privilege, '$.unlisted.leads')) = 'true'")
            ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(privilege, '$.unlisted.leads_allocation')) = 'true'")
            ->orderBy('name')
            ->get(['uid', 'name']);

        return view('admin.unlisted.orders', compact('adminUsers'));
    }

    public function ordersData(Request $request)
    {
        if (!$this->canAccess()) abort(403);

        $query = DB::table('unlisted_orders')
            ->select([
                'unlisted_orders.*',
                'u.name as customer_name',
                's.UL_STOCKS_COMPNAME as share_name',
                's.UL_STOCKS_LOT_SIZE as lot_size',
                'dealer.name as dealer_name',
                'alloc.name as allocated_name',
            ])
            ->leftJoin('users as u', 'u.uid', '=', 'unlisted_orders.UL_ORD_USER_ID')
            ->leftJoin('unlisted_stocks as s', 's.UL_STOCKS_FINCODE', '=', 'unlisted_orders.UL_ORD_FINCODE')
            ->leftJoin('users as dealer', 'dealer.uid', '=', 'unlisted_orders.UL_ORD_INTERMEDIARY_USER_ID')
            ->leftJoin(DB::raw('(
                SELECT l.UL_LEAD_UID, l.UL_LEAD_ALLOCATED_TO
                FROM unlisted_leads l
                INNER JOIN (
                    SELECT UL_LEAD_UID, MAX(UL_LEAD_ID) as max_id
                    FROM unlisted_leads
                    GROUP BY UL_LEAD_UID
                ) ml ON l.UL_LEAD_UID = ml.UL_LEAD_UID AND l.UL_LEAD_ID = ml.max_id
            ) as latest_lead'), 'latest_lead.UL_LEAD_UID', '=', 'unlisted_orders.UL_ORD_USER_ID')
            ->leftJoin('users as alloc', 'alloc.uid', '=', 'latest_lead.UL_LEAD_ALLOCATED_TO')
            ->orderByDesc('unlisted_orders.UL_ORD_ID');

        if ($s = trim($request->input('searchtext', ''))) {
            $query->where(function ($q) use ($s) {
                $q->where('u.name', 'like', "%{$s}%")
                  ->orWhere('s.UL_STOCKS_COMPNAME', 'like', "%{$s}%")
                  ->orWhere('unlisted_orders.UL_ORD_USER_ID', $s)
                  ->orWhere('unlisted_orders.UL_ORD_ID', $s);
            });
        }

        if ($type = $request->input('type', '')) {
            $query->whereRaw('LOWER(unlisted_orders.UL_ORD_TYPE) = ?', [strtolower($type)]);
        }

        if ($status = $request->input('status', '')) {
            $query->where('unlisted_orders.UL_ORD_STATUS', $status);
        }

        if ($from = $request->input('date_from', '')) {
            $query->whereDate('unlisted_orders.UL_ORD_INSERT_TIME', '>=', $from);
        }

        if ($to = $request->input('date_to', '')) {
            $query->whereDate('unlisted_orders.UL_ORD_INSERT_TIME', '<=', $to);
        }

        $orders = $query->paginate(25);

        return view('admin.unlisted.partials.orders-rows', compact('orders'));
    }

    public function updateOrder(Request $request, int $orderId)
    {
        if (!$this->canAccess()) return response()->json(['success' => false], 403);

        $exists = DB::table('unlisted_orders')->where('UL_ORD_ID', $orderId)->exists();
        if (!$exists) return response()->json(['success' => false, 'message' => 'Order not found.'], 404);

        $qty   = (int) $request->input('qty', 0);
        $price = (float) $request->input('price_per_share', 0);

        $dateStr = $request->input('order_date', '');
        $hr      = $request->input('order_hr', '0');
        $min     = $request->input('order_min', '0');
        $orderDate = null;
        if ($dateStr) {
            $orderDate = $dateStr . ' ' . str_pad($hr, 2, '0', STR_PAD_LEFT) . ':' . str_pad($min, 2, '0', STR_PAD_LEFT) . ':00';
        }

        DB::table('unlisted_orders')->where('UL_ORD_ID', $orderId)->update([
            'UL_ORD_TYPE'                    => $request->input('type', ''),
            'UL_ORD_QUANTITY'                => $qty ?: null,
            'UL_ORD_PRICE_PER_SHARE'         => $price ?: null,
            'UL_ORD_AMOUNT'                  => ($qty && $price) ? $qty * $price : null,
            'UL_ORD_STATUS'                  => $request->input('status', ''),
            'UL_ORD_SUB_STATUS'              => $request->input('sub_status', ''),
            'UL_ORD_DATE'                    => $orderDate,
            'UL_ORD_INTERMEDIARY_USER_ID'    => $request->input('intermediary_uid') ?: null,
            'UL_ORD_INTERMEDIARY_MARGIN'     => $request->input('margin') !== '' ? (float) $request->input('margin') : null,
            'UL_ORD_INTERMEDIARY_COMMISSION' => $request->input('commission') !== '' ? (float) $request->input('commission') : null,
            'UL_ORD_LP'                      => $request->input('lp') !== '' ? (float) $request->input('lp') : null,
            'UL_ORD_MLP'                     => $request->input('mlp') !== '' ? (float) $request->input('mlp') : null,
            'UL_ORD_ADDED_BY'                => $request->input('added_by') ?: null,
            'UL_ORD_DIRECT_FLAG'             => $request->input('direct_flag') ? 1 : 0,
            'UL_ORD_UPDATE_TIME'             => now(),
        ]);

        return response()->json(['success' => true]);
    }

    private function canAccess(): bool
    {
        $priv = session('privilege.unlisted', []);
        return !empty(session('privilege.admin'))
            || !empty(session('privilege.user_master'))
            || !empty($priv['orders']);
    }
}
