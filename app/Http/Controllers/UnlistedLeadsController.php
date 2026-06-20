<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UnlistedLead;
use App\Models\UnlistedLeadActivity;
use App\Models\UnlistedOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnlistedLeadsController extends Controller
{
    public function leads()
    {
        $isAdmin     = !empty(user_privilege('admin')) || !empty(user_privilege('user_master'));
        $canAllocate = $isAdmin || !empty(user_privilege('unlisted.leads_allocation'));
        $canSeeOwn   = !empty(user_privilege('unlisted.leads'));

        if (!$canAllocate && !$canSeeOwn) abort(403);

        $leadAgents = $this->getLeadAgents();

        return view('admin.unlisted.leads', compact('leadAgents', 'canAllocate'));
    }

    public function leadsData(Request $request)
    {
        $isAdmin     = !empty(user_privilege('admin')) || !empty(user_privilege('user_master'));
        $canAllocate = $isAdmin || !empty(user_privilege('unlisted.leads_allocation'));
        $canSeeOwn   = !empty(user_privilege('unlisted.leads'));

        if (!$canAllocate && !$canSeeOwn) abort(403);

        $query = DB::table('unlisted_leads')
            ->select([
                'unlisted_leads.*',
                'u.name as user_name',
                'u.email',
                'u.phone',
                'a.name as allocated_name',
            ])
            ->leftJoin('users as u', 'u.uid', '=', 'unlisted_leads.UL_LEAD_UID')
            ->leftJoin('users as a', 'a.uid', '=', 'unlisted_leads.UL_LEAD_ALLOCATED_TO')
            ->orderByDesc('unlisted_leads.UL_LEAD_ID');

        if (!$canAllocate) {
            $query->where('unlisted_leads.UL_LEAD_ALLOCATED_TO', session('uid'));
        }

        if ($s = trim($request->input('searchtext', ''))) {
            $query->where(function ($q) use ($s) {
                $q->where('u.name',  'like', "%{$s}%")
                  ->orWhere('u.email', 'like', "%{$s}%")
                  ->orWhere('u.phone', 'like', "%{$s}%")
                  ->orWhere('unlisted_leads.UL_LEAD_UID', 'like', "%{$s}%");
            });
        }

        if ($disp = $request->input('disposition', '')) {
            if ($disp === 'Fresh') {
                $query->where(function ($q) {
                    $q->whereNull('unlisted_leads.UL_LEAD_DISPOSITION')
                      ->orWhere('unlisted_leads.UL_LEAD_DISPOSITION', '')
                      ->orWhere('unlisted_leads.UL_LEAD_DISPOSITION', 'New Lead');
                });
            } else {
                $query->where('unlisted_leads.UL_LEAD_DISPOSITION', $disp);
            }
        }

        if ($allocated = $request->input('allocated_to', '')) {
            if ($allocated === 'unallocated') {
                $query->where(function ($q) {
                    $q->whereNull('unlisted_leads.UL_LEAD_ALLOCATED_TO')
                      ->orWhere('unlisted_leads.UL_LEAD_ALLOCATED_TO', 0);
                });
            } else {
                $query->where('unlisted_leads.UL_LEAD_ALLOCATED_TO', $allocated);
            }
        }

        if ($cb = $request->input('callback', '')) {
            match ($cb) {
                'today'    => $query->whereDate('unlisted_leads.UL_LEAD_CALLBACK_TIME', today()),
                'overdue'  => $query->where('unlisted_leads.UL_LEAD_CALLBACK_TIME', '<', now())
                                    ->where('unlisted_leads.UL_LEAD_CALLBACK_TIME', '>', '2022-01-01 00:00:00'),
                'tomorrow' => $query->whereDate('unlisted_leads.UL_LEAD_CALLBACK_TIME', today()->addDay()),
                'upcoming' => $query->whereDate('unlisted_leads.UL_LEAD_CALLBACK_TIME', '>', today()->addDay()),
                default    => null,
            };
        }

        $reqCall = strtolower($request->input('request_for_call', ''));
        if ($reqCall === 'yes') {
            $query->whereRaw("LOWER(unlisted_leads.UL_LEAD_REQUEST_FOR_CALL) = 'yes'");
        } elseif ($reqCall === 'no') {
            $query->where(function ($q) {
                $q->whereRaw("LOWER(unlisted_leads.UL_LEAD_REQUEST_FOR_CALL) != 'yes'")
                  ->orWhereNull('unlisted_leads.UL_LEAD_REQUEST_FOR_CALL');
            });
        }

        $leads      = $query->paginate(25);
        $leadAgents = $this->getLeadAgents();

        return view('admin.unlisted.partials.leads-rows', compact('leads', 'canAllocate', 'leadAgents'));
    }

    public function allocateLead(Request $request, int $leadId)
    {
        $canAllocate = !empty(user_privilege('admin'))
                    || !empty(user_privilege('user_master'))
                    || !empty(user_privilege('unlisted.leads_allocation'));

        if (!$canAllocate) return response()->json(['success' => false], 403);

        $lead     = UnlistedLead::findOrFail($leadId);
        $agentUid = $request->input('allocated_to') ?: null;

        $lead->update([
            'UL_LEAD_ALLOCATED_TO' => $agentUid,
            'UL_LEAD_UPDATE_TIME'  => now(),
        ]);

        UnlistedLeadActivity::create([
            'UL_LEAD_ACTY_LID'       => $leadId,
            'UL_LEAD_ACTY_UID'       => session('uid'),
            'UL_LEAD_ACTY_TYPE'      => $agentUid ? 'Allocation' : 'Deallocated',
            'UL_LEAD_ACTY_COMMENT'   => $agentUid
                ? 'Allocated to: ' . (User::where('uid', $agentUid)->value('name') ?? $agentUid)
                : 'Allocation removed',
            'UL_LEAD_ACTY_TIMESTAMP' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function saveDisposition(Request $request, int $leadId)
    {
        if (!$this->canAccessLeads()) return response()->json(['success' => false], 403);

        $lead = UnlistedLead::findOrFail($leadId);

        $cb   = trim($request->input('callback_time', ''));
        $data = [
            'UL_LEAD_DISPOSITION'         => $request->input('disposition', ''),
            'UL_LEAD_SUB_DISPOSITION'     => $request->input('sub_disposition', ''),
            'UL_LEAD_DISPOSITION_COMMENT' => $request->input('comment', ''),
            'UL_LEAD_DISPOSITION_TIME'    => now(),
            'UL_LEAD_UPDATE_TIME'         => now(),
            'UL_LEAD_CALLBACK_TIME'       => ($cb && $cb !== '0000-00-00 00:00:00') ? $cb : null,
        ];

        $lead->update($data);

        UnlistedLeadActivity::create([
            'UL_LEAD_ACTY_LID'             => $leadId,
            'UL_LEAD_ACTY_UID'             => session('uid'),
            'UL_LEAD_ACTY_TYPE'            => 'Disposition',
            'UL_LEAD_ACTY_DISPOSITION'     => $data['UL_LEAD_DISPOSITION'],
            'UL_LEAD_ACTY_SUB_DISPOSITION' => $data['UL_LEAD_SUB_DISPOSITION'],
            'UL_LEAD_ACTY_COMMENT'         => $data['UL_LEAD_DISPOSITION_COMMENT'],
            'UL_LEAD_ACTY_CALLBACK_TIME'   => $data['UL_LEAD_CALLBACK_TIME'],
            'UL_LEAD_ACTY_TIMESTAMP'       => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function leadActivity(int $leadId)
    {
        if (!$this->canAccessLeads()) abort(403);

        $activities = DB::table('unlisted_leads_activity')
            ->leftJoin('users', 'users.uid', '=', 'unlisted_leads_activity.UL_LEAD_ACTY_UID')
            ->select('unlisted_leads_activity.*', 'users.name as actor_name')
            ->where('UL_LEAD_ACTY_LID', $leadId)
            ->orderByDesc('UL_LEAD_ACTY_TIMESTAMP')
            ->get();

        return view('admin.unlisted.partials.lead-activity', compact('activities', 'leadId'));
    }

    public function clearCallbackRequest(int $leadId)
    {
        if (!$this->canAccessLeads()) return response()->json(['success' => false], 403);

        UnlistedLead::findOrFail($leadId)->update([
            'UL_LEAD_REQUEST_FOR_CALL' => 'No',
            'UL_LEAD_UPDATE_TIME'      => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function investInquiry(Request $request)
    {
        if (!session('uid')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $fincode = (int) $request->input('fincode');
        $type    = $request->input('type');
        $qty     = (int) $request->input('qty', 0);

        $stock = DB::selectOne("
            SELECT s.UL_STOCKS_LOT_SIZE, p.UL_PD_BID_PRICE
            FROM unlisted_stocks s
            LEFT JOIN (
                SELECT pd.UL_PD_FINCODE, pd.UL_PD_BID_PRICE
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
            WHERE s.UL_STOCKS_FINCODE = ? AND s.UL_STOCKS_STATUS = 1
        ", [$fincode]);

        if (!$stock) {
            return response()->json(['success' => false, 'message' => 'Stock not found.'], 404);
        }

        if (in_array($type, ['buy', 'sell'])) {
            $lotSize = (int) $stock->UL_STOCKS_LOT_SIZE ?: 50;
            if ($qty < $lotSize) {
                return response()->json(['success' => false, 'message' => "Minimum quantity is {$lotSize}."], 422);
            }
        }

        $uid  = session('uid');
        $now  = now();
        $lead = UnlistedLead::firstWhere('UL_LEAD_UID', $uid);

        if ($type === 'callback') {
            if ($lead) {
                $lead->update([
                    'UL_LEAD_REQUEST_FOR_CALL' => 'yes',
                    'UL_LEAD_UPDATE_TIME'      => $now,
                ]);
            } else {
                UnlistedLead::create([
                    'UL_LEAD_UID'                        => $uid,
                    'UL_LEAD_TYPE'                       => 'callback',
                    'UL_LEAD_INSERT_TIME'                => $now,
                    'UL_LEAD_UPDATE_TIME'                => $now,
                    'UL_LEAD_CUSTOMER_LAST_VISITED_TIME' => $now,
                    'UL_LEAD_USER_TYPE'                  => session('unlisted_user_type', ''),
                    'UL_LEAD_COMPANY'                    => $request->input('company', ''),
                    'UL_LEAD_LANDING_PAGE'               => $request->headers->get('referer', '/'),
                    'UL_LEAD_REQUEST_FOR_CALL'           => 'yes',
                ]);
            }
        } elseif ($lead) {
            $closedDispositions = ['Rejected', 'Sale Closed'];
            if (in_array($lead->UL_LEAD_DISPOSITION, $closedDispositions)) {
                $lead->update([
                    'UL_LEAD_TYPE'                       => $type,
                    'UL_LEAD_DISPOSITION'                => '',
                    'UL_LEAD_SUB_DISPOSITION'            => '',
                    'UL_LEAD_DISPOSITION_TIME'           => null,
                    'UL_LEAD_DISPOSITION_COMMENT'        => '',
                    'UL_LEAD_CALLBACK_TIME'              => null,
                    'UL_LEAD_ALLOCATED_TO'               => null,
                    'UL_LEAD_CUSTOMER_LAST_VISITED_TIME' => $now,
                    'UL_LEAD_UPDATE_TIME'                => $now,
                ]);
            } else {
                $lead->update([
                    'UL_LEAD_TYPE'                       => $type,
                    'UL_LEAD_CUSTOMER_LAST_VISITED_TIME' => $now,
                    'UL_LEAD_UPDATE_TIME'                => $now,
                ]);
            }
        } else {
            UnlistedLead::create([
                'UL_LEAD_UID'                        => $uid,
                'UL_LEAD_TYPE'                       => $type,
                'UL_LEAD_INSERT_TIME'                => $now,
                'UL_LEAD_UPDATE_TIME'                => $now,
                'UL_LEAD_CUSTOMER_LAST_VISITED_TIME' => $now,
                'UL_LEAD_USER_TYPE'                  => session('unlisted_user_type', ''),
                'UL_LEAD_COMPANY'                    => $request->input('company', ''),
                'UL_LEAD_LANDING_PAGE'               => $request->headers->get('referer', '/'),
                'UL_LEAD_REQUEST_FOR_CALL'           => 'no',
            ]);
        }

        if (in_array($type, ['buy', 'sell'])) {
            $price = (float) $stock->UL_PD_BID_PRICE;
            UnlistedOrder::create([
                'UL_ORD_USER_ID'         => $uid,
                'UL_ORD_FINCODE'         => $fincode,
                'UL_ORD_TYPE'            => $type,
                'UL_ORD_QUANTITY'        => $qty,
                'UL_ORD_PRICE_PER_SHARE' => $price,
                'UL_ORD_AMOUNT'          => $price * $qty,
                'UL_ORD_INSERT_TIME'     => $now,
                'UL_ORD_UPDATE_TIME'     => $now,
            ]);
        }

        return response()->json(['success' => true]);
    }

    private function canAccessLeads(): bool
    {
        return !empty(user_privilege('admin'))
            || !empty(user_privilege('user_master'))
            || !empty(user_privilege('unlisted.leads_allocation'))
            || !empty(user_privilege('unlisted.leads'));
    }

    private function getLeadAgents()
    {
        return User::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(privilege, '$.unlisted.leads')) = 'true'")
            ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(privilege, '$.unlisted.leads_allocation')) = 'true'")
            ->get(['uid', 'name'])
            ->sortBy('name')
            ->values();
    }
}
