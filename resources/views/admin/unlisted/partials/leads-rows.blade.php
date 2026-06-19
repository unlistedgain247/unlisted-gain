{{-- Meta read by JS for count --}}
<div id="leadsMeta" data-total="{{ $leads->total() }}" style="display:none;"></div>

@if($leads->total() === 0)
<div style="text-align:center;padding:60px 20px;">
    <i class="fa-solid fa-user-slash" style="font-size:32px;color:#ddd;margin-bottom:12px;display:block;"></i>
    <div style="font-size:14px;color:#bbb;font-weight:500;">No leads found</div>
    <div style="font-size:12px;color:#ccc;margin-top:4px;">Try adjusting your filters</div>
</div>
@else
<table style="width:100%;border-collapse:collapse;">
    <thead>
        <tr style="background:#f8f9fa;border-bottom:2px solid #e9ecef;">
            <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.06em;width:60px;">UID</th>
            <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.06em;width:220px;">Name</th>
            <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.06em;width:120px;">Last Visited</th>
            <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.06em;width:160px;">Allocation</th>
            <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.06em;width:200px;">Disposition</th>
            <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.06em;width:120px;">Callback</th>
            <th style="padding:10px 14px;text-align:center;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.06em;width:70px;">Call</th>
        </tr>
    </thead>
    <tbody>
    @foreach($leads->items() as $lead)
    @php
        $dispColors = [
            'Interested'  => ['bg' => '#dcfce7', 'color' => '#15803d', 'border' => '#bbf7d0'],
            'Working'     => ['bg' => '#dbeafe', 'color' => '#1d4ed8', 'border' => '#bfdbfe'],
            'Rejected'    => ['bg' => '#fee2e2', 'color' => '#b91c1c', 'border' => '#fecaca'],
            'Sale Closed' => ['bg' => '#d1fae5', 'color' => '#065f46', 'border' => '#a7f3d0'],
            'New Lead'    => ['bg' => '#fef9c3', 'color' => '#854d0e', 'border' => '#fde68a'],
        ];
        $dc = $dispColors[$lead->UL_LEAD_DISPOSITION] ?? ['bg' => '#f3f4f6', 'color' => '#6b7280', 'border' => '#e5e7eb'];
        $leadJson = json_encode($lead, JSON_HEX_APOS | JSON_HEX_TAG);
        $noCallback = empty($lead->UL_LEAD_CALLBACK_TIME) || $lead->UL_LEAD_CALLBACK_TIME === '0000-00-00 00:00:00';
        $isOverdue = !$noCallback && \Carbon\Carbon::parse($lead->UL_LEAD_CALLBACK_TIME)->isPast();
    @endphp
    <tr style="border-bottom:1px solid #f1f3f5;transition:background 0.12s;" onmouseover="this.style.background='#fafbfc'" onmouseout="this.style.background=''"  >

        {{-- UID --}}
        <td style="padding:12px 14px;vertical-align:middle;">
            <span style="font-weight:700;color:#3b82f6;font-size:13px;font-family:monospace;">{{ $lead->UL_LEAD_UID }}</span>
        </td>

        {{-- Name + contact + tags --}}
        <td style="padding:12px 14px;vertical-align:middle;max-width:220px;overflow:hidden;">
            <div style="font-weight:600;font-size:13px;color:#111827;line-height:1.3;">{{ $lead->user_name ?: '—' }}</div>
            @if($lead->email || $lead->phone)
            <div style="font-size:11px;color:#9ca3af;margin-top:2px;line-height:1.3;">
                @if($lead->email)<span>{{ $lead->email }}</span>@endif
                @if($lead->email && $lead->phone)<span style="margin:0 4px;color:#d1d5db;">·</span>@endif
                @if($lead->phone)<span>{{ $lead->phone }}</span>@endif
            </div>
            @endif
            @if($lead->UL_LEAD_USER_TYPE)
            <div style="margin-top:6px;display:flex;flex-wrap:wrap;gap:3px;">
                @foreach(explode(',', $lead->UL_LEAD_USER_TYPE) as $tag)
                @php $tag = trim($tag);
                $ts = match($tag) {
                    'MF'              => 'background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;',
                    'Unlisted'        => 'background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;',
                    'Demat'           => 'background:#fffbeb;color:#d97706;border:1px solid #fde68a;',
                    'Channel Partner' => 'background:#faf5ff;color:#7c3aed;border:1px solid #e9d5ff;',
                    'Startup'         => 'background:#f0fdfa;color:#0f766e;border:1px solid #99f6e4;',
                    default           => 'background:#f9fafb;color:#6b7280;border:1px solid #e5e7eb;',
                }; @endphp
                <span style="display:inline-block;padding:1px 7px;border-radius:10px;font-size:10px;font-weight:600;{{ $ts }}">{{ $tag }}</span>
                @endforeach
            </div>
            @endif
        </td>

        {{-- Last Visited --}}
        <td style="padding:12px 14px;vertical-align:middle;">
            @if($lead->UL_LEAD_CUSTOMER_LAST_VISITED_TIME)
            <div style="font-size:12px;color:#374151;font-weight:500;">
                {{ \Carbon\Carbon::parse($lead->UL_LEAD_CUSTOMER_LAST_VISITED_TIME)->format('d M Y') }}
            </div>
            <div style="font-size:11px;color:#9ca3af;margin-top:1px;">
                {{ \Carbon\Carbon::parse($lead->UL_LEAD_CUSTOMER_LAST_VISITED_TIME)->format('g:i A') }}
            </div>
            @else
            <span style="color:#d1d5db;font-size:13px;">—</span>
            @endif
        </td>

        {{-- Allocation --}}
        <td style="padding:12px 14px;vertical-align:middle;">
            @if($canAllocate)
            <select class="lead-alloc-select" data-lead-id="{{ $lead->UL_LEAD_ID }}"
                style="width:100%;padding:5px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;color:#374151;background:#fff;cursor:pointer;outline:none;">
                <option value="">— Unallocated —</option>
                @foreach($leadAgents as $agent)
                <option value="{{ $agent->uid }}" {{ $lead->UL_LEAD_ALLOCATED_TO == $agent->uid ? 'selected' : '' }}>
                    {{ $agent->name }}
                </option>
                @endforeach
            </select>
            <div class="alloc-label" style="font-size:11px;margin-top:4px;">
                @if($lead->allocated_name)
                <span style="color:#16a34a;"><i class="fa-solid fa-circle-check" style="font-size:10px;"></i> {{ $lead->allocated_name }}</span>
                @else
                <span style="color:#d1d5db;">Not assigned</span>
                @endif
            </div>
            @else
            @if($lead->allocated_name)
            <div style="display:flex;align-items:center;gap:6px;">
                <div style="width:26px;height:26px;border-radius:50%;background:#e0f2fe;color:#0369a1;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">
                    {{ strtoupper(substr($lead->allocated_name, 0, 1)) }}
                </div>
                <span style="font-size:12px;color:#374151;font-weight:500;">{{ $lead->allocated_name }}</span>
            </div>
            @else
            <span style="color:#d1d5db;font-size:13px;">—</span>
            @endif
            @endif
        </td>

        {{-- Disposition --}}
        <td style="padding:12px 14px;vertical-align:middle;">
            <div style="display:flex;align-items:center;gap:5px;">
                @if($lead->UL_LEAD_DISPOSITION)
                <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:{{ $dc['bg'] }};color:{{ $dc['color'] }};border:1px solid {{ $dc['border'] }};">
                    {{ $lead->UL_LEAD_DISPOSITION }}
                </span>
                @else
                <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#f3f4f6;color:#9ca3af;border:1px solid #e5e7eb;">Fresh</span>
                @endif
                <button class="open-activity-drawer" data-lead-id="{{ $lead->UL_LEAD_ID }}" data-lead-name="{{ $lead->user_name ?: $lead->UL_LEAD_UID }}" title="View Activity"
                    style="flex-shrink:0;width:24px;height:24px;border:1px solid #e5e7eb;background:#fff;color:#9ca3af;border-radius:5px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;padding:0;"
                    onmouseover="this.style.borderColor='#6366f1';this.style.color='#6366f1';this.style.background='#eef2ff';"
                    onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#9ca3af';this.style.background='#fff';">
                    <i class="fa-solid fa-clock-rotate-left" style="font-size:9px;"></i>
                </button>
                <button class="disp-edit-btn open-disp-modal" data-lead='{{ $leadJson }}' title="Edit Disposition"
                    style="flex-shrink:0;width:24px;height:24px;border:1px solid #e5e7eb;background:#fff;color:#9ca3af;border-radius:5px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;padding:0;transition:all 0.15s;"
                    onmouseover="this.style.borderColor='#87b942';this.style.color='#87b942';this.style.background='#f0f8e8';"
                    onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#9ca3af';this.style.background='#fff';">
                    <i class="fa-solid fa-pen" style="font-size:9px;"></i>
                </button>
            </div>
            @if($lead->UL_LEAD_SUB_DISPOSITION)
            <div style="font-size:11px;color:#6b7280;margin-top:4px;">{{ $lead->UL_LEAD_SUB_DISPOSITION }}</div>
            @endif
            @if($lead->UL_LEAD_DISPOSITION_COMMENT)
            <div style="font-size:11px;color:#9ca3af;margin-top:2px;font-style:italic;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $lead->UL_LEAD_DISPOSITION_COMMENT }}">
                {{ $lead->UL_LEAD_DISPOSITION_COMMENT }}
            </div>
            @endif
        </td>

        {{-- Callback --}}
        <td style="padding:12px 14px;vertical-align:middle;white-space:nowrap;">
            @if(!$noCallback)
            <div style="font-size:12px;font-weight:600;color:{{ $isOverdue ? '#dc2626' : '#1d4ed8' }};">
                {{ \Carbon\Carbon::parse($lead->UL_LEAD_CALLBACK_TIME)->format('d M Y') }}
            </div>
            <div style="font-size:11px;color:#9ca3af;margin-top:1px;">
                {{ \Carbon\Carbon::parse($lead->UL_LEAD_CALLBACK_TIME)->format('g:i A') }}
                @if($isOverdue)
                <span style="color:#dc2626;font-weight:600;margin-left:3px;">Overdue</span>
                @endif
            </div>
            @else
            <span style="color:#d1d5db;font-size:13px;">—</span>
            @endif
        </td>

        {{-- Req. Call --}}
        <td style="padding:12px 14px;vertical-align:middle;text-align:center;" class="req-call-cell" data-lead-id="{{ $lead->UL_LEAD_ID }}">
            @if(strtolower($lead->UL_LEAD_REQUEST_FOR_CALL ?? '') === 'yes')
            <span class="req-call-badge" title="Click to mark as done"
                style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#fef9c3;color:#a16207;border:1px solid #fde68a;cursor:pointer;transition:all 0.15s;"
                onmouseover="this.style.background='#fde047';this.style.borderColor='#ca8a04';"
                onmouseout="this.style.background='#fef9c3';this.style.borderColor='#fde68a';">
                <i class="fa-solid fa-phone-volume" style="font-size:9px;"></i> Yes
            </span>
            @else
            <span style="color:#d1d5db;font-size:13px;">--</span>
            @endif
        </td>

    </tr>
    @endforeach
    </tbody>
</table>

{{-- Pagination --}}
@if($leads->lastPage() > 1)
<div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-top:1px solid #f1f3f5;background:#fafafa;">
    <span style="font-size:12px;color:#9ca3af;">
        Page <strong style="color:#374151;">{{ $leads->currentPage() }}</strong> of {{ $leads->lastPage() }}
        &nbsp;&middot;&nbsp;
        <strong style="color:#374151;">{{ $leads->total() }}</strong> results
    </span>
    <div style="display:flex;gap:4px;">
        <button onclick="loadLeads({{ $leads->currentPage() - 1 }})" {{ $leads->onFirstPage() ? 'disabled' : '' }}
            style="padding:5px 12px;border:1px solid #e5e7eb;border-radius:6px;background:#fff;font-size:12px;cursor:pointer;color:#374151;transition:all 0.15s;"
            onmouseover="if(!this.disabled)this.style.borderColor='#87b942';" onmouseout="this.style.borderColor='#e5e7eb';">
            ‹ Prev
        </button>
        @php $pgStart = max(1, $leads->currentPage() - 2); $pgEnd = min($leads->lastPage(), $leads->currentPage() + 2); @endphp
        @for($p = $pgStart; $p <= $pgEnd; $p++)
        <button onclick="loadLeads({{ $p }})"
            style="padding:5px 10px;border:1px solid {{ $p === $leads->currentPage() ? '#87b942' : '#e5e7eb' }};border-radius:6px;background:{{ $p === $leads->currentPage() ? '#87b942' : '#fff' }};font-size:12px;cursor:pointer;color:{{ $p === $leads->currentPage() ? '#fff' : '#374151' }};font-weight:{{ $p === $leads->currentPage() ? '600' : '400' }};">
            {{ $p }}
        </button>
        @endfor
        <button onclick="loadLeads({{ $leads->currentPage() + 1 }})" {{ $leads->hasMorePages() ? '' : 'disabled' }}
            style="padding:5px 12px;border:1px solid #e5e7eb;border-radius:6px;background:#fff;font-size:12px;cursor:pointer;color:#374151;transition:all 0.15s;"
            onmouseover="if(!this.disabled)this.style.borderColor='#87b942';" onmouseout="this.style.borderColor='#e5e7eb';">
            Next ›
        </button>
    </div>
</div>
@endif
@endif
