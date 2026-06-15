@if($activities->isEmpty())
<div style="text-align:center;padding:48px 20px;">
    <i class="fa-solid fa-clock-rotate-left" style="font-size:28px;color:#e5e7eb;display:block;margin-bottom:10px;"></i>
    <div style="font-size:13px;color:#9ca3af;font-weight:500;">No activity yet</div>
</div>
@else
<div style="padding:16px 0;">
    @foreach($activities as $act)
    @php
        $ts  = $act->UL_LEAD_ACTY_TIMESTAMP
               ? \Carbon\Carbon::parse($act->UL_LEAD_ACTY_TIMESTAMP)
               : null;
        $type = $act->UL_LEAD_ACTY_TYPE ?? '';

        $iconMap = [
            'Disposition' => ['icon' => 'fa-tag',          'bg' => '#eff6ff', 'color' => '#3b82f6'],
            'Allocation'  => ['icon' => 'fa-user-check',   'bg' => '#f0fdf4', 'color' => '#16a34a'],
            'Deallocated' => ['icon' => 'fa-user-minus',   'bg' => '#fff7ed', 'color' => '#ea580c'],
            'Created'     => ['icon' => 'fa-plus-circle',  'bg' => '#f0fdf4', 'color' => '#15803d'],
            'Updated'     => ['icon' => 'fa-pen-to-square','bg' => '#fafafa', 'color' => '#6b7280'],
        ];
        $ic = $iconMap[$type] ?? ['icon' => 'fa-circle-dot', 'bg' => '#f3f4f6', 'color' => '#9ca3af'];

        $dispColors = [
            'Interested'  => ['bg' => '#dcfce7', 'color' => '#15803d', 'border' => '#bbf7d0'],
            'Working'     => ['bg' => '#dbeafe', 'color' => '#1d4ed8', 'border' => '#bfdbfe'],
            'Rejected'    => ['bg' => '#fee2e2', 'color' => '#b91c1c', 'border' => '#fecaca'],
            'Sale Closed' => ['bg' => '#d1fae5', 'color' => '#065f46', 'border' => '#a7f3d0'],
        ];
        $dc = $dispColors[$act->UL_LEAD_ACTY_DISPOSITION ?? ''] ?? null;

        $noCallback = empty($act->UL_LEAD_ACTY_CALLBACK_TIME)
                   || $act->UL_LEAD_ACTY_CALLBACK_TIME === '0000-00-00 00:00:00';
    @endphp

    <div style="display:flex;gap:12px;padding:12px 20px;border-bottom:1px solid #f3f4f6;">

        {{-- Icon --}}
        <div style="flex-shrink:0;width:32px;height:32px;border-radius:50%;background:{{ $ic['bg'] }};color:{{ $ic['color'] }};display:flex;align-items:center;justify-content:center;margin-top:2px;">
            <i class="fa-solid {{ $ic['icon'] }}" style="font-size:12px;"></i>
        </div>

        {{-- Body --}}
        <div style="flex:1;min-width:0;">

            {{-- Type + time --}}
            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                <span style="font-size:12px;font-weight:700;color:#111827;">{{ $type }}</span>
                @if($ts)
                <span style="font-size:11px;color:#9ca3af;white-space:nowrap;" title="{{ $ts->format('d M Y, g:i A') }}">
                    {{ $ts->diffForHumans() }}
                </span>
                @endif
            </div>

            {{-- Disposition badge + sub --}}
            @if($type === 'Disposition')
            <div style="margin-top:5px;display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                @if($dc)
                <span style="display:inline-block;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:600;background:{{ $dc['bg'] }};color:{{ $dc['color'] }};border:1px solid {{ $dc['border'] }};">
                    {{ $act->UL_LEAD_ACTY_DISPOSITION }}
                </span>
                @elseif($act->UL_LEAD_ACTY_DISPOSITION)
                <span style="display:inline-block;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#f3f4f6;color:#6b7280;border:1px solid #e5e7eb;">
                    {{ $act->UL_LEAD_ACTY_DISPOSITION }}
                </span>
                @else
                <span style="display:inline-block;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#f3f4f6;color:#9ca3af;border:1px solid #e5e7eb;">Fresh</span>
                @endif

                @if($act->UL_LEAD_ACTY_SUB_DISPOSITION)
                <span style="font-size:11px;color:#6b7280;">
                    <i class="fa-solid fa-chevron-right" style="font-size:8px;color:#d1d5db;"></i>
                    {{ $act->UL_LEAD_ACTY_SUB_DISPOSITION }}
                </span>
                @endif
            </div>
            @endif

            {{-- Comment --}}
            @if($act->UL_LEAD_ACTY_COMMENT)
            <div style="margin-top:5px;font-size:12px;color:#4b5563;background:#f9fafb;border-left:3px solid #e5e7eb;padding:5px 10px;border-radius:0 5px 5px 0;line-height:1.5;">
                {{ $act->UL_LEAD_ACTY_COMMENT }}
            </div>
            @endif

            {{-- Callback time --}}
            @if(!$noCallback)
            <div style="margin-top:5px;font-size:11px;color:#6b7280;display:flex;align-items:center;gap:4px;">
                <i class="fa-solid fa-clock" style="font-size:9px;color:#9ca3af;"></i>
                Callback: {{ \Carbon\Carbon::parse($act->UL_LEAD_ACTY_CALLBACK_TIME)->format('d M Y, g:i A') }}
            </div>
            @endif

            {{-- Actor --}}
            <div style="margin-top:5px;font-size:11px;color:#9ca3af;display:flex;align-items:center;gap:4px;">
                <i class="fa-solid fa-user" style="font-size:9px;"></i>
                {{ $act->actor_name ?? ('UID #' . $act->UL_LEAD_ACTY_UID) }}
                @if($ts)
                &nbsp;·&nbsp; {{ $ts->format('d M Y, g:i A') }}
                @endif
            </div>

        </div>
    </div>
    @endforeach
</div>
@endif
