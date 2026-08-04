@extends('layout.app')

@section('title', 'Unlisted Stock Market News | UnlistedGain')
@section('meta_description', 'Latest news, updates and announcements on unlisted and pre-IPO companies in India.')
@section('meta_keywords', 'unlisted stock news, pre-ipo news, unlisted shares updates')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/news.css') }}?v={{ filemtime(public_path('assets/css/pagecss/news.css')) }}">
@endpush

@section('subheader')
@include('partials.breadcrumb', ['crumbs' => [['label' => 'News']]])
@endsection

@section('content')
<main>

    {{-- ── HERO ── --}}
    <section class="nh-hero">
        <div class="nh-hero-pattern" aria-hidden="true"></div>
        <div class="nh-hero-inner">
            <span class="nh-hero-eyebrow"><i class="fa-solid fa-bolt"></i> UnlistedGain Newsroom</span>
            <h1 class="nh-hero-title">Unlisted Stock Market <span>News &amp; Updates</span></h1>
            <p class="nh-hero-subtitle">The latest announcements, price moves and company updates on unlisted and pre-IPO shares in India.</p>

            <div class="nh-hero-stats">
                <div class="nh-stat">
                    <strong>{{ $totalCount }}+</strong>
                    <span>News Items</span>
                </div>
                <div class="nh-stat-sep" aria-hidden="true"></div>
                <div class="nh-stat">
                    <strong>Daily</strong>
                    <span>Updated</span>
                </div>
                <div class="nh-stat-sep" aria-hidden="true"></div>
                <div class="nh-stat">
                    <strong>Market</strong>
                    <span>Insights</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ── TOOLBAR ── --}}
    <div class="nh-toolbar-wrap">
        <div class="nh-toolbar">
            <div class="nh-search" style="position:relative;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="newsStockSearch" placeholder="Search stocks & news topics..." autocomplete="off">
                <input type="hidden" id="newsStockFincode">
                <div id="newsStockDropdown" class="nh-search-dropdown"></div>
            </div>
            <div class="nh-date-filter">
                <i class="fa-solid fa-calendar-days"></i>
                <input type="date" id="newsStartDate" title="From date">
            </div>
            <div class="nh-date-filter">
                <i class="fa-solid fa-calendar-days"></i>
                <input type="date" id="newsEndDate" title="To date">
            </div>
            <label class="nh-video-toggle">
                <input type="checkbox" id="newsVideoOnly"> <i class="fa-solid fa-circle-play"></i> Video Only
            </label>
        </div>
    </div>

    <div class="nh-layout">
        <div class="nh-main">
            <div id="newsGridWrap">
                <div class="nh-loading"><i class="fa-solid fa-spinner fa-spin"></i></div>
            </div>
        </div>

        {{-- ── SIDEBAR ── --}}
        <aside class="nh-sidebar">
            @if($latestFive->isNotEmpty())
            <div class="nh-widget">
                <h3 class="nh-widget-title"><i class="fa-solid fa-clock"></i> Latest News</h3>
                <ul class="nh-widget-list">
                    @foreach($latestFive as $a)
                        <li>
                            <a href="javascript:void(0);" onclick="showUnlistedNewsModal({{ $a->id }})" class="nh-widget-item">
                                <div class="nh-widget-thumb">
                                    @if($a->type !== 'video' && $a->link)
                                        <img src="{{ asset($a->link) }}" alt="{{ $a->title }}" loading="lazy">
                                    @else
                                        <i class="fa-solid fa-newspaper"></i>
                                    @endif
                                </div>
                                <div class="nh-widget-item-body">
                                    <span class="nh-widget-item-title">{{ \Illuminate\Support\Str::limit($a->title, 55) }}</span>
                                    <span class="nh-widget-item-date">{{ $a->published_at?->format('d M Y') }}</span>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </aside>
    </div>
</main>

{{-- ── News Detail Modal ─────────────────────────────────────── --}}
<div id="newsDetailOverlay" class="news-detail-overlay">
    <div class="news-detail-modal">
        <button type="button" class="news-detail-close" id="newsDetailClose">&times;</button>
        <div id="newsDetailBody" class="news-detail-modal-body"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var NEWS_DATA_URL   = '{{ url("/unlisted-shares/news/data") }}';
var NEWS_SHOW_URL   = '{{ url("/unlisted-shares/news") }}';
var NEWS_SEARCH_URL = '{{ url("/unlisted-shares/news/search/stocks") }}';
var newsPage = 1;
var newsGridWrap = document.getElementById('newsGridWrap');

function getNewsFilterData(page) {
    // Note: #newsStockSearch is a typeahead selector for the `fincode` filter,
    // not a free-text search box — its display text must never also be sent
    // as a `search` term, or it silently ANDs an unrelated title/content
    // match against the fincode filter and hides every real result.
    return {
        page: page || 1,
        fincode: $('#newsStockFincode').val(),
        start_date: $('#newsStartDate').val(),
        end_date: $('#newsEndDate').val(),
        video_only: $('#newsVideoOnly').is(':checked') ? 1 : 0,
    };
}

function loadNewsPage(page, scrollToTop) {
    newsPage = page || 1;
    $(newsGridWrap).addClass('nh-loading-state');
    $.get(NEWS_DATA_URL, getNewsFilterData(newsPage)).done(function (html) {
        $('#newsGridWrap').html(html);
        $(newsGridWrap).removeClass('nh-loading-state');
        // Only scroll on an explicit user action (pagination/filter change) —
        // not on the page's very first automatic load, which would otherwise
        // yank the viewport straight past the hero and toolbar.
        if (scrollToTop) newsGridWrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
}

function showUnlistedNewsModal(id) {
    $.get(NEWS_SHOW_URL + '/' + id).done(function (html) {
        $('#newsDetailBody').html(html);
        $('#newsDetailOverlay').addClass('open');
    });
}

$('#newsDetailClose').on('click', function () { $('#newsDetailOverlay').removeClass('open'); });
$('#newsDetailOverlay').on('click', function (e) {
    if ($(e.target).is('#newsDetailOverlay')) $(this).removeClass('open');
});

// Stock typeahead
var newsStockSearchTimer = null;
$('#newsStockSearch').on('input', function () {
    var term = $(this).val().trim();
    $('#newsStockFincode').val('');
    clearTimeout(newsStockSearchTimer);
    if (term.length < 1) { $('#newsStockDropdown').hide().empty(); return; }

    newsStockSearchTimer = setTimeout(function () {
        $.get(NEWS_SEARCH_URL, { q: term }).done(function (stocks) {
            var $dd = $('#newsStockDropdown').empty();
            if (!stocks.length) { $dd.hide(); return; }
            stocks.forEach(function (s) {
                $('<div>').text(s.name).attr('data-fincode', s.fincode)
                    .on('click', function () {
                        $('#newsStockSearch').val(s.name);
                        $('#newsStockFincode').val(s.fincode);
                        $dd.hide();
                        loadNewsPage(1);
                    }).appendTo($dd);
            });
            $dd.show();
        });
    }, 250);
});

$(document).on('click', function (e) {
    if (!$(e.target).closest('#newsStockSearch, #newsStockDropdown').length) $('#newsStockDropdown').hide();
});

$('#newsStartDate, #newsEndDate, #newsVideoOnly').on('change', function () { loadNewsPage(1); });

$(function () {
    loadNewsPage(1);

    @if(request()->integer('nid') > 0)
    showUnlistedNewsModal({{ request()->integer('nid') }});
    @endif
});
</script>
@endpush
