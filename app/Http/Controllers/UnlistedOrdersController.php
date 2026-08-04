<?php

namespace App\Http\Controllers;

use App\Helpers\Privilege;
use App\Models\User;
use App\Models\UnlistedOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnlistedOrdersController extends Controller
{
    public function orders()
    {
        if (!$this->canAccess()) abort(403);

        // Same "leads OR leads_allocation" definition of an eligible intermediary/
        // added-by staff member used elsewhere (UnlistedLeadsController::getLeadAgents).
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

        $order = DB::table('unlisted_orders')->where('UL_ORD_ID', $orderId)->first();
        if (!$order) return response()->json(['success' => false, 'message' => 'Order not found.'], 404);

        $newStatus = $request->has('status') ? $request->input('status', '') : $order->UL_ORD_STATUS;

        // Completed is a terminal state — cannot be moved to any other status from here.
        if ($order->UL_ORD_STATUS === 'Completed' && $request->has('status') && $newStatus !== 'Completed') {
            return response()->json(['success' => false, 'message' => 'Completed orders cannot be reverted to another status.']);
        }

        // Completing an order requires LP and Order Added By to be set (existing value counts too).
        if ($newStatus === 'Completed') {
            $lp      = $request->has('lp')       ? $request->input('lp')       : $order->UL_ORD_LP;
            $addedBy = $request->has('added_by') ? $request->input('added_by') : $order->UL_ORD_ADDED_BY;
            if ($lp === null || $lp === '' || !$addedBy) {
                return response()->json(['success' => false, 'message' => 'LP and Order Added By must be set before marking an order Completed.']);
            }
        }

        $data = [];

        if ($request->has('type')) $data['UL_ORD_TYPE'] = $request->input('type', '');

        if ($request->has('qty') || $request->has('price_per_share')) {
            $qty   = $request->has('qty')             ? (int) $request->input('qty', 0)             : (int) $order->UL_ORD_QUANTITY;
            $price = $request->has('price_per_share') ? (float) $request->input('price_per_share', 0) : (float) $order->UL_ORD_PRICE_PER_SHARE;
            if ($request->has('qty'))              $data['UL_ORD_QUANTITY']        = $qty ?: null;
            if ($request->has('price_per_share'))  $data['UL_ORD_PRICE_PER_SHARE'] = $price ?: null;
            $data['UL_ORD_AMOUNT'] = ($qty && $price) ? $qty * $price : null;
        }

        if ($request->has('status'))     $data['UL_ORD_STATUS']     = $request->input('status', '');
        if ($request->has('sub_status')) $data['UL_ORD_SUB_STATUS'] = $request->input('sub_status', '');

        if ($request->has('order_date')) {
            $dateStr = $request->input('order_date', '');
            $hr      = $request->input('order_hr', '0');
            $min     = $request->input('order_min', '0');
            $data['UL_ORD_DATE'] = $dateStr
                ? ($dateStr . ' ' . str_pad($hr, 2, '0', STR_PAD_LEFT) . ':' . str_pad($min, 2, '0', STR_PAD_LEFT) . ':00')
                : null;
        }

        if ($request->has('intermediary_uid')) {
            $intermediaryUid = $request->input('intermediary_uid');

            if ($intermediaryUid === '' || $intermediaryUid === null) {
                $data['UL_ORD_INTERMEDIARY_USER_ID'] = null;
            } else {
                // The dropdown only *shows* leads-eligible staff — that's UX, not
                // security. A direct API call could submit any uid, so re-check the
                // same eligibility rule server-side before saving it.
                $intermediaryUid = (int) $intermediaryUid;
                $isEligibleIntermediary = User::whereKey($intermediaryUid)
                    ->where(function ($q) {
                        $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(privilege, '$.unlisted.leads')) = 'true'")
                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(privilege, '$.unlisted.leads_allocation')) = 'true'");
                    })
                    ->exists();

                abort_if(!$isEligibleIntermediary, 422, 'Selected intermediary is not a valid leads-eligible staff user.');
                $data['UL_ORD_INTERMEDIARY_USER_ID'] = $intermediaryUid;
            }
        }
        if ($request->has('margin'))            $data['UL_ORD_INTERMEDIARY_MARGIN']     = $request->input('margin') !== '' ? (float) $request->input('margin') : null;
        if ($request->has('commission'))        $data['UL_ORD_INTERMEDIARY_COMMISSION'] = $request->input('commission') !== '' ? (float) $request->input('commission') : null;
        if ($request->has('lp'))                $data['UL_ORD_LP']                      = $request->input('lp') !== '' ? (float) $request->input('lp') : null;
        if ($request->has('mlp'))                $data['UL_ORD_MLP']                    = $request->input('mlp') !== '' ? (float) $request->input('mlp') : null;
        if ($request->has('added_by'))          $data['UL_ORD_ADDED_BY']                = $request->input('added_by') ?: null;
        if ($request->has('direct_flag'))       $data['UL_ORD_DIRECT_FLAG']             = $request->input('direct_flag') ? 1 : 0;

        $data['UL_ORD_UPDATE_TIME'] = now();

        DB::table('unlisted_orders')->where('UL_ORD_ID', $orderId)->update($data);

        return response()->json(['success' => true]);
    }

    public function searchCustomers(Request $request)
    {
        if (!Privilege::get('unlisted.orders')) abort(403);

        $q = trim($request->input('q', ''));
        if (strlen($q) < 1) return response()->json([]);

        $rows = DB::select(
            "SELECT uid, name, phone FROM users
             WHERE (name LIKE ? OR uid LIKE ? OR phone LIKE ?)
             ORDER BY name LIMIT 20",
            ['%'.$q.'%', '%'.$q.'%', '%'.$q.'%']
        );

        return response()->json(array_map(fn ($r) => [
            'uid'   => $r->uid,
            'label' => $r->uid . ' — ' . $r->name . ($r->phone ? ' (' . $r->phone . ')' : ''),
        ], $rows));
    }

    public function searchStocks(Request $request)
    {
        if (!Privilege::get('unlisted.orders')) abort(403);

        $q = trim($request->input('q', ''));
        if (strlen($q) < 2) return response()->json([]);

        // Same "latest price per fincode" pattern as StocksController::priceListResults.
        $rows = DB::select(
            "SELECT
                s.UL_STOCKS_FINCODE AS fincode,
                s.UL_STOCKS_COMPNAME AS name,
                s.UL_STOCKS_LOT_SIZE AS lot_size,
                p.UL_PD_BID_PRICE AS bid_price
             FROM unlisted_stocks s
             LEFT JOIN (
                 SELECT pd.*
                 FROM unlisted_price_data pd
                 INNER JOIN (
                     SELECT UL_PD_FINCODE, MAX(UL_PD_DATE) AS max_date
                     FROM unlisted_price_data
                     WHERE UL_PD_INVALID_FLAG = 0
                     GROUP BY UL_PD_FINCODE
                 ) lp ON lp.UL_PD_FINCODE = pd.UL_PD_FINCODE AND pd.UL_PD_DATE = lp.max_date
                 WHERE pd.UL_PD_INVALID_FLAG = 0
             ) p ON p.UL_PD_FINCODE = s.UL_STOCKS_FINCODE
             WHERE s.UL_STOCKS_COMPNAME LIKE ?
             ORDER BY s.UL_STOCKS_COMPNAME LIMIT 20",
            ['%'.$q.'%']
        );

        return response()->json(array_map(fn ($r) => [
            'fincode'   => $r->fincode,
            'label'     => $r->name,
            'lot_size'  => $r->lot_size,
            'bid_price' => $r->bid_price,
        ], $rows));
    }

    public function storeOrder(Request $request)
    {
        if (!$this->canAccess()) return response()->json(['success' => false], 403);

        $validated = $request->validate([
            'uid'             => 'required|integer|exists:users,uid',
            'fincode'         => 'required|integer|exists:unlisted_stocks,UL_STOCKS_FINCODE',
            'type'            => 'required|in:Buy,Sell',
            'qty'             => 'required|integer|min:1',
            'price_per_share' => 'required|numeric|min:0.01',
        ], [
            'uid.required'     => 'Please select a customer.',
            'uid.exists'       => 'Selected customer could not be found.',
            'fincode.required' => 'Please select a company.',
            'fincode.exists'   => 'Selected company could not be found.',
        ]);

        $qty   = (int) $validated['qty'];
        $price = (float) $validated['price_per_share'];

        $order = UnlistedOrder::create([
            'UL_ORD_USER_ID'         => $validated['uid'],
            'UL_ORD_FINCODE'         => $validated['fincode'],
            'UL_ORD_TYPE'            => $validated['type'],
            'UL_ORD_QUANTITY'        => $qty,
            'UL_ORD_PRICE_PER_SHARE' => $price,
            'UL_ORD_AMOUNT'          => $qty * $price,
            'UL_ORD_STATUS'          => 'Pending',
            'UL_ORD_ADDED_BY'        => session('uid'),
            'UL_ORD_INSERT_TIME'     => now(),
            'UL_ORD_UPDATE_TIME'     => now(),
            'UL_ORD_DATE'            => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Order created successfully.', 'order_id' => $order->UL_ORD_ID]);
    }

    private function canAccess(): bool
    {
        return !empty(Privilege::get('admin'))
            || !empty(Privilege::get('unlisted.orders'));
    }
}
