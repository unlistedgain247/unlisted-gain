@php
    use Illuminate\Support\Str;

    $typeMeta = function ($type) {
        return match ($type) {
            'video'     => ['icon' => 'fa-circle-play', 'label' => 'Video'],
            'image'     => ['icon' => 'fa-image', 'label' => 'Update'],
            'pdf'       => ['icon' => 'fa-file-pdf', 'label' => 'PDF'],
            'one_pager' => ['icon' => 'fa-file-lines', 'label' => 'One Pager'],
            default     => ['icon' => 'fa-newspaper', 'label' => 'Update'],
        };
    };

    $thumb = function ($item) {
        if ($item->type === 'video' && $item->video_preview_image) return asset($item->video_preview_image);
        if ($item->link && $item->type !== 'video') return asset($item->link);
        return null;
    };

    $tagNames = fn ($item) => $item->unlistedStocks->pluck('UL_STOCKS_COMPNAME')->implode(', ');

    $pageItems   = collect($news->items());
    $isFirstPage = $news->currentPage() === 1;
    $featured    = $isFirstPage ? $pageItems->first() : null;
    $gridItems   = $featured ? $pageItems->slice(1) : $pageItems;
@endphp

<div id="newsGridMeta" data-total="{{ $news->total() }}" data-last-page="{{ $news->lastPage() }}" data-page="{{ $news->currentPage() }}" style="display:none;"></div>

@if($pageItems->isEmpty())
<div class="nh-empty">
    <i class="fa-solid fa-newspaper"></i>
    <p>No news found for the selected filters.</p>
</div>
@else

    @if($featured)
    @php $fMeta = $typeMeta($featured->type); $fThumb = $thumb($featured); $fTags = $tagNames($featured); @endphp
    <div class="nh-featured" onclick="showUnlistedNewsModal({{ $featured->id }})">
        <div class="nh-featured-image">
            @if($fThumb)
                <img class="lazy-img" data-src="{{ $fThumb }}" alt="{{ $featured->title }}">
            @else
                <div class="nh-image-fallback"><i class="fa-solid {{ $fMeta['icon'] }}"></i></div>
            @endif
            <span class="nh-badge nh-badge-type"><i class="fa-solid {{ $fMeta['icon'] }}"></i> {{ $fMeta['label'] }}</span>
            <span class="nh-badge nh-badge-time"><i class="fa-regular fa-clock"></i> {{ $featured->published_at?->format('d M Y') }}</span>
        </div>
        <div class="nh-featured-body">
            @if($fTags)
                <span class="nh-related-badge" title="{{ $fTags }}">{{ $fTags }}</span>
            @endif
            <h2 class="nh-featured-title">{{ $featured->title }}</h2>
            @if($featured->short_content)
                <p class="nh-featured-summary">{{ Str::limit($featured->short_content, 180) }}</p>
            @endif
            <span class="nh-cta">Read Full Story <i class="fa-solid fa-arrow-right"></i></span>
        </div>
    </div>
    @endif

    <div class="nh-grid">
        @foreach($gridItems as $item)
        @php $meta = $typeMeta($item->type); $img = $thumb($item); $tags = $tagNames($item); @endphp
        <div class="nh-card" onclick="showUnlistedNewsModal({{ $item->id }})">
            <div class="nh-card-image">
                @if($img)
                    <img class="lazy-img" data-src="{{ $img }}" alt="{{ $item->title }}">
                @else
                    <div class="nh-image-fallback"><i class="fa-solid {{ $meta['icon'] }}"></i></div>
                @endif
                <span class="nh-badge nh-badge-type nh-badge-sm"><i class="fa-solid {{ $meta['icon'] }}"></i> {{ $meta['label'] }}</span>
            </div>
            <div class="nh-card-body">
                <div class="nh-card-top-row">
                    @if($tags)
                        <span class="nh-related-badge nh-related-badge-sm" title="{{ $tags }}">{{ $tags }}</span>
                    @else
                        <span></span>
                    @endif
                    <span class="nh-card-date">{{ $item->published_at?->format('d M Y') }}</span>
                </div>
                <h3 class="nh-card-title">{{ Str::limit($item->title, 80) }}</h3>
                @if($item->short_content)
                    <p class="nh-card-summary">{{ Str::limit($item->short_content, 105) }}</p>
                @endif
                <div class="nh-card-footer">
                    <span class="nh-card-time"><i class="fa-regular fa-clock"></i> {{ $item->published_at?->format('g:i A') }}</span>
                    <span class="nh-readmore">Read more <i class="fa-solid fa-arrow-right"></i></span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($news->lastPage() > 1)
    <div class="nh-pagination-wrap">
        <p class="nh-pagination-info">
            Showing <strong>{{ $news->firstItem() }}–{{ $news->lastItem() }}</strong> of <strong>{{ $news->total() }}</strong> news
        </p>
        <ul class="nh-pagination">
            <li class="nh-page-item {{ $news->onFirstPage() ? 'disabled' : '' }}">
                <button onclick="loadNewsPage({{ $news->currentPage() - 1 }}, true)" {{ $news->onFirstPage() ? 'disabled' : '' }}>‹</button>
            </li>
            @php $pgStart = max(1, $news->currentPage() - 2); $pgEnd = min($news->lastPage(), $news->currentPage() + 2); @endphp
            @for($p = $pgStart; $p <= $pgEnd; $p++)
            <li class="nh-page-item {{ $p === $news->currentPage() ? 'active' : '' }}">
                <button onclick="loadNewsPage({{ $p }}, true)">{{ $p }}</button>
            </li>
            @endfor
            <li class="nh-page-item {{ $news->hasMorePages() ? '' : 'disabled' }}">
                <button onclick="loadNewsPage({{ $news->currentPage() + 1 }}, true)" {{ $news->hasMorePages() ? '' : 'disabled' }}>›</button>
            </li>
        </ul>
    </div>
    @endif
@endif
