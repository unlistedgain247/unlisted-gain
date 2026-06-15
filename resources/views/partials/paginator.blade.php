{{--
    Reusable AJAX paginator.
    Usage:
        @include('partials.paginator', [
            'total'       => $collection->total(),
            'perPage'     => $collection->perPage(),
            'currentPage' => $collection->currentPage(),
            'callback'    => 'myJsFunction',   // called with (pageNumber)
        ])
--}}

@php
    $lastPage = $perPage > 0 ? (int) ceil($total / $perPage) : 1;
    $cur      = (int) $currentPage;

    if ($lastPage <= 9) {
        $range = range(1, $lastPage);
    } else {
        $raw  = array_unique(array_merge([1], range(max(1, $cur - 2), min($lastPage, $cur + 2)), [$lastPage]));
        sort($raw);
        $range = [];
        $prev  = 0;
        foreach ($raw as $p) {
            if ($p - $prev > 1) $range[] = '...';
            $range[] = $p;
            $prev    = $p;
        }
    }
@endphp

@if ($lastPage > 1)
@once
<style>
.pagi-wrap{display:flex;align-items:center;justify-content:center;gap:4px;padding:12px 22px;border-top:1px solid #e2e8f0;background:#fafafa;flex-shrink:0;flex-wrap:wrap}
.pagi-btn{min-width:32px;height:32px;padding:0 9px;border:1.5px solid #e2e8f0;background:#fff;border-radius:7px;font-size:13px;font-weight:500;color:#475569;cursor:pointer;transition:all .15s;line-height:1}
.pagi-btn:hover:not(:disabled){background:#f1f5f9;border-color:#87b942;color:#87b942}
.pagi-btn.pagi-active{background:#87b942;border-color:#87b942;color:#fff;font-weight:700}
.pagi-btn:disabled{opacity:.4;cursor:not-allowed}
.pagi-ellipsis{padding:0 4px;color:#94a3b8;font-size:14px;line-height:32px}
</style>
@endonce

<div class="pagi-wrap">

    <button @class(['pagi-btn']) data-cb="{{ $callback }}" data-page="{{ $cur - 1 }}"
            @disabled($cur <= 1)>«</button>

    @foreach ($range as $p)
        @if ($p === '...')
            <span class="pagi-ellipsis">…</span>
        @else
            <button @class(['pagi-btn', 'pagi-active' => $p == $cur])
                    data-cb="{{ $callback }}" data-page="{{ $p }}">{{ $p }}</button>
        @endif
    @endforeach

    <button @class(['pagi-btn']) data-cb="{{ $callback }}" data-page="{{ $cur + 1 }}"
            @disabled($cur >= $lastPage)>»</button>

</div>
@endif
