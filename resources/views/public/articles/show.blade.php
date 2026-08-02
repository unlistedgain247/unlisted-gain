@extends('layout.app')

@section('title', ($article->meta_title ?: $article->title) . ' | UnlistedGain')
@section('meta_description', $article->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($article->summary), 160))
@section('meta_keywords', $article->meta_keywords ?: 'unlisted shares, ' . strtolower($article->title))

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/articles.css') }}?v={{ filemtime(public_path('assets/css/pagecss/articles.css')) }}">
@endpush

@section('subheader')
@include('partials.breadcrumb', ['crumbs' => [
    ['label' => 'Articles', 'url' => url('/articles')],
    ['label' => $article->title],
]])
@endsection

@php
    use App\Models\Article;
    use Illuminate\Support\Str;

    $wordCount  = str_word_count(strip_tags($article->content));
    $readMins   = max(1, (int) ceil($wordCount / 200));
    $authorName = $article->author->name ?? 'UnlistedGain Team';
    $authorInitial = strtoupper(mb_substr($authorName, 0, 1));

    $authorArticleCount = $article->created_by
        ? Article::published()->where('created_by', $article->created_by)->count()
        : 1;

    // "Previous" = published just before this one, "Next" = published just after —
    // pure read-only lookups, no controller/model changes needed for these.
    $prevArticle = Article::published()
        ->where('published_at', '<', $article->published_at)
        ->orderByDesc('published_at')
        ->first();

    $nextArticle = Article::published()
        ->where('published_at', '>', $article->published_at)
        ->orderBy('published_at')
        ->first();

    $relatedArticles = collect();
    if ($article->unlistedStocks->isNotEmpty()) {
        $relatedArticles = Article::published()
            ->where('id', '!=', $article->id)
            ->whereHas('unlistedStocks', function ($q) use ($article) {
                $q->whereIn('unlisted_stocks.UL_STOCKS_FINCODE', $article->unlistedStocks->pluck('UL_STOCKS_FINCODE'));
            })
            ->with('author')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();
    }
    if ($relatedArticles->count() < 3) {
        $excludeIds = $relatedArticles->pluck('id')->push($article->id);
        $relatedArticles = $relatedArticles->concat(
            Article::published()
                ->whereNotIn('id', $excludeIds)
                ->with('author')
                ->orderByDesc('published_at')
                ->limit(3 - $relatedArticles->count())
                ->get()
        );
    }

    $readTime = function ($a) {
        return max(1, (int) ceil(str_word_count(strip_tags($a->content ?? '')) / 200));
    };
@endphp

@section('content')

<div class="reading-progress-bar" id="readingProgressBar"></div>

<main>
    <div class="ug-article-wrap">
        <article class="ug-article-page">

            <div class="article-reading-col">
                <a href="{{ url('/articles') }}" class="article-back-link">
                    <i class="fas fa-arrow-left"></i> Back to Articles
                </a>

                <h1 class="article-title">{{ $article->title }}</h1>

                <div class="article-meta-row">
                    @if($article->author)
                        <a href="{{ route('public.authors.show', [$article->author->uid, \Illuminate\Support\Str::slug($authorName)]) }}" class="article-author" title="View {{ $authorName }}'s author page">
                            <span class="article-author-avatar">{{ $authorInitial }}</span>
                            {{ $authorName }}
                        </a>
                    @else
                        <span class="article-author">
                            <span class="article-author-avatar">{{ $authorInitial }}</span>
                            {{ $authorName }}
                        </span>
                    @endif
                    <span class="article-meta-dot">&bull;</span>
                    @if($article->published_at)
                        <span><i class="fas fa-calendar"></i> {{ $article->published_at->format('d M Y') }}</span>
                        <span class="article-meta-dot">&bull;</span>
                    @endif
                    <span><i class="fas fa-clock"></i> {{ $readMins }} min read</span>
                </div>

                @if($article->unlistedStocks->isNotEmpty())
                    <div class="article-related-stocks article-related-stocks-top">
                        <span class="article-related-label"><i class="fas fa-link"></i> Related:</span>
                        <div class="article-related-stocks-list">
                            @foreach($article->unlistedStocks as $stock)
                                <a href="{{ route('public.company', $stock->UL_STOCKS_SLUG) }}" class="article-related-stock-chip">
                                    {{ $stock->UL_STOCKS_COMPNAME }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($article->author && ($article->author->author_linkedin || $article->author->author_twitter || $article->author->author_facebook || $article->author->author_instagram || $article->author->author_website))
                    <div class="article-actions-row">
                        <div class="article-share-row">
                            <span class="article-share-label">{{ $authorName }} on:</span>
                            @if($article->author->author_linkedin)
                                <a href="{{ $article->author->author_linkedin }}" target="_blank" rel="noopener" class="article-share-btn share-linkedin" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            @endif
                            @if($article->author->author_twitter)
                                <a href="{{ $article->author->author_twitter }}" target="_blank" rel="noopener" class="article-share-btn share-twitter" title="Twitter / X"><i class="fab fa-x-twitter"></i></a>
                            @endif
                            @if($article->author->author_facebook)
                                <a href="{{ $article->author->author_facebook }}" target="_blank" rel="noopener" class="article-share-btn" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                            @endif
                            @if($article->author->author_instagram)
                                <a href="{{ $article->author->author_instagram }}" target="_blank" rel="noopener" class="article-share-btn" title="Instagram"><i class="fab fa-instagram"></i></a>
                            @endif
                            @if($article->author->author_website)
                                <a href="{{ $article->author->author_website }}" target="_blank" rel="noopener" class="article-share-btn" title="Website"><i class="fas fa-globe"></i></a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            @if($article->featured_image)
                <img src="{{ asset($article->featured_image) }}" alt="{{ $article->title }}" class="article-featured-image">
            @endif

            <div class="article-reading-col">
                @if($article->summary)
                    <p class="article-summary">{{ $article->summary }}</p>
                @endif

                <div class="article-content" id="articleContent">
                    {!! $article->content !!}
                </div>

                {{-- Was this helpful? --}}
                <div class="article-helpful" id="articleHelpful">
                    <p>Was this article helpful?</p>
                    <div class="article-helpful-btns">
                        <button type="button" class="article-helpful-btn" data-vote="yes"><i class="fas fa-thumbs-up"></i> Yes</button>
                        <button type="button" class="article-helpful-btn" data-vote="no"><i class="fas fa-thumbs-down"></i> No</button>
                    </div>
                    <div class="article-helpful-msg" id="articleHelpfulMsg" style="display:none;"></div>
                </div>

                {{-- Author bio --}}
                <div class="article-author-card">
                    <div class="article-author-card-avatar">{{ $authorInitial }}</div>
                    <div class="article-author-card-body">
                        <h4>
                            @if($article->author)
                                <a href="{{ route('public.authors.show', [$article->author->uid, \Illuminate\Support\Str::slug($authorName)]) }}">{{ $authorName }}</a>
                            @else
                                {{ $authorName }}
                            @endif
                        </h4>
                        <p>
                            @if($article->author?->author_bio)
                                {{ $article->author->author_bio }}
                            @else
                                Contributor at UnlistedGain, covering unlisted shares, pre-IPO investing and market insights.
                            @endif
                            {{ $authorArticleCount }} {{ Str::plural('article', $authorArticleCount) }} published.
                        </p>
                        @if($article->author && ($article->author->author_linkedin || $article->author->author_twitter || $article->author->author_facebook || $article->author->author_instagram || $article->author->author_website))
                            <div class="article-author-card-socials">
                                @if($article->author->author_linkedin)
                                    <a href="{{ $article->author->author_linkedin }}" target="_blank" rel="noopener" class="article-share-btn share-linkedin" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                                @endif
                                @if($article->author->author_twitter)
                                    <a href="{{ $article->author->author_twitter }}" target="_blank" rel="noopener" class="article-share-btn share-twitter" title="Twitter / X"><i class="fab fa-x-twitter"></i></a>
                                @endif
                                @if($article->author->author_facebook)
                                    <a href="{{ $article->author->author_facebook }}" target="_blank" rel="noopener" class="article-share-btn" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                                @endif
                                @if($article->author->author_instagram)
                                    <a href="{{ $article->author->author_instagram }}" target="_blank" rel="noopener" class="article-share-btn" title="Instagram"><i class="fab fa-instagram"></i></a>
                                @endif
                                @if($article->author->author_website)
                                    <a href="{{ $article->author->author_website }}" target="_blank" rel="noopener" class="article-share-btn" title="Website"><i class="fas fa-globe"></i></a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Previous / Next --}}
                @if($prevArticle || $nextArticle)
                <div class="article-prev-next">
                    @if($prevArticle)
                        <a href="{{ route('public.articles.show', $prevArticle->slug) }}" class="article-prev-next-link prev">
                            <span class="article-prev-next-label"><i class="fas fa-arrow-left"></i> Previous</span>
                            <span class="article-prev-next-title">{{ Str::limit($prevArticle->title, 70) }}</span>
                        </a>
                    @else
                        <span></span>
                    @endif

                    @if($nextArticle)
                        <a href="{{ route('public.articles.show', $nextArticle->slug) }}" class="article-prev-next-link next">
                            <span class="article-prev-next-label">Next <i class="fas fa-arrow-right"></i></span>
                            <span class="article-prev-next-title">{{ Str::limit($nextArticle->title, 70) }}</span>
                        </a>
                    @endif
                </div>
                @endif

                {{-- Related articles --}}
                @if($relatedArticles->isNotEmpty())
                <div class="article-related-articles">
                    <h3><i class="fas fa-newspaper"></i> Related Articles</h3>
                    <div class="article-related-grid">
                        @foreach($relatedArticles as $ra)
                            @php $raAuthor = $ra->author->name ?? 'UnlistedGain Team'; @endphp
                            <a href="{{ route('public.articles.show', $ra->slug) }}" class="ah-card">
                                <div class="ah-card-image">
                                    @if($ra->featured_image)
                                        <img src="{{ asset($ra->featured_image) }}" alt="{{ $ra->title }}" loading="lazy">
                                    @else
                                        <div class="ah-card-image-fallback"><i class="fas fa-newspaper"></i></div>
                                    @endif
                                    <span class="ah-badge ah-badge-read"><i class="fas fa-clock"></i> {{ $readTime($ra) }} min</span>
                                </div>
                                <div class="ah-card-body">
                                    <div class="ah-card-top-row">
                                        <span></span>
                                        <span class="ah-card-date">{{ $ra->published_at?->format('d M Y') }}</span>
                                    </div>
                                    <h3 class="ah-card-title">{{ Str::limit($ra->title, 65) }}</h3>
                                    <div class="ah-card-footer">
                                        <span class="ah-author ah-author-sm">
                                            <span class="ah-author-avatar">{{ strtoupper(mb_substr($raAuthor, 0, 1)) }}</span>
                                            {{ $raAuthor }}
                                        </span>
                                        <span class="ah-readmore">Read <i class="fas fa-arrow-right"></i></span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

        </article>
    </div>
</main>

<script>
(function () {
    // ── Table horizontal-scroll wrapping ──
    document.querySelectorAll('.article-content table').forEach(function (table) {
        if (table.parentElement.classList.contains('table-scroll')) return;
        var wrap = document.createElement('div');
        wrap.className = 'table-scroll';
        table.parentNode.insertBefore(wrap, table);
        wrap.appendChild(table);
    });

    // ── FAQ accordion (works on any .faq-item pasted into article content) ──
    document.querySelectorAll('.faq-question').forEach(function (btn) {
        if (!btn.querySelector('i')) {
            btn.insertAdjacentHTML('beforeend', ' <i class="fas fa-chevron-down"></i>');
        }
        btn.addEventListener('click', function () {
            btn.closest('.faq-item')?.classList.toggle('open');
        });
    });

    // ── Reading progress bar ──
    var progressBar = document.getElementById('readingProgressBar');
    var contentEl = document.getElementById('articleContent');

    function updateProgress() {
        if (!progressBar || !contentEl) return;
        var rect = contentEl.getBoundingClientRect();
        var contentTop = rect.top + window.scrollY;
        var contentHeight = contentEl.offsetHeight;
        var scrolled = window.scrollY - contentTop + window.innerHeight * 0.5;
        var pct = Math.max(0, Math.min(100, (scrolled / contentHeight) * 100));
        progressBar.style.width = pct + '%';
    }
    window.addEventListener('scroll', updateProgress, { passive: true });
    updateProgress();

    // ── Was this helpful? (local feedback only, not persisted server-side) ──
    var helpfulBtns = document.querySelectorAll('.article-helpful-btn');
    var helpfulMsg = document.getElementById('articleHelpfulMsg');
    helpfulBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            helpfulBtns.forEach(function (b) { b.classList.remove('selected'); });
            btn.classList.add('selected');
            if (helpfulMsg) {
                helpfulMsg.textContent = btn.dataset.vote === 'yes'
                    ? 'Thanks for the feedback!'
                    : 'Thanks — we\'ll work on making this better.';
                helpfulMsg.style.display = '';
            }
        });
    });
})();
</script>
@endsection
