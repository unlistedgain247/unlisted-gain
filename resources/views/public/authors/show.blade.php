@extends('layout.app')

@section('title', $author->name . ' — Author at UnlistedGain')
@section('meta_description', $author->author_bio
    ? \Illuminate\Support\Str::limit($author->author_bio, 155)
    : $author->name . ' is a contributor at UnlistedGain, writing about unlisted shares and pre-IPO investing.')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/articles.css') }}?v={{ filemtime(public_path('assets/css/pagecss/articles.css')) }}">
<link rel="stylesheet" href="{{ asset('assets/css/pagecss/author.css') }}?v={{ filemtime(public_path('assets/css/pagecss/author.css')) }}">
@endpush

@section('subheader')
@include('partials.breadcrumb', ['crumbs' => [
    ['label' => 'Articles', 'url' => url('/articles')],
    ['label' => $author->name],
]])
@endsection

@php
    $initial  = strtoupper(mb_substr($author->name, 0, 1));
    $readTime = function ($a) {
        return max(1, (int) ceil(str_word_count(strip_tags($a->content ?? '')) / 200));
    };
    $hasSocials = $author->author_linkedin || $author->author_twitter || $author->author_facebook || $author->author_instagram || $author->author_website;

    $authorCms       = $author->privilege['cms'] ?? [];
    $authorIsWriter  = !empty($authorCms['author']);
    $authorIsReviewer = !empty($authorCms['reviewer']);
    $authorRoleLabel = $authorIsWriter && $authorIsReviewer
        ? 'Author & Reviewer'
        : ($authorIsReviewer ? 'Reviewer' : 'Author');
@endphp

@section('content')
<main>

    <section class="author-hero">
        <div class="author-hero-pattern" aria-hidden="true"></div>
        <div class="author-hero-inner">
            <div class="author-hero-avatar-ring">
                <div class="author-hero-avatar">
                    <img src="{{ route('profile.avatar', $author->uid) }}" alt="{{ $author->name }}" class="author-hero-avatar-dp" onerror="this.style.display='none'">
                    <span class="author-hero-avatar-initial">{{ $initial }}</span>
                </div>
            </div>
            <div class="author-hero-body">
                <span class="author-hero-eyebrow"><i class="fas fa-feather-pointed"></i> {{ $authorRoleLabel }} at UnlistedGain</span>
                <h1 class="author-hero-name">{{ $author->name }}</h1>
                <p class="author-hero-bio">
                    {{ $author->author_bio ?: 'Contributor at UnlistedGain, covering unlisted shares, pre-IPO investing and market insights.' }}
                </p>

                <div class="author-hero-badge-row">
                    <span class="author-hero-badge author-hero-badge-solid">
                        <i class="fas fa-circle-check"></i> Verified {{ $authorRoleLabel }}
                    </span>
                    <span class="author-hero-badge">
                        <i class="fas fa-newspaper"></i> {{ $articles->total() }} {{ \Illuminate\Support\Str::plural('article', $articles->total()) }} published
                    </span>

                    @if($hasSocials)
                        <span class="author-hero-socials">
                            @if($author->author_linkedin)
                                <a href="{{ $author->author_linkedin }}" target="_blank" rel="noopener" class="article-share-btn share-linkedin" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            @endif
                            @if($author->author_twitter)
                                <a href="{{ $author->author_twitter }}" target="_blank" rel="noopener" class="article-share-btn share-twitter" title="Twitter / X"><i class="fab fa-x-twitter"></i></a>
                            @endif
                            @if($author->author_facebook)
                                <a href="{{ $author->author_facebook }}" target="_blank" rel="noopener" class="article-share-btn" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                            @endif
                            @if($author->author_instagram)
                                <a href="{{ $author->author_instagram }}" target="_blank" rel="noopener" class="article-share-btn" title="Instagram"><i class="fab fa-instagram"></i></a>
                            @endif
                            @if($author->author_website)
                                <a href="{{ $author->author_website }}" target="_blank" rel="noopener" class="article-share-btn" title="Website"><i class="fas fa-globe"></i></a>
                            @endif
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <svg class="author-hero-wave" viewBox="0 0 1440 60" preserveAspectRatio="none" aria-hidden="true">
            <path d="M0,32 C240,60 480,60 720,40 C960,20 1200,4 1440,24 L1440,60 L0,60 Z" fill="#fff"></path>
        </svg>
    </section>

    <div class="author-articles-wrap">
        <div class="author-articles-header">
            <span class="author-articles-eyebrow"><i class="fas fa-layer-group"></i> Author Archive</span>
            <h2 class="author-articles-title">Articles by {{ $author->name }}</h2>
        </div>

        @if($articles->isEmpty())
            <p class="ah-no-results"><i class="fas fa-newspaper"></i> No published articles yet.</p>
        @else
            <div class="ah-grid">
                @foreach($articles as $article)
                    @php
                        $related = $article->unlistedStocks->pluck('UL_STOCKS_COMPNAME')->implode(', ');
                        $mins = $readTime($article);
                    @endphp
                    <a href="{{ route('public.articles.show', $article->slug) }}" class="ah-card">
                        <div class="ah-card-image">
                            @if($article->featured_image)
                                <img class="lazy-img" data-src="{{ asset($article->featured_image) }}" alt="{{ $article->title }}">
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
                                <p class="ah-card-summary">{{ \Illuminate\Support\Str::limit($article->summary, 105) }}</p>
                            @endif
                            <div class="ah-card-footer">
                                <span></span>
                                <span class="ah-readmore">Read more <i class="fas fa-arrow-right"></i></span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{ $articles->links('partials.pagination') }}
        @endif
    </div>
</main>
@endsection
