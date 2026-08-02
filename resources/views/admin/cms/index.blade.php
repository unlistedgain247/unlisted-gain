@extends('layout.admin')

@section('title', 'Articles | CMS | Admin | UnlistedGain')

@section('content')
<div class="admin-main">

    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <h1 class="admin-page-title">Articles</h1>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('admin.cms.profile.edit') }}" class="cms-action-btn">
                <i class="fa-solid fa-id-badge"></i> My Author Profile
            </a>
            <a href="{{ route('admin.cms.articles.create') }}" class="cms-new-btn">
                <i class="fa-solid fa-plus"></i> New Article
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="cms-flash-success">{{ session('success') }}</div>
    @endif

    <div class="admin-card">

        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
            <div class="cms-status-tabs">
                @foreach(['' => 'All', 'draft' => 'Draft', 'published' => 'Published', 'trash' => 'Trash'] as $key => $label)
                    <a href="{{ route('admin.cms.articles', array_filter(['status' => $key, 'search' => $search ?? ''])) }}"
                       class="cms-status-tab {{ ($status ?? '') === $key ? 'active' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.cms.articles') }}" style="display:flex;gap:6px;">
                <input type="hidden" name="status" value="{{ $status ?? '' }}">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search by title..." class="cms-search-input">
                <button type="submit" class="cms-search-btn"><i class="fa-solid fa-search"></i></button>
            </form>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $article)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ $article->title }}</strong>
                            @if($article->status === 'published')
                                <br><a href="{{ route('public.articles.show', $article->slug) }}" target="_blank" style="font-size:12px;color:#2196f3;">
                                    View live <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:10px;"></i>
                                </a>
                            @endif
                        </td>
                        <td>{{ $article->author->name ?? '—' }}</td>
                        <td>
                            @if($article->trashed())
                                <span class="admin-badge badge-locked">Trashed</span>
                            @elseif($article->status === 'published')
                                <span class="admin-badge" style="background:#e8f5e9;color:#2e7d32;">Published</span>
                            @else
                                <span class="admin-badge" style="background:#fff3e0;color:#e65100;">Draft</span>
                            @endif
                        </td>
                        <td>{{ $article->updated_at?->format('d M Y, h:i A') }}</td>
                        <td style="display:flex;gap:6px;flex-wrap:wrap;">
                            @if(!$article->trashed())
                                <a href="{{ route('admin.cms.articles.edit', $article->id) }}" class="cms-action-btn">Edit</a>
                                <button type="button" class="cms-action-btn cms-toggle-btn" data-id="{{ $article->id }}">
                                    {{ $article->status === 'published' ? 'Unpublish' : 'Publish' }}
                                </button>
                                <button type="button" class="cms-action-btn cms-trash-btn" data-id="{{ $article->id }}">Trash</button>
                            @else
                                <button type="button" class="cms-action-btn cms-restore-btn" data-id="{{ $article->id }}">Restore</button>
                                <button type="button" class="cms-action-btn cms-danger-btn" data-id="{{ $article->id }}">Delete Forever</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;color:#aaa;padding:32px">No articles found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($articles, 'links'))
            <div style="margin-top:16px">{{ $articles->links() }}</div>
        @endif
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/cms-index.css') }}?v={{ filemtime(public_path('assets/css/admin/cms-index.css')) }}">
@endpush
@endsection

@push('scripts')
<script>
$(function () {
    const CSRF = $('meta[name="csrf-token"]').attr('content');
    const BASE = '{{ url("/admin/cms/articles") }}';

    $(document).on('click', '.cms-toggle-btn', function () {
        const id = $(this).data('id');
        $.ajax({
            url: BASE + '/' + id + '/publish-toggle',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF },
        }).done(function () { location.reload(); })
          .fail(function () { alert('Something went wrong.'); });
    });

    $(document).on('click', '.cms-trash-btn', function () {
        if (!confirm('Move this article to trash?')) return;
        const id = $(this).data('id');
        $.ajax({
            url: BASE + '/' + id,
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF },
        }).done(function () { location.reload(); })
          .fail(function () { alert('Something went wrong.'); });
    });

    $(document).on('click', '.cms-restore-btn', function () {
        const id = $(this).data('id');
        $.ajax({
            url: BASE + '/' + id + '/restore',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF },
        }).done(function () { location.reload(); })
          .fail(function () { alert('Something went wrong.'); });
    });

    $(document).on('click', '.cms-danger-btn', function () {
        if (!confirm('Permanently delete this article? This cannot be undone.')) return;
        const id = $(this).data('id');
        $.ajax({
            url: BASE + '/' + id + '/force',
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF },
        }).done(function () { location.reload(); })
          .fail(function () { alert('Something went wrong.'); });
    });
});
</script>
@endpush
