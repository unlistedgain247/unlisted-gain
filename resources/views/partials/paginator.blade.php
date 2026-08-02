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
{{-- Styles live in global.css and admin.css — this partial is included both
     inside full pages (layout.app / layout.admin, both already load one of
     those) and inside AJAX-only fragments with no layout of their own, so an
     inline <style> here can't be replaced with a <link> without risking a
     flash of unstyled content on every paginated AJAX response. --}}

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
