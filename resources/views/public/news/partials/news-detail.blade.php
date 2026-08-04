@php
    $tags = $news->unlistedStocks;
@endphp
<div class="news-detail-header">
    <h2 class="news-detail-title">{{ $news->title }}</h2>
    <div class="news-detail-meta">
        <span>{{ $news->published_at?->format('M d, Y') }}</span>
        @if(in_array($news->type, ['pdf', 'one_pager']) && $news->link)
            <span> | <a href="{{ asset($news->link) }}" target="_blank" download><i class="fa fa-download"></i></a></span>
        @endif
    </div>
    @if($tags->count())
    <div class="news-detail-tags">
        @foreach($tags as $tag)
        <a href="{{ route('public.company', $tag->UL_STOCKS_SLUG) }}" target="_blank"><span>{{ $tag->UL_STOCKS_S_NAME ?: $tag->UL_STOCKS_COMPNAME }}</span></a>
        @endforeach
    </div>
    @endif
</div>

<div class="news-detail-body">
    @if($news->type === 'image' && $news->link)
        <div class="news-detail-media">
            <img src="{{ asset($news->link) }}" alt="{{ $news->title }}">
        </div>
    @elseif($news->type === 'video' && $news->link)
        <div class="news-detail-media">
            <iframe width="100%" height="300" src="{{ $news->link }}" title="{{ $news->title }}" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    @endif

    <div class="news-detail-content">
        {!! $news->content !!}
    </div>
</div>
