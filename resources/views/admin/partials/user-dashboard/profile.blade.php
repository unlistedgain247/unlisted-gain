{{-- Hidden balance for JS to pick up --}}
<span id="udmBalanceData" data-balance="{{ $balance }}" style="display:none;"></span>

<table style="width:100%;border-collapse:collapse;font-size:12px;">
    {{-- Name / Phone / Email --}}
    <tr style="border-bottom:1px solid #f0f0f0;">
        <td style="padding:10px 14px;font-weight:600;color:#6b7280;width:180px;white-space:nowrap;background:#fafafa;">Name / Phone No. / Email</td>
        <td style="padding:10px 14px;">
            <span style="font-weight:700;font-size:13px;color:#111;">{{ $user->name }}</span>
        </td>
        <td style="padding:10px 14px;">
            <span style="color:#111;">{{ $user->phone ?: '—' }}</span>
            <i class="fa-solid fa-circle-check" style="color:#16a34a;font-size:10px;margin-left:4px;" title="Verified"></i>
        </td>
        <td style="padding:10px 14px;">
            <span style="color:#111;">{{ $user->email ?: '—' }}</span>
            <i class="fa-solid fa-circle-check" style="color:#16a34a;font-size:10px;margin-left:4px;" title="Verified"></i>
        </td>
    </tr>

    {{-- Landing Page --}}
    <tr style="border-bottom:1px solid #f0f0f0;">
        <td style="padding:10px 14px;font-weight:600;color:#6b7280;background:#fafafa;">Landing Page</td>
        <td colspan="3" style="padding:10px 14px;word-break:break-all;color:#2b80b9;">
            {{ $lead->UL_LEAD_LANDING_PAGE ?? '—' }}
        </td>
    </tr>

    {{-- CallBack Time --}}
    <tr style="border-bottom:1px solid #f0f0f0;">
        <td style="padding:10px 14px;font-weight:600;color:#6b7280;background:#fafafa;">CallBack Time</td>
        <td colspan="3" style="padding:10px 14px;">
            @if(!empty($lead->UL_LEAD_CALLBACK_TIME) && $lead->UL_LEAD_CALLBACK_TIME !== '0000-00-00 00:00:00')
                {{ \Carbon\Carbon::parse($lead->UL_LEAD_CALLBACK_TIME)->format('d M Y, h:i A') }}
            @else
                <span style="color:#ccc;">—</span>
            @endif
        </td>
    </tr>

    {{-- User Interested In --}}
    <tr style="border-bottom:1px solid #f0f0f0;">
        <td style="padding:10px 14px;font-weight:600;color:#6b7280;background:#fafafa;">User Interested In</td>
        <td style="padding:10px 14px;">
            @if(!empty($lead->UL_LEAD_USER_TYPE))
                @foreach(explode(',', $lead->UL_LEAD_USER_TYPE) as $tag)
                    @php $tag = trim($tag); @endphp
                    <span style="display:inline-block;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600;background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;margin-right:4px;">{{ $tag }}</span>
                @endforeach
            @else
                <span style="color:#ccc;">—</span>
            @endif
        </td>
        <td colspan="2" style="padding:10px 14px;color:#6b7280;">
            {{ $lead->UL_LEAD_COMPANY ?? '' }}
        </td>
    </tr>

    {{-- Completed Orders --}}
    <tr style="border-bottom:1px solid #f0f0f0;">
        <td style="padding:10px 14px;font-weight:600;color:#6b7280;background:#fafafa;">Completed Orders</td>
        <td colspan="3" style="padding:10px 14px;">
            @if($ordersResult && $ordersResult->cnt > 0)
                <span style="font-weight:600;">₹{{ number_format($ordersResult->total, 2) }}</span>
                <span style="color:#6b7280;margin-left:6px;">({{ $ordersResult->cnt }} Orders)</span>
            @else
                <span style="color:#ccc;">No completed orders</span>
            @endif
        </td>
    </tr>

    {{-- First Visited --}}
    <tr style="border-bottom:1px solid #f0f0f0;">
        <td style="padding:10px 14px;font-weight:600;color:#6b7280;background:#fafafa;">First Visited</td>
        <td colspan="3" style="padding:10px 14px;">
            @if(!empty($lead->UL_LEAD_INSERT_TIME))
                {{ \Carbon\Carbon::parse($lead->UL_LEAD_INSERT_TIME)->format('Y-m-d H:i:s') }}
            @else
                <span style="color:#ccc;">—</span>
            @endif
        </td>
    </tr>

    {{-- SC-RM --}}
    <tr style="border-bottom:1px solid #f0f0f0;">
        <td style="padding:10px 14px;font-weight:600;color:#6b7280;background:#fafafa;">SC-RM</td>
        <td colspan="3" style="padding:10px 14px;">
            @if($allocatedName)
                <span style="color:#111;">{{ $allocatedName }}</span>
                <span style="color:#9ca3af;margin-left:4px;">({{ $lead->UL_LEAD_ALLOCATED_TO }})</span>
            @else
                <span style="color:#9ca3af;">(0)</span>
            @endif
        </td>
    </tr>

    {{-- Last Visited --}}
    <tr style="border-bottom:1px solid #f0f0f0;">
        <td style="padding:10px 14px;font-weight:600;color:#6b7280;background:#fafafa;">Last Visited</td>
        <td colspan="3" style="padding:10px 14px;">
            @if(!empty($lead->UL_LEAD_CUSTOMER_LAST_VISITED_TIME) && $lead->UL_LEAD_CUSTOMER_LAST_VISITED_TIME !== '0000-00-00 00:00:00')
                {{ \Carbon\Carbon::parse($lead->UL_LEAD_CUSTOMER_LAST_VISITED_TIME)->format('Y-m-d H:i:s') }}
                <i class="fa-regular fa-eye" style="color:#9ca3af;margin-left:6px;font-size:11px;"></i>
            @else
                <span style="color:#ccc;">—</span>
            @endif
        </td>
    </tr>

    {{-- Disposition --}}
    <tr style="border-bottom:1px solid #f0f0f0;">
        <td style="padding:10px 14px;font-weight:600;color:#6b7280;background:#fafafa;">Disposition</td>
        <td colspan="3" style="padding:10px 14px;">
            @if(!empty($lead->UL_LEAD_DISPOSITION))
                <span style="display:inline-block;padding:2px 10px;border-radius:10px;font-size:11px;font-weight:600;background:#dbeafe;color:#1d4ed8;border:1px solid #bfdbfe;">
                    {{ $lead->UL_LEAD_DISPOSITION }}
                </span>
                @if(!empty($lead->UL_LEAD_SUB_DISPOSITION))
                    <span style="color:#6b7280;font-size:11px;margin-left:6px;">{{ $lead->UL_LEAD_SUB_DISPOSITION }}</span>
                @endif
            @else
                <span style="color:#9ca3af;">- -</span>
            @endif
            <i class="fa-solid fa-plus" style="color:#2b80b9;margin-left:8px;cursor:pointer;font-size:11px;"></i>
        </td>
    </tr>

    {{-- Sub Disposition --}}
    <tr style="border-bottom:1px solid #f0f0f0;">
        <td style="padding:10px 14px;font-weight:600;color:#6b7280;background:#fafafa;">Sub Disposition</td>
        <td colspan="3" style="padding:10px 14px;color:#111;">
            {{ $lead->UL_LEAD_SUB_DISPOSITION ?? '—' }}
        </td>
    </tr>

    {{-- Comment --}}
    <tr style="border-bottom:1px solid #f0f0f0;">
        <td style="padding:10px 14px;font-weight:600;color:#6b7280;background:#fafafa;">Comment</td>
        <td colspan="3" style="padding:10px 14px;">
            @if(!empty($lead->UL_LEAD_DISPOSITION_COMMENT))
                <span style="color:#374151;">{{ $lead->UL_LEAD_DISPOSITION_COMMENT }}</span>
            @else
                <span style="color:#d1d5db;">—</span>
            @endif
            <i class="fa-solid fa-pen" style="color:#9ca3af;margin-left:8px;font-size:11px;cursor:pointer;"></i>
        </td>
    </tr>

    {{-- Stocks Interested --}}
    <tr style="border-bottom:1px solid #f0f0f0;">
        <td style="padding:10px 14px;font-weight:600;color:#6b7280;background:#fafafa;">Stocks Interested</td>
        <td colspan="3" style="padding:10px 14px;">
            <span style="color:#d1d5db;">—</span>
            <i class="fa-solid fa-pen" style="color:#9ca3af;margin-left:8px;font-size:11px;cursor:pointer;"></i>
        </td>
    </tr>

    {{-- Amount Potential --}}
    <tr>
        <td style="padding:10px 14px;font-weight:600;color:#6b7280;background:#fafafa;">Amount Potential</td>
        <td colspan="3" style="padding:10px 14px;">
            <span style="color:#d1d5db;">—</span>
            <i class="fa-solid fa-pen" style="color:#9ca3af;margin-left:8px;font-size:11px;cursor:pointer;"></i>
        </td>
    </tr>
</table>
