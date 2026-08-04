{{-- Meta read by JS --}}
<div id="newsMeta" data-total="{{ $news->total() }}" data-last-page="{{ $news->lastPage() }}" style="display:none;"></div>

@if($news->total() === 0)
<div style="text-align:center;padding:60px 20px;">
    <i class="fa-solid fa-newspaper" style="font-size:32px;color:#ddd;margin-bottom:12px;display:block;"></i>
    <div style="font-size:14px;color:#bbb;font-weight:500;">No news found</div>
    <div style="font-size:12px;color:#ccc;margin-top:4px;">Try adjusting your filters</div>
</div>
@else
<div style="overflow-x:auto;">
<table style="width:100%;border-collapse:collapse;min-width:900px;">
    <thead>
        <tr style="background:#f8f9fa;border-bottom:2px solid #e9ecef;">
            @php
                $th = 'padding:10px 12px;text-align:left;font-size:11px;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap;';
            @endphp
            <th style="{{ $th }}width:60px;">ID</th>
            <th style="{{ $th }}min-width:260px;">Title</th>
            <th style="{{ $th }}width:100px;">Type</th>
            <th style="{{ $th }}width:130px;">Published</th>
            <th style="{{ $th }}width:100px;">Status</th>
            <th style="{{ $th }}width:80px;text-align:center;">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($news->items() as $item)
    <tr style="border-bottom:1px solid #f1f3f5;">
        <td style="padding:12px;vertical-align:middle;">
            <span style="font-weight:700;font-size:13px;color:#111827;font-family:monospace;">{{ $item->id }}</span>
        </td>
        <td style="padding:12px;vertical-align:middle;">
            <span style="font-size:13px;font-weight:600;color:#111827;">{{ $item->title }}</span>
        </td>
        <td style="padding:12px;vertical-align:middle;">
            <span class="unn-type-pill">{{ ucfirst(str_replace('_', ' ', $item->type)) }}</span>
        </td>
        <td style="padding:12px;vertical-align:middle;white-space:nowrap;">
            <span style="font-size:12px;color:#374151;">{{ $item->published_at?->format('d-M-Y, h:i A') ?? '—' }}</span>
        </td>
        <td style="padding:12px;vertical-align:middle;">
            <span class="unn-badge {{ $item->status === 'Active' ? 'active' : 'inactive' }}">{{ $item->status }}</span>
        </td>
        <td style="padding:12px;vertical-align:middle;text-align:center;white-space:nowrap;">
            <button class="open-edit-news" data-id="{{ $item->id }}" title="Edit"
                style="width:26px;height:26px;border:1px solid #e5e7eb;background:#fff;color:#9ca3af;border-radius:5px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;padding:0;">
                <i class="fa-solid fa-pen" style="font-size:9px;"></i>
            </button>
            <button class="toggle-news-status" data-id="{{ $item->id }}" data-status="{{ $item->status }}" title="Toggle Active/Inactive"
                style="width:26px;height:26px;border:1px solid #e5e7eb;background:#fff;color:#9ca3af;border-radius:5px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;padding:0;margin-left:3px;">
                <i class="fa-solid fa-power-off" style="font-size:9px;"></i>
            </button>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
</div>

@if($news->lastPage() > 1)
<div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-top:1px solid #f1f3f5;background:#fafafa;">
    <span style="font-size:12px;color:#9ca3af;">
        Page <strong style="color:#374151;">{{ $news->currentPage() }}</strong> of {{ $news->lastPage() }}
        &nbsp;&middot;&nbsp;
        <strong style="color:#374151;">{{ $news->total() }}</strong> results
    </span>
    <div style="display:flex;gap:4px;">
        <button onclick="loadNews({{ $news->currentPage() - 1 }})" {{ $news->onFirstPage() ? 'disabled' : '' }}
            style="padding:5px 12px;border:1px solid #e5e7eb;border-radius:6px;background:#fff;font-size:12px;cursor:pointer;color:#374151;">
            ‹ Prev
        </button>
        @php $pgStart = max(1, $news->currentPage() - 2); $pgEnd = min($news->lastPage(), $news->currentPage() + 2); @endphp
        @for($p = $pgStart; $p <= $pgEnd; $p++)
        <button onclick="loadNews({{ $p }})"
            style="padding:5px 10px;border:1px solid {{ $p === $news->currentPage() ? '#87b942' : '#e5e7eb' }};border-radius:6px;background:{{ $p === $news->currentPage() ? '#87b942' : '#fff' }};font-size:12px;cursor:pointer;color:{{ $p === $news->currentPage() ? '#fff' : '#374151' }};font-weight:{{ $p === $news->currentPage() ? '600' : '400' }};">
            {{ $p }}
        </button>
        @endfor
        <button onclick="loadNews({{ $news->currentPage() + 1 }})" {{ $news->hasMorePages() ? '' : 'disabled' }}
            style="padding:5px 12px;border:1px solid #e5e7eb;border-radius:6px;background:#fff;font-size:12px;cursor:pointer;color:#374151;">
            Next ›
        </button>
    </div>
</div>
@endif
@endif
