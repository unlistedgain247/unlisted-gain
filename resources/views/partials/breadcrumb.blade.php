<nav class="ug-breadcrumb" aria-label="Breadcrumb">
    <div class="ug-bc-inner">
        <a href="{{ url('/') }}" class="bc-link">Home</a>
        @foreach($crumbs ?? [] as $crumb)
            <span class="bc-sep"><i class="fas fa-chevron-right"></i></span>
            @if(isset($crumb['url']))
                <a href="{{ $crumb['url'] }}" class="bc-link">{{ $crumb['label'] }}</a>
            @else
                <span class="bc-current">{{ $crumb['label'] }}</span>
            @endif
        @endforeach
    </div>
</nav>
