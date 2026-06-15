{{-- Meta read by JS --}}
<div id="ordersMeta" data-total="{{ $orders->total() }}" data-last-page="{{ $orders->lastPage() }}" style="display:none;"></div>

@if($orders->total() === 0)
<div style="text-align:center;padding:60px 20px;">
    <i class="fa-solid fa-box-open" style="font-size:32px;color:#ddd;margin-bottom:12px;display:block;"></i>
    <div style="font-size:14px;color:#bbb;font-weight:500;">No orders found</div>
    <div style="font-size:12px;color:#ccc;margin-top:4px;">Try adjusting your filters</div>
</div>
@else
<div style="overflow-x:auto;">
<table style="width:100%;border-collapse:collapse;min-width:1360px;">
    <thead>
        <tr style="background:#f8f9fa;border-bottom:2px solid #e9ecef;">
            @php
                $th = 'padding:10px 12px;text-align:left;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap;';
            @endphp
            <th style="{{ $th }}width:75px;">Order Id</th>
            <th style="{{ $th }}min-width:160px;">Share</th>
            <th style="{{ $th }}min-width:180px;">Customer</th>
            <th style="{{ $th }}width:130px;">Order Date</th>
            <th style="{{ $th }}width:110px;">Dealer</th>
            <th style="{{ $th }}width:75px;">Ord BY</th>
            <th style="{{ $th }}width:100px;">Allocated</th>
            <th style="{{ $th }}width:110px;">Deal Status</th>
            <th style="{{ $th }}width:65px;">Type</th>
            <th style="{{ $th }}width:60px;">Qty</th>
            <th style="{{ $th }}width:85px;">Price</th>
            <th style="{{ $th }}width:90px;">Amount</th>
            <th style="{{ $th }}width:75px;">Comm.</th>
            <th style="{{ $th }}width:75px;">LP</th>
            <th style="{{ $th }}width:90px;text-align:center;">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($orders->items() as $order)
    @php
        $statusColors = [
            'Completed'  => ['bg' => '#d1fae5', 'color' => '#065f46', 'border' => '#a7f3d0'],
            'Pending'    => ['bg' => '#fef9c3', 'color' => '#854d0e', 'border' => '#fde68a'],
            'Processing' => ['bg' => '#dbeafe', 'color' => '#1d4ed8', 'border' => '#bfdbfe'],
            'Cancelled'  => ['bg' => '#fee2e2', 'color' => '#b91c1c', 'border' => '#fecaca'],
        ];
        $sc = $statusColors[$order->UL_ORD_STATUS ?? ''] ?? ['bg' => '#f3f4f6', 'color' => '#6b7280', 'border' => '#e5e7eb'];
        $isBuy = strtolower($order->UL_ORD_TYPE ?? '') === 'buy';
        $orderDate = $order->UL_ORD_INSERT_TIME
            ? \Carbon\Carbon::parse($order->UL_ORD_INSERT_TIME)->format('d-M-Y, h:i A')
            : '—';

        $copyText = 'Order Id : ' . $order->UL_ORD_ID
            . ' Share : ' . ($order->share_name ?? '—')
            . ' Customer : ' . ($order->customer_name ?? '—') . ' (' . ($order->UL_ORD_USER_ID ?? '—') . ')'
            . ' Order Date : ' . $orderDate
            . ' Type : ' . ucfirst($order->UL_ORD_TYPE ?? '—')
            . ' Qty : ' . ($order->UL_ORD_QUANTITY ?? '—')
            . ' Price : ' . ($order->UL_ORD_PRICE_PER_SHARE ?? '—')
            . ' Amount : ' . ($order->UL_ORD_AMOUNT ?? '—');
    @endphp
    <tr style="border-bottom:1px solid #f1f3f5;transition:background 0.12s;"
        onmouseover="this.style.background='#fafbfc'" onmouseout="this.style.background=''">

        {{-- Order Id --}}
        <td style="padding:12px 12px;vertical-align:middle;">
            <span style="font-weight:700;font-size:13px;color:#111827;font-family:monospace;">{{ $order->UL_ORD_ID }}</span>
        </td>

        {{-- Share --}}
        <td style="padding:12px 12px;vertical-align:middle;">
            @if($order->share_name)
            <span style="font-size:12px;font-weight:600;color:#3b82f6;">{{ $order->share_name }}</span>
            @else
            <span style="color:#d1d5db;font-size:13px;">—</span>
            @endif
        </td>

        {{-- Customer --}}
        <td style="padding:12px 12px;vertical-align:middle;">
            <div style="display:flex;align-items:center;gap:5px;flex-wrap:wrap;">
                @if($order->customer_name)
                <span style="font-size:12px;font-weight:600;color:#111827;">{{ $order->customer_name }}</span>
                @endif
                @if($order->UL_ORD_USER_ID)
                <span style="font-size:11px;color:#6b7280;">({{ $order->UL_ORD_USER_ID }})</span>
                @endif
                <button class="copy-order-btn" data-copy="{{ e($copyText) }}" title="Copy order details"
                    style="background:none;border:none;padding:2px 4px;cursor:pointer;color:#9ca3af;border-radius:3px;line-height:1;flex-shrink:0;transition:color 0.12s;"
                    onmouseover="this.style.color='#374151';" onmouseout="this.style.color='#9ca3af';">
                    <i class="fa-regular fa-copy"></i>
                </button>
            </div>
        </td>

        {{-- Order Date --}}
        <td style="padding:12px 12px;vertical-align:middle;white-space:nowrap;">
            @if($order->UL_ORD_INSERT_TIME)
            <div style="font-size:12px;color:#374151;font-weight:500;">
                {{ \Carbon\Carbon::parse($order->UL_ORD_INSERT_TIME)->format('d-M-Y') }}
            </div>
            <div style="font-size:11px;color:#9ca3af;margin-top:1px;">
                {{ \Carbon\Carbon::parse($order->UL_ORD_INSERT_TIME)->format('h:i A') }}
            </div>
            @else
            <span style="color:#d1d5db;">—</span>
            @endif
        </td>

        {{-- Dealer --}}
        <td style="padding:12px 12px;vertical-align:middle;">
            @if($order->dealer_name)
            <span style="font-size:12px;color:#374151;">{{ $order->dealer_name }}</span>
            @else
            <span style="color:#d1d5db;font-size:13px;">—</span>
            @endif
        </td>

        {{-- Ord BY --}}
        <td style="padding:12px 12px;vertical-align:middle;">
            @if($order->UL_ORD_USER_ID)
            <span style="font-size:12px;font-weight:600;color:#374151;font-family:monospace;">{{ $order->UL_ORD_USER_ID }}</span>
            @else
            <span style="color:#d1d5db;">—</span>
            @endif
        </td>

        {{-- Allocated --}}
        <td style="padding:12px 12px;vertical-align:middle;">
            <span style="color:#d1d5db;font-size:13px;">—</span>
        </td>

        {{-- Deal Status --}}
        <td style="padding:12px 12px;vertical-align:middle;">
            @if($order->UL_ORD_STATUS)
            <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;
                background:{{ $sc['bg'] }};color:{{ $sc['color'] }};border:1px solid {{ $sc['border'] }};">
                {{ $order->UL_ORD_STATUS }}
            </span>
            @if($order->UL_ORD_SUB_STATUS)
            <div style="font-size:10px;color:#9ca3af;margin-top:3px;">{{ $order->UL_ORD_SUB_STATUS }}</div>
            @endif
            @else
            <span style="color:#d1d5db;font-size:13px;">—</span>
            @endif
        </td>

        {{-- Type --}}
        <td style="padding:12px 12px;vertical-align:middle;">
            @if($order->UL_ORD_TYPE)
            <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;
                background:{{ $isBuy ? '#d1fae5' : '#fee2e2' }};
                color:{{ $isBuy ? '#065f46' : '#b91c1c' }};
                border:1px solid {{ $isBuy ? '#a7f3d0' : '#fecaca' }};">
                {{ ucfirst($order->UL_ORD_TYPE) }}
            </span>
            @else
            <span style="color:#d1d5db;">—</span>
            @endif
        </td>

        {{-- Qty --}}
        <td style="padding:12px 12px;vertical-align:middle;text-align:right;">
            <span style="font-size:13px;color:#374151;font-weight:600;">
                {{ $order->UL_ORD_QUANTITY !== null ? number_format($order->UL_ORD_QUANTITY) : '—' }}
            </span>
        </td>

        {{-- Price --}}
        <td style="padding:12px 12px;vertical-align:middle;text-align:right;">
            <span style="font-size:13px;color:#374151;">
                {{ $order->UL_ORD_PRICE_PER_SHARE !== null ? number_format($order->UL_ORD_PRICE_PER_SHARE, 0) : '—' }}
            </span>
        </td>

        {{-- Amount --}}
        <td style="padding:12px 12px;vertical-align:middle;text-align:right;">
            <span style="font-size:13px;font-weight:600;color:#111827;">
                {{ $order->UL_ORD_AMOUNT !== null ? number_format($order->UL_ORD_AMOUNT, 0) : '—' }}
            </span>
        </td>

        {{-- Comm. --}}
        <td style="padding:12px 12px;vertical-align:middle;text-align:right;">
            <span style="font-size:12px;color:#6b7280;">
                {{ $order->UL_ORD_INTERMEDIARY_COMMISSION !== null ? number_format($order->UL_ORD_INTERMEDIARY_COMMISSION, 0) : '—' }}
            </span>
        </td>

        {{-- LP --}}
        <td style="padding:12px 12px;vertical-align:middle;text-align:center;">
            @if($order->UL_ORD_LP !== null)
            <span style="display:inline-block;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:700;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;">
                {{ number_format($order->UL_ORD_LP, 0) }}
            </span>
            @else
            <span style="color:#d1d5db;font-size:13px;">—</span>
            @endif
        </td>

        {{-- Action --}}
        <td style="padding:12px 12px;vertical-align:middle;text-align:center;white-space:nowrap;">
            @php $orderJson = json_encode($order, JSON_HEX_APOS | JSON_HEX_TAG); @endphp
            <button class="open-edit-ord"
                data-order='{{ $orderJson }}'
                title="Edit Order"
                style="width:26px;height:26px;border:1px solid #e5e7eb;background:#fff;color:#9ca3af;border-radius:5px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;padding:0;transition:all 0.15s;"
                onmouseover="this.style.borderColor='#87b942';this.style.color='#87b942';this.style.background='#f0f8e8';"
                onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#9ca3af';this.style.background='#fff';">
                <i class="fa-solid fa-pen" style="font-size:9px;"></i>
            </button>
            <button class="open-quick-status" data-order='{{ $orderJson }}' title="Quick Status Update"
                style="width:26px;height:26px;border:1px solid #e5e7eb;background:#fff;color:#9ca3af;border-radius:5px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;padding:0;transition:all 0.15s;margin-left:3px;"
                onmouseover="this.style.borderColor='#6366f1';this.style.color='#6366f1';this.style.background='#eef2ff';"
                onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#9ca3af';this.style.background='#fff';">
                <i class="fa-solid fa-list" style="font-size:9px;"></i>
            </button>
        </td>

    </tr>
    @endforeach
    </tbody>
</table>
</div>

{{-- Pagination --}}
@if($orders->lastPage() > 1)
<div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-top:1px solid #f1f3f5;background:#fafafa;">
    <span style="font-size:12px;color:#9ca3af;">
        Page <strong style="color:#374151;">{{ $orders->currentPage() }}</strong> of {{ $orders->lastPage() }}
        &nbsp;&middot;&nbsp;
        <strong style="color:#374151;">{{ $orders->total() }}</strong> results
    </span>
    <div style="display:flex;gap:4px;">
        <button onclick="loadOrders({{ $orders->currentPage() - 1 }})" {{ $orders->onFirstPage() ? 'disabled' : '' }}
            style="padding:5px 12px;border:1px solid #e5e7eb;border-radius:6px;background:#fff;font-size:12px;cursor:pointer;color:#374151;transition:all 0.15s;"
            onmouseover="if(!this.disabled)this.style.borderColor='#87b942';" onmouseout="this.style.borderColor='#e5e7eb';">
            ‹ Prev
        </button>
        @php $pgStart = max(1, $orders->currentPage() - 2); $pgEnd = min($orders->lastPage(), $orders->currentPage() + 2); @endphp
        @for($p = $pgStart; $p <= $pgEnd; $p++)
        <button onclick="loadOrders({{ $p }})"
            style="padding:5px 10px;border:1px solid {{ $p === $orders->currentPage() ? '#87b942' : '#e5e7eb' }};border-radius:6px;background:{{ $p === $orders->currentPage() ? '#87b942' : '#fff' }};font-size:12px;cursor:pointer;color:{{ $p === $orders->currentPage() ? '#fff' : '#374151' }};font-weight:{{ $p === $orders->currentPage() ? '600' : '400' }};">
            {{ $p }}
        </button>
        @endfor
        <button onclick="loadOrders({{ $orders->currentPage() + 1 }})" {{ $orders->hasMorePages() ? '' : 'disabled' }}
            style="padding:5px 12px;border:1px solid #e5e7eb;border-radius:6px;background:#fff;font-size:12px;cursor:pointer;color:#374151;transition:all 0.15s;"
            onmouseover="if(!this.disabled)this.style.borderColor='#87b942';" onmouseout="this.style.borderColor='#e5e7eb';">
            Next ›
        </button>
    </div>
</div>
@endif
@endif
