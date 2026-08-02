@extends('layout.app')

@section('title', 'Articles & Insights on Unlisted Shares | UnlistedGain')
@section('meta_description', 'Read the latest articles, insights and guides on unlisted shares, pre-IPO investing and building wealth before companies go public.')
@section('meta_keywords', 'unlisted shares articles, pre-ipo insights, unlisted share investing guide')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/articles.css') }}?v={{ filemtime(public_path('assets/css/pagecss/articles.css')) }}">
@endpush

@section('subheader')
@include('partials.breadcrumb', ['crumbs' => [['label' => 'Articles']]])
@endsection

@php
    use Illuminate\Support\Str;

    $readTime = function ($article) {
        $words = str_word_count(strip_tags($article->content ?? ''));
        return max(1, (int) ceil($words / 200));
    };

    $relatedNames = function ($article) {
        return $article->unlistedStocks->pluck('UL_STOCKS_COMPNAME')->implode(', ');
    };

    $pageItems   = collect($articles->items());
    $isFirstPage = $articles->currentPage() === 1;
    $featured    = $isFirstPage ? $pageItems->first() : null;
    $gridItems   = $featured ? $pageItems->slice(1) : $pageItems;
    $latestFive  = $pageItems->take(5);
@endphp

@section('content')
<main>

    {{-- ── HERO ── --}}
    <section class="ah-hero">
        <div class="ah-hero-pattern" aria-hidden="true"></div>
        <div class="ah-hero-inner">
            <span class="ah-hero-eyebrow"><i class="fas fa-bolt"></i> UnlistedGain Knowledge Hub</span>
            <h1 class="ah-hero-title">Unlisted Shares <span>Articles &amp; Market Insights</span></h1>
            <p class="ah-hero-subtitle">Guides, analysis and the latest updates on unlisted shares, pre-IPO investing and building wealth before companies go public.</p>

            <div class="ah-hero-stats">
                <div class="ah-stat">
                    <strong>{{ $articles->total() }}+</strong>
                    <span>Articles</span>
                </div>
                <div class="ah-stat-sep" aria-hidden="true"></div>
                <div class="ah-stat">
                    <strong>Daily</strong>
                    <span>Updated</span>
                </div>
                <div class="ah-stat-sep" aria-hidden="true"></div>
                <div class="ah-stat">
                    <strong>Market</strong>
                    <span>Insights</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ── SEARCH + DATE FILTER ── --}}
    <div class="ah-toolbar-wrap">
        <div class="ah-toolbar">
            <div class="ah-search">
                <i class="fas fa-search"></i>
                <input type="text" id="articleSearch" placeholder="Search articles by title or topic...">
            </div>
            <div class="ah-date-filter">
                <i class="fas fa-calendar-alt"></i>
                <select id="dateFilter">
                    <option value="all">All Time</option>
                    <option value="7">Last 7 Days</option>
                    <option value="30">Last 30 Days</option>
                    <option value="90">Last 3 Months</option>
                    <option value="365">This Year</option>
                </select>
            </div>
        </div>
    </div>

    <div class="ah-layout">
        <div class="ah-main">
          <div id="ahResults">

            @if($pageItems->isEmpty())
                <div class="ah-empty">
                    <i class="fas fa-newspaper"></i>
                    <p>No articles published yet. Check back soon.</p>
                </div>
            @else

                {{-- ── FEATURED ARTICLE ── --}}
                @if($featured)
                    @php
                        $fRelated = $relatedNames($featured);
                        $fAuthor = $featured->author->name ?? 'UnlistedGain Team';
                        $fInitial = strtoupper(mb_substr($fAuthor, 0, 1));
                    @endphp
                    <a href="{{ route('public.articles.show', $featured->slug) }}"
                       class="ah-featured"
                       data-search="{{ strtolower($featured->title . ' ' . $featured->summary) }}"
                       data-date="{{ $featured->published_at?->toDateString() }}">
                        <div class="ah-featured-image">
                            @if($featured->featured_image)
                                <img src="{{ asset($featured->featured_image) }}" alt="{{ $featured->title }}">
                            @else
                                <div class="ah-featured-image-fallback"><i class="fas fa-newspaper"></i></div>
                            @endif
                            <span class="ah-badge ah-badge-featured"><i class="fas fa-star"></i> Featured</span>
                            <span class="ah-badge ah-badge-read"><i class="fas fa-clock"></i> {{ $readTime($featured) }} min read</span>
                        </div>
                        <div class="ah-featured-body">
                            @if($fRelated)
                                <span class="ah-related-badge" title="{{ $fRelated }}">{{ $fRelated }}</span>
                            @endif
                            <h2 class="ah-featured-title">{{ $featured->title }}</h2>
                            @if($featured->summary)
                                <p class="ah-featured-summary">{{ Str::limit($featured->summary, 180) }}</p>
                            @endif
                            <div class="ah-featured-footer">
                                <span class="ah-author">
                                    <span class="ah-author-avatar">{{ $fInitial }}</span>
                                    <span>
                                        <strong>{{ $fAuthor }}</strong>
                                        <em>{{ $featured->published_at?->format('d M Y') }}</em>
                                    </span>
                                </span>
                                <span class="ah-cta">Read Full Article <i class="fas fa-arrow-right"></i></span>
                            </div>
                        </div>
                    </a>
                @endif

                {{-- ── GRID ── --}}
                <div class="ah-grid" id="articlesGrid">
                    @foreach($gridItems as $article)
                        @php
                            $related = $relatedNames($article);
                            $mins = $readTime($article);
                            $authorName = $article->author->name ?? 'UnlistedGain Team';
                            $authorInitial = strtoupper(mb_substr($authorName, 0, 1));
                        @endphp
                        <a href="{{ route('public.articles.show', $article->slug) }}"
                           class="ah-card"
                           data-search="{{ strtolower($article->title . ' ' . $article->summary) }}"
                           data-date="{{ $article->published_at?->toDateString() }}">
                            <div class="ah-card-image">
                                @if($article->featured_image)
                                    <img src="{{ asset($article->featured_image) }}" alt="{{ $article->title }}" loading="lazy">
                                @else
                                    <div class="ah-card-image-fallback"><i class="fas fa-newspaper"></i></div>
                                @endif
                                <span class="ah-badge ah-badge-read"><i class="fas fa-clock"></i> {{ $mins }} min</span>
                            </div>
                            <div class="ah-card-body">
                                <div class="ah-card-top-row">
                                    @if($related)
                                        <span class="ah-related-badge ah-related-badge-sm" title="{{ $related }}">{{ $related }}</span>
                                    @else
                                        <span></span>
                                    @endif
                                    <span class="ah-card-date">{{ $article->published_at?->format('d M Y') }}</span>
                                </div>
                                <h3 class="ah-card-title">{{ $article->title }}</h3>
                                @if($article->summary)
                                    <p class="ah-card-summary">{{ Str::limit($article->summary, 105) }}</p>
                                @endif
                                <div class="ah-card-footer">
                                    <span class="ah-author ah-author-sm">
                                        <span class="ah-author-avatar">{{ $authorInitial }}</span>
                                        {{ $authorName }}
                                    </span>
                                    <span class="ah-readmore">Read more <i class="fas fa-arrow-right"></i></span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <p class="ah-no-results" id="noResultsMsg" style="display:none;">
                    <i class="fas fa-search"></i> No articles match your search.
                </p>

                {{ $articles->links('partials.pagination') }}

            @endif
          </div>
        </div>

        {{-- ── SIDEBAR (desktop only) ── --}}
        <aside class="ah-sidebar">

            @if($latestFive->isNotEmpty())
            <div class="ah-widget">
                <h3 class="ah-widget-title"><i class="fas fa-clock"></i> Latest Articles</h3>
                <ul class="ah-widget-list">
                    @foreach($latestFive as $a)
                        <li>
                            <a href="{{ route('public.articles.show', $a->slug) }}" class="ah-widget-item">
                                <div class="ah-widget-thumb">
                                    @if($a->featured_image)
                                        <img src="{{ asset($a->featured_image) }}" alt="{{ $a->title }}" loading="lazy">
                                    @else
                                        <i class="fas fa-newspaper"></i>
                                    @endif
                                </div>
                                <div class="ah-widget-item-body">
                                    <span class="ah-widget-item-title">{{ Str::limit($a->title, 55) }}</span>
                                    <span class="ah-widget-item-date">{{ $a->published_at?->format('d M Y') }}</span>
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

<script>
(function () {
    const searchInput = document.getElementById('articleSearch');
    const dateFilter   = document.getElementById('dateFilter');
    const ahResults    = document.getElementById('ahResults');

    function applyFilters() {
        const term    = (searchInput?.value || '').trim().toLowerCase();
        const days    = dateFilter && dateFilter.value !== 'all' ? parseInt(dateFilter.value, 10) : null;
        const cutoff  = days ? new Date(Date.now() - days * 24 * 60 * 60 * 1000) : null;
        let visibleCount = 0;

        document.querySelectorAll('#ahResults .ah-card, #ahResults .ah-featured').forEach(function (card) {
            const matchesSearch = term === '' || (card.dataset.search || '').includes(term);

            let matchesDate = true;
            if (cutoff) {
                const cardDate = card.dataset.date ? new Date(card.dataset.date) : null;
                matchesDate = !!cardDate && cardDate >= cutoff;
            }

            const visible = matchesSearch && matchesDate;
            card.style.display = visible ? '' : 'none';
            if (visible) visibleCount++;
        });

        const noResultsMsg = document.getElementById('noResultsMsg');
        if (noResultsMsg) noResultsMsg.style.display = visibleCount === 0 ? '' : 'none';
    }

    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }

    if (dateFilter) {
        dateFilter.addEventListener('change', applyFilters);
    }

    // ── Pagination via fetch, keeping the address bar on /articles ──
    // The real, crawlable ?page=N links stay in the DOM (good for SEO /
    // no-JS fallback); JS just intercepts the click and swaps the results
    // container in-place instead of doing a full navigation.
    document.addEventListener('click', function (e) {
        const link = e.target.closest('#ahResults .ah-pagination-wrap a.page-link');
        if (!link) return;

        const url = link.getAttribute('href');
        if (!url) return;

        e.preventDefault();
        ahResults.classList.add('ah-loading');

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (res) {
                if (!res.ok) throw new Error('Request failed');
                return res.text();
            })
            .then(function (html) {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const fresh = doc.getElementById('ahResults');
                if (fresh) {
                    ahResults.innerHTML = fresh.innerHTML;
                    applyFilters();
                    ahResults.scrollIntoView({ behavior: 'smooth', block: 'start' });
                } else {
                    window.location.href = url;
                }
            })
            .catch(function () {
                window.location.href = url;
            })
            .finally(function () {
                ahResults.classList.remove('ah-loading');
            });
    });
})();
</script>
@endsection
