@extends('layout.admin')

@section('title', 'News | Admin | UnlistedGain')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/unlisted-orders.css') }}?v={{ filemtime(public_path('assets/css/admin/unlisted-orders.css')) }}">
<link rel="stylesheet" href="{{ asset('assets/css/admin/unlisted-news.css') }}?v={{ filemtime(public_path('assets/css/admin/unlisted-news.css')) }}">
@endpush

@section('content')

@include('partials.admin-unlisted-subnav')

<div class="admin-main">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <h1 class="admin-page-title" style="margin:0;">
            News
            <span id="newsCount" style="font-size:14px;font-weight:400;color:#aaa;margin-left:8px;"></span>
        </h1>
        <button type="button" id="openAddNewsBtn" style="padding:9px 18px;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;border:none;background:#87b942;color:#fff;">
            <i class="fa-solid fa-plus"></i> Add News
        </button>
    </div>

    <div class="admin-card" style="padding:0;">

        <div class="unn-filters">
            <div class="filter-group">
                <label>Search</label>
                <input type="text" id="fSearch" placeholder="Title" style="min-width:220px;">
            </div>
            <div class="filter-group">
                <label>Type</label>
                <select id="fType">
                    <option value="">All</option>
                    <option value="text">Text</option>
                    <option value="image">Image</option>
                    <option value="video">Video</option>
                    <option value="pdf">PDF</option>
                    <option value="one_pager">One Pager</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Status</label>
                <select id="fStatus">
                    <option value="">All</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="filter-group" style="justify-content:flex-end;">
                <label>&nbsp;</label>
                <div style="display:flex;gap:6px;">
                    <button class="filter-btn" onclick="loadNews(1)" style="background:#87b942;color:#fff;">
                        <i class="fa-solid fa-magnifying-glass"></i> Search
                    </button>
                    <button class="filter-btn" onclick="resetNewsFilters()" style="background:#f5f5f5;color:#555;border:1px solid #e0e0e0;">
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <div id="newsTableWrap">
            <div class="unn-loading"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>
        </div>

    </div>
</div>

{{-- ── Add/Edit News Modal ───────────────────────────────────── --}}
<div id="unnOverlay">
    <div id="unnModal">
        <div class="unn-header">
            <div class="unn-title" id="unnTitle">Add News</div>
            <button class="unn-close" id="unnClose">&times;</button>
        </div>
        <div class="unn-body">

            <div id="unnMsg"></div>

            <div class="unn-grid-2">
                <div class="unn-field">
                    <label>Title</label>
                    <input type="text" id="unnFieldTitle" placeholder="News title">
                </div>
                <div class="unn-field">
                    <label>Type</label>
                    <select id="unnFieldType">
                        <option value="">Select</option>
                        <option value="text">Text</option>
                        <option value="image">Image</option>
                        <option value="video">Video</option>
                        <option value="pdf">PDF</option>
                        <option value="one_pager">One Pager</option>
                    </select>
                </div>
            </div>

            <div class="unn-grid-2" id="unnMediaRow" style="display:none;">
                <div class="unn-field">
                    <label id="unnMediaLabel">Upload File</label>
                    <input type="file" id="unnFieldMedia">
                    <div class="unn-media-preview" id="unnMediaPreview"></div>
                </div>
            </div>

            <div class="unn-grid-2" id="unnVideoRow" style="display:none;">
                <div class="unn-field">
                    <label>Video Link (YouTube embed URL)</label>
                    <input type="text" id="unnFieldVideoLink" placeholder="https://www.youtube.com/embed/...">
                </div>
                <div class="unn-field">
                    <label>Video Preview Image</label>
                    <input type="file" id="unnFieldVideoPreview" accept="image/*">
                    <div class="unn-media-preview" id="unnVideoPreviewPreview"></div>
                </div>
            </div>

            <div class="unn-grid-3">
                <div class="unn-field">
                    <label>Status</label>
                    <select id="unnFieldStatus">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                <div class="unn-field">
                    <label>Published Date</label>
                    <input type="date" id="unnFieldDate">
                </div>
                <div class="unn-field">
                    <label>&nbsp;</label>
                    <div style="display:flex;gap:6px;">
                        <select id="unnFieldHh" style="width:50%;">
                            <option value="">Hr</option>
                            @for($i = 0; $i < 24; $i++)
                            <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                        <select id="unnFieldMm" style="width:50%;">
                            <option value="">Min</option>
                            @for($i = 0; $i < 60; $i += 5)
                            <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <div class="unn-field" style="margin-bottom:14px;">
                <label>Tagged Companies</label>
                <div class="unn-tag-picker">
                    <div class="unn-tag-chips" id="unnTagChips"></div>
                    <input type="text" id="unnTagSearchInput" placeholder="Search stock by name...">
                    <ul id="unnTagSuggestions" class="unn-tag-suggestions"></ul>
                </div>
            </div>

            <div class="unn-field" style="margin-bottom:14px;">
                <label>Short Content</label>
                <textarea id="unnFieldShortContent" rows="3" placeholder="Teaser shown on the news card"></textarea>
            </div>

            <div class="unn-field">
                <label>Full Content</label>
                <textarea id="unnFieldContent"></textarea>
            </div>

        </div>
        <div class="unn-footer">
            <button id="unnSubmitBtn">Submit</button>
            <button id="unnCancelBtn">Cancel</button>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/tinymce_6.1.2/tinymce.min.js') }}"></script>
<script>
var NEWS_DATA_URL     = '{{ url("/admin/unlisted/news/data") }}';
var NEWS_STORE_URL    = '{{ url("/admin/unlisted/news") }}';
var NEWS_SHOW_URL     = '{{ url("/admin/unlisted/news") }}';
var NEWS_STATUS_URL   = '{{ url("/admin/unlisted/news") }}';
var NEWS_UPLOAD_URL   = '{{ url("/admin/unlisted/news/upload-image") }}';
var NEWS_SEARCH_URL   = '{{ url("/admin/unlisted/news/search/stocks") }}';
var CSRF              = $('meta[name="csrf-token"]').attr('content');
var currentPage       = 1;
var unnEditId         = null;

function getNewsFilters() {
    return {
        search: $('#fSearch').val().trim(),
        type:   $('#fType').val(),
        status: $('#fStatus').val(),
        page:   currentPage,
    };
}

function resetNewsFilters() {
    $('#fSearch').val('');
    $('#fType, #fStatus').val('');
    loadNews(1);
}

function loadNews(page) {
    currentPage = page || 1;
    $('#newsTableWrap').html('<div class="unn-loading"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>');

    $.get(NEWS_DATA_URL, getNewsFilters())
        .done(function (html) {
            $('#newsTableWrap').html(html);
            var $meta = $('#newsTableWrap #newsMeta');
            var total = $meta.data('total');
            var lastPage = $meta.data('last-page');
            if (total !== undefined) {
                $('#newsCount').text(total + ' results found' + (lastPage > 1 ? ' · Showing page ' + currentPage + ' of ' + lastPage : ''));
            }
        })
        .fail(function () {
            $('#newsTableWrap').html('<div class="unn-loading" style="color:#e53935;">Failed to load news.</div>');
        });
}

$('#fSearch').on('keydown', function (e) { if (e.key === 'Enter') loadNews(1); });

// ── TinyMCE ──────────────────────────────────────────────────
function unn_image_upload_handler(blobInfo) {
    return new Promise(function (resolve, reject) {
        var formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());
        formData.append('_token', CSRF);

        $.ajax({
            url: NEWS_UPLOAD_URL,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
        }).done(function (res) {
            if (res.location) resolve(res.location);
            else reject('Upload failed: no location returned.');
        }).fail(function (xhr) {
            reject((xhr.responseJSON && xhr.responseJSON.message) || 'Image upload failed.');
        });
    });
}

function initUnnEditor() {
    tinymce.init({
        selector: '#unnFieldContent',
        plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
        menubar: 'file edit view insert format tools table help',
        toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen preview save print | insertfile image media link anchor codesample | ltr rtl',
        height: 350,
        image_caption: true,
        quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
        toolbar_mode: 'sliding',
        contextmenu: 'link image table',
        convert_urls: true,
        document_base_url: window.location.origin + '/',
        images_upload_handler: unn_image_upload_handler,
        automatic_uploads: true,
        branding: false,
        promotion: false,
    });
}

// ── Type-conditional fields ─────────────────────────────────
function unnTypeChanged() {
    var type = $('#unnFieldType').val();
    $('#unnMediaRow, #unnVideoRow').hide();

    if (type === 'video') {
        $('#unnVideoRow').show();
    } else if (type === 'image' || type === 'pdf' || type === 'one_pager') {
        $('#unnMediaRow').show();
        $('#unnFieldMedia').attr('accept', type === 'image' ? 'image/*' : 'application/pdf');
        $('#unnMediaLabel').text(type === 'image' ? 'Upload Image' : 'Upload PDF');
    }
}
$('#unnFieldType').on('change', unnTypeChanged);

// ── Tag picker ───────────────────────────────────────────────
var unnTagSearchTimer = null;
$('#unnTagSearchInput').on('input', function () {
    var term = $(this).val().trim();
    clearTimeout(unnTagSearchTimer);
    if (term.length < 2) { $('#unnTagSuggestions').hide().empty(); return; }

    unnTagSearchTimer = setTimeout(function () {
        $.get(NEWS_SEARCH_URL, { term: term }).done(function (stocks) {
            var existing = $('#unnTagChips [data-fincode]').map(function () { return String($(this).data('fincode')); }).get();
            var $list = $('#unnTagSuggestions').empty();
            stocks.forEach(function (s) {
                if (existing.indexOf(String(s.fincode)) !== -1) return;
                $('<li>').text(s.name).attr('data-fincode', s.fincode).attr('data-name', s.name).appendTo($list);
            });
            $list.toggle($list.children().length > 0);
        });
    }, 250);
});

$(document).on('click', '#unnTagSuggestions li', function () {
    var fincode = $(this).data('fincode');
    var name = $(this).data('name');
    var chip = $('<span>').addClass('unn-tag-chip').attr('data-fincode', fincode)
        .html($('<div>').text(name).html() + ' <input type="hidden" name="stock_tickers[]" value="' + fincode + '"> <i class="fa-solid fa-xmark unn-tag-remove"></i>');
    $('#unnTagChips').append(chip);
    $('#unnTagSearchInput').val('');
    $('#unnTagSuggestions').hide().empty();
});

$(document).on('click', '.unn-tag-remove', function () {
    $(this).closest('.unn-tag-chip').remove();
});

$(document).on('click', function (e) {
    if (!$(e.target).closest('.unn-tag-picker').length) $('#unnTagSuggestions').hide();
});

// ── Add / Edit modal ─────────────────────────────────────────
function resetUnnForm() {
    unnEditId = null;
    $('#unnMsg').removeClass('has-msg success error').empty();
    $('#unnFieldTitle, #unnFieldVideoLink, #unnFieldDate').val('');
    $('#unnFieldType, #unnFieldHh, #unnFieldMm').val('');
    $('#unnFieldStatus').val('Active');
    $('#unnFieldMedia, #unnFieldVideoPreview').val('');
    $('#unnMediaPreview, #unnVideoPreviewPreview').empty();
    $('#unnFieldShortContent').val('');
    $('#unnTagChips').empty();
    $('#unnMediaRow, #unnVideoRow').hide();
    if (tinymce.get('unnFieldContent')) tinymce.get('unnFieldContent').setContent('');
    $('#unnSubmitBtn').prop('disabled', false).text('Submit');
}

$('#openAddNewsBtn').on('click', function () {
    resetUnnForm();
    $('#unnTitle').text('Add News');
    $('#unnOverlay').addClass('open');
});

$(document).on('click', '.open-edit-news', function () {
    var id = $(this).data('id');
    resetUnnForm();
    unnEditId = id;
    $('#unnTitle').text('Edit News');
    $('#unnOverlay').addClass('open');

    $.get(NEWS_SHOW_URL + '/' + id).done(function (res) {
        $('#unnFieldTitle').val(res.title);
        $('#unnFieldType').val(res.type).trigger('change');
        $('#unnFieldStatus').val(res.status);
        $('#unnFieldDate').val(res.published_at);
        $('#unnFieldHh').val(parseInt(res.published_hh, 10));
        $('#unnFieldMm').val(parseInt(res.published_mm, 10));
        $('#unnFieldShortContent').val(res.short_content);
        if (tinymce.get('unnFieldContent')) tinymce.get('unnFieldContent').setContent(res.content || '');

        if (res.type === 'video') {
            $('#unnFieldVideoLink').val(res.link);
            if (res.video_preview_image) {
                $('#unnVideoPreviewPreview').html('<a href="' + res.video_preview_image + '" target="_blank">Current preview image</a>');
            }
        } else if (res.link) {
            $('#unnMediaPreview').html('<a href="' + res.link + '" target="_blank">Current file</a>');
        }

        (res.stocks || []).forEach(function (s) {
            var chip = $('<span>').addClass('unn-tag-chip').attr('data-fincode', s.UL_STOCKS_FINCODE)
                .html($('<div>').text(s.UL_STOCKS_COMPNAME).html() + ' <input type="hidden" name="stock_tickers[]" value="' + s.UL_STOCKS_FINCODE + '"> <i class="fa-solid fa-xmark unn-tag-remove"></i>');
            $('#unnTagChips').append(chip);
        });
    });
});

function closeUnnModal() {
    $('#unnOverlay').removeClass('open');
}
$('#unnClose, #unnCancelBtn').on('click', closeUnnModal);
$('#unnOverlay').on('click', function (e) {
    if ($(e.target).is('#unnOverlay')) closeUnnModal();
});

$('#unnSubmitBtn').on('click', function () {
    if (tinymce.get('unnFieldContent')) tinymce.triggerSave();
    var $btn = $(this).prop('disabled', true).text('Saving...');

    var formData = new FormData();
    formData.append('_token', CSRF);
    formData.append('title', $('#unnFieldTitle').val());
    formData.append('type', $('#unnFieldType').val());
    formData.append('status', $('#unnFieldStatus').val());
    formData.append('published_date', $('#unnFieldDate').val());
    formData.append('published_hh', $('#unnFieldHh').val());
    formData.append('published_mm', $('#unnFieldMm').val());
    formData.append('short_content', $('#unnFieldShortContent').val());
    formData.append('content', $('#unnFieldContent').val());
    formData.append('video_link', $('#unnFieldVideoLink').val());

    var mediaFile = $('#unnFieldMedia')[0].files[0];
    if (mediaFile) formData.append('media', mediaFile);
    var videoPreviewFile = $('#unnFieldVideoPreview')[0].files[0];
    if (videoPreviewFile) formData.append('video_preview', videoPreviewFile);

    $('#unnTagChips input[name="stock_tickers[]"]').each(function () {
        formData.append('stock_tickers[]', $(this).val());
    });

    var url = unnEditId ? (NEWS_STORE_URL + '/' + unnEditId + '/update') : NEWS_STORE_URL;

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
    })
    .done(function (res) {
        if (res.success) {
            $('#unnMsg').addClass('has-msg success').text(res.message || 'Saved.');
            loadNews(currentPage);
            setTimeout(closeUnnModal, 900);
        } else {
            $('#unnMsg').addClass('has-msg error').text(res.message || 'Failed to save.');
            $btn.prop('disabled', false).text('Submit');
        }
    })
    .fail(function (xhr) {
        var msg = (xhr.responseJSON && (xhr.responseJSON.message || (xhr.responseJSON.errors && Object.values(xhr.responseJSON.errors)[0][0]))) || 'Failed to save.';
        $('#unnMsg').addClass('has-msg error').text(msg);
        $btn.prop('disabled', false).text('Submit');
    });
});

// ── Toggle status ────────────────────────────────────────────
$(document).on('click', '.toggle-news-status', function () {
    var id = $(this).data('id');
    var current = $(this).data('status');
    var next = current === 'Active' ? 'Inactive' : 'Active';
    if (!confirm('Mark this news item as ' + next + '?')) return;

    $.ajax({
        url: NEWS_STATUS_URL + '/' + id + '/status',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF },
        data: { status: next },
    }).done(function (res) {
        if (res.success) loadNews(currentPage);
        else alert(res.message || 'Failed to update status.');
    }).fail(function () {
        alert('Failed to update status.');
    });
});

$(function () {
    loadNews(1);
    initUnnEditor();
});
</script>
@endpush

@endsection
