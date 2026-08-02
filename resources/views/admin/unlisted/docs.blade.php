@extends('layout.admin')

@section('title', 'Unlisted Docs')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/unlisted-docs.css') }}?v={{ filemtime(public_path('assets/css/admin/unlisted-docs.css')) }}">
@endpush

@section('content')
@include('partials.admin-unlisted-subnav')

<div class="admin-main">
    <h1 class="admin-page-title">Docs</h1>

    <div class="admin-card" style="padding:0;">

        <div class="doc-filters">
            <div class="filter-group">
                <label>Search</label>
                <input type="text" id="docSearch" placeholder="Company / description">
            </div>
            <div class="filter-group">
                <label>Type</label>
                <select id="docTypeFilter">
                    <option value="">All Types</option>
                    @foreach(\App\Models\UnlistedDocument::DOC_TYPES as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Status</label>
                <select id="docStatusFilter">
                    <option value="">All</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <button type="button" class="doc-add-btn" id="addDocBtn">
                <i class="fa-solid fa-plus"></i> Add Document
            </button>
        </div>

        <div id="docsTableContainer">
            <div class="docs-loading"><i class="fa-solid fa-circle-notch fa-spin"></i></div>
        </div>

    </div>
</div>

@include('admin.unlisted.docs-modal')

@endsection

@push('scripts')
<script>
window.DOCS_BASE = '{{ url("/admin/unlisted/docs") }}';
var DOCS_CSRF = $('meta[name="csrf-token"]').attr('content');
var currentDocsPage = 1;

function loadDocsPage(page) {
    currentDocsPage = page;
    $('#docsTableContainer').html('<div class="docs-loading"><i class="fa-solid fa-circle-notch fa-spin"></i></div>');

    $.get(window.DOCS_BASE + '/data', {
        page:   page,
        q:      $('#docSearch').val(),
        type:   $('#docTypeFilter').val(),
        status: $('#docStatusFilter').val(),
    })
    .done(function (html) { $('#docsTableContainer').html(html); })
    .fail(function ()     { $('#docsTableContainer').html('<div class="docs-loading">Failed to load. Please refresh.</div>'); });
}

$(function () {
    loadDocsPage(1);

    var debounce;
    $('#docSearch').on('input', function () {
        clearTimeout(debounce);
        debounce = setTimeout(function () { loadDocsPage(1); }, 350);
    });
    $('#docTypeFilter, #docStatusFilter').on('change', function () { loadDocsPage(1); });

    // Toggle status
    $(document).on('change', '.doc-toggle', function () {
        var $cb  = $(this);
        var id   = $cb.data('id');
        var $badge = $cb.closest('tr').find('.admin-badge');
        $.ajax({ url: window.DOCS_BASE + '/' + id + '/toggle', method: 'POST', headers: { 'X-CSRF-TOKEN': DOCS_CSRF } })
            .done(function (res) {
                if (res.success) {
                    var active = res.status === '1';
                    $badge.text(active ? 'Active' : 'Inactive')
                          .removeClass('badge-admin badge-locked')
                          .addClass(active ? 'badge-admin' : 'badge-locked');
                } else { $cb.prop('checked', !$cb.prop('checked')); }
            })
            .fail(function () { $cb.prop('checked', !$cb.prop('checked')); });
    });
});
</script>
@endpush
