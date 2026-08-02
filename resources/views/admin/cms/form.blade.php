@extends('layout.admin')

@section('title', ($article->exists ? 'Edit' : 'New') . ' Article | CMS | Admin | UnlistedGain')

@section('content')
<div class="admin-main">

    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <h1 class="admin-page-title">{{ $article->exists ? 'Edit Article' : 'New Article' }}</h1>
        <a href="{{ route('admin.cms.articles') }}" class="cms-back-link"><i class="fa-solid fa-arrow-left"></i> Back to Articles</a>
    </div>

    @if(session('success'))
        <div class="cms-flash-success">{{ session('success') }}</div>
    @endif

    @if(session('lock_error'))
        <div class="cms-lock-banner cms-lock-banner-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>{{ session('lock_error') }}</span>
        </div>
    @endif

    @if($lockedBy ?? null)
        <div class="cms-lock-banner" id="lockBanner">
            <i class="fa-solid fa-lock"></i>
            <span id="lockBannerText"><strong>{{ $lockedBy['name'] }}</strong> is currently editing this article (started {{ $lockedBy['since'] }}). This form is read-only until they finish — you'll be able to edit as soon as it's free.</span>
        </div>
    @endif

    <fieldset id="articleFieldset" @if($lockedBy ?? null) disabled @endif style="border:none;padding:0;margin:0;">
    <form id="articleForm" method="POST"
          action="{{ $article->exists ? route('admin.cms.articles.update', $article->id) : route('admin.cms.articles.store') }}"
          enctype="multipart/form-data">
        @csrf
        @if($article->exists) @method('PUT') @endif

        <div class="cms-form-grid">

            {{-- Main column --}}
            <div class="admin-card cms-main-col">

                <div class="cms-field">
                    <label>Title <span class="req">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $article->title) }}" required
                           class="cms-input" placeholder="Article title">
                    @error('title') <div class="cms-error">{{ $message }}</div> @enderror
                </div>

                @if($article->exists)
                <div class="cms-field">
                    <label>URL Slug</label>
                    <div class="cms-slug-row">
                        <input type="text" name="slug" id="slugInput"
                               value="{{ old('slug', $article->slug) }}"
                               data-original="{{ $article->slug }}"
                               readonly class="cms-input cms-input-locked">
                        <button type="button" id="slugEditToggle" class="cms-slug-toggle-btn">Edit</button>
                    </div>
                    <p class="cms-field-hint cms-field-warning" id="slugWarning" style="display:none;">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        Changing the URL can break links people already have to this article and may drop its Google search ranking. Only change it if you really need to.
                    </p>
                    @error('slug') <div class="cms-error">{{ $message }}</div> @enderror
                </div>
                @endif

                <div class="cms-field">
                    <label>Summary <span class="req">*</span></label>
                    <textarea name="summary" rows="3" required class="cms-input" placeholder="Short summary shown in listings">{{ old('summary', $article->summary) }}</textarea>
                    @error('summary') <div class="cms-error">{{ $message }}</div> @enderror
                </div>

                <div class="cms-field">
                    <label>Content <span class="req">*</span></label>
                    <textarea name="content" id="article_content">{{ old('content', $article->content) }}</textarea>
                    <div class="cms-error" id="contentError" style="display:none;">Content is required.</div>
                    @error('content') <div class="cms-error">{{ $message }}</div> @enderror
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="cms-side-col">

                <div class="admin-card">
                    <div class="cms-side-title">Publish</div>
                    <div class="cms-field">
                        <label>Status</label>
                        <select name="status" class="cms-input">
                            <option value="draft" @selected(old('status', $article->status ?: 'draft') === 'draft')>Draft</option>
                            <option value="published" @selected(old('status', $article->status) === 'published')>Published</option>
                        </select>
                    </div>
                    <button type="submit" class="cms-submit-btn">{{ $article->exists ? 'Save Changes' : 'Create Article' }}</button>
                </div>

                @if($article->exists)
                <div class="admin-card">
                    <div class="cms-side-title">Article Info</div>
                    <div class="cms-info-row">
                        <span class="cms-info-label">Author</span>
                        <span class="cms-info-value">{{ $article->author->name ?? 'Unknown' }}</span>
                    </div>
                    <div class="cms-info-row">
                        <span class="cms-info-label">First drafted</span>
                        <span class="cms-info-value">{{ $article->created_at?->format('d M Y, h:i A') }}</span>
                    </div>
                    <div class="cms-info-row">
                        <span class="cms-info-label">Last updated</span>
                        <span class="cms-info-value">{{ $article->updated_at?->format('d M Y, h:i A') }}</span>
                    </div>
                    @if($article->published_at)
                    <div class="cms-info-row">
                        <span class="cms-info-label">Published on</span>
                        <span class="cms-info-value">{{ $article->published_at->format('d M Y, h:i A') }}</span>
                    </div>
                    @endif
                </div>
                @endif

                <div class="admin-card">
                    <div class="cms-side-title">Featured Image {{ $article->exists ? '' : '(required)' }}</div>
                    @if($article->featured_image)
                        <img src="{{ asset($article->featured_image) }}" class="cms-featured-preview" id="featuredPreview">
                    @else
                        <img src="" class="cms-featured-preview" id="featuredPreview" style="display:none;">
                    @endif
                    <input type="file" name="featured_image" accept="image/*" id="featuredInput" class="cms-input" @unless($article->featured_image) required @endunless>
                    @error('featured_image') <div class="cms-error">{{ $message }}</div> @enderror
                </div>

                <div class="admin-card">
                    <div class="cms-side-title">Related Unlisted Stocks</div>
                    <div class="cms-tag-picker">
                        <div class="cms-tag-chips" id="tagChips">
                            @foreach($selectedStocks as $stock)
                                <span class="cms-tag-chip" data-fincode="{{ $stock->UL_STOCKS_FINCODE }}">
                                    {{ $stock->UL_STOCKS_COMPNAME }}
                                    <input type="hidden" name="stock_tickers[]" value="{{ $stock->UL_STOCKS_FINCODE }}">
                                    <i class="fa-solid fa-xmark cms-tag-remove"></i>
                                </span>
                            @endforeach
                        </div>
                        <input type="text" id="tagSearchInput" class="cms-input" placeholder="Search stock by name...">
                        <ul id="tagSuggestions" class="cms-tag-suggestions"></ul>
                    </div>
                </div>

                <div class="admin-card">
                    <div class="cms-side-title">SEO Meta</div>
                    <div class="cms-field">
                        <label>Meta Title <span class="req">*</span></label>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $article->meta_title) }}" required class="cms-input">
                        @error('meta_title') <div class="cms-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="cms-field">
                        <label>Meta Description <span class="req">*</span></label>
                        <textarea name="meta_description" rows="2" required class="cms-input">{{ old('meta_description', $article->meta_description) }}</textarea>
                        @error('meta_description') <div class="cms-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="cms-field">
                        <label>Meta Keywords <span class="req">*</span></label>
                        <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $article->meta_keywords) }}" required class="cms-input">
                        @error('meta_keywords') <div class="cms-error">{{ $message }}</div> @enderror
                    </div>
                </div>

            </div>
        </div>
    </form>
    </fieldset>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/cms-form.css') }}?v={{ filemtime(public_path('assets/css/admin/cms-form.css')) }}">
@endpush
@endsection

@push('scripts')
<script src="{{ asset('js/tinymce_6.1.2/tinymce.min.js') }}"></script>
<script>
$(function () {
    const CSRF = $('meta[name="csrf-token"]').attr('content');
    const UPLOAD_URL = '{{ route("admin.cms.articles.upload-image") }}';
    const SEARCH_URL = '{{ route("admin.cms.articles.stocks.search") }}';

    // ── Edit lock: first editor to open the article keeps it; everyone else
    // gets a read-only form (enforced again server-side in update()) until the
    // holder saves/leaves, goes idle for a while, or their tab goes quiet past
    // the TTL (crash/network loss). ──
    const IS_LOCKED_OUT = @json((bool) ($lockedBy ?? null));

    let lastActivityAt = Date.now();
    function markActivity() {
        lastActivityAt = Date.now();
        $('#idleNotice').remove();
    }
    $(document).on('mousemove keydown click scroll', markActivity);

    @if($article->exists)
    (function () {
        const HEARTBEAT_URL    = '{{ route("admin.cms.articles.heartbeat", $article->id) }}';
        const RELEASE_LOCK_URL = '{{ route("admin.cms.articles.release-lock", $article->id) }}';
        const HEARTBEAT_MS     = 60000; // keep in sync with LOCK_TTL_MINUTES on the server
        const IDLE_TIMEOUT_MS  = 10 * 60000; // stop renewing the lock after 10 min of no activity

        const heartbeatTimer = setInterval(function () {
            // Holder gone idle: stop renewing so someone else who opens the
            // article can claim it. If nobody does, the next heartbeat after
            // activity resumes just reclaims it automatically — this only
            // opens a window for a genuinely waiting editor, it doesn't force
            // a handover.
            if (!IS_LOCKED_OUT && (Date.now() - lastActivityAt) > IDLE_TIMEOUT_MS) {
                if (!$('#idleNotice').length) {
                    $('<div class="cms-lock-banner" id="idleNotice"><i class="fa-solid fa-hourglass-half"></i>' +
                      '<span>You have been idle for a while. If someone else opens this article they may start editing it — move your mouse or keep typing to hold onto your lock.</span></div>')
                        .insertBefore('#articleFieldset');
                }
                return;
            }

            $.post(HEARTBEAT_URL, { _token: CSRF }).done(function (state) {
                // The lock only ever needs to flip this page editable <-> read-only
                // (this also covers A losing the lock to B after going idle).
                // Reloading re-renders (and, for the newly-freed case, claims the
                // lock) from the server rather than hand-reconciling DOM state.
                if (state.locked !== IS_LOCKED_OUT) location.reload();
            });
        }, HEARTBEAT_MS);

        $(window).on('pagehide beforeunload', function () {
            clearInterval(heartbeatTimer);
            const data = new FormData();
            data.append('_token', CSRF);
            navigator.sendBeacon(RELEASE_LOCK_URL, data);
        });
    })();
    @endif

    function content_image_upload_handler(blobInfo) {
        return new Promise(function (resolve, reject) {
            const formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            formData.append('_token', CSRF);

            $.ajax({
                url: UPLOAD_URL,
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

    tinymce.init({
        selector: 'textarea#article_content',
        plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
        menubar: 'file edit view insert format tools table help',
        toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen preview save print | insertfile image media link anchor codesample | ltr rtl',
        height: 500,
        image_caption: true,
        quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
        toolbar_mode: 'sliding',
        contextmenu: 'link image table',
        convert_urls: true,
        document_base_url: window.location.origin + '/',
        images_upload_handler: content_image_upload_handler,
        automatic_uploads: true,
        branding: false,
        promotion: false,
        readonly: IS_LOCKED_OUT,
        // TinyMCE's content lives in its own iframe, so typing there never
        // bubbles up to the document-level listeners markActivity is bound to.
        setup: function (editor) {
            editor.on('input keyup NodeChange', markActivity);
        },
    });

    // Slug edit toggle
    $('#slugEditToggle').on('click', function () {
        const $input   = $('#slugInput');
        const $warning = $('#slugWarning');
        const editing  = !$input.prop('readonly');

        if (editing) {
            // Cancel: revert to original value and re-lock
            $input.val($input.data('original')).prop('readonly', true).removeClass('cms-input-editable').addClass('cms-input-locked');
            $warning.hide();
            $(this).text('Edit');
        } else {
            $input.prop('readonly', false).removeClass('cms-input-locked').addClass('cms-input-editable').trigger('focus');
            $warning.show();
            $(this).text('Cancel');
        }
    });

    // Featured image preview
    $('#featuredInput').on('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            $('#featuredPreview').attr('src', e.target.result).show();
        };
        reader.readAsDataURL(file);
    });

    // Stock tag picker
    let searchTimer = null;
    $('#tagSearchInput').on('input', function () {
        const term = $(this).val().trim();
        clearTimeout(searchTimer);
        if (term.length < 2) { $('#tagSuggestions').hide().empty(); return; }

        searchTimer = setTimeout(function () {
            $.get(SEARCH_URL, { term: term }).done(function (stocks) {
                const existing = $('#tagChips [data-fincode]').map(function () { return String($(this).data('fincode')); }).get();
                const $list = $('#tagSuggestions').empty();
                stocks.forEach(function (s) {
                    if (existing.indexOf(String(s.fincode)) !== -1) return;
                    $('<li>').text(s.name).attr('data-fincode', s.fincode).attr('data-name', s.name).appendTo($list);
                });
                $list.toggle($list.children().length > 0);
            });
        }, 250);
    });

    $(document).on('click', '#tagSuggestions li', function () {
        const fincode = $(this).data('fincode');
        const name = $(this).data('name');
        const chip = $('<span>').addClass('cms-tag-chip').attr('data-fincode', fincode)
            .html(name + ' <input type="hidden" name="stock_tickers[]" value="' + fincode + '"> <i class="fa-solid fa-xmark cms-tag-remove"></i>');
        $('#tagChips').append(chip);
        $('#tagSearchInput').val('');
        $('#tagSuggestions').hide().empty();
    });

    $(document).on('click', '.cms-tag-remove', function () {
        $(this).closest('.cms-tag-chip').remove();
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.cms-tag-picker').length) $('#tagSuggestions').hide();
    });

    $('#articleForm').on('submit', function (e) {
        if (typeof tinymce !== 'undefined') {
            tinymce.triggerSave();
        }

        const content = $('#article_content').val().replace(/<[^>]*>/g, '').trim();
        if (content === '') {
            e.preventDefault();
            $('#contentError').show();
            $('html, body').animate({ scrollTop: $('#article_content').offset().top - 100 }, 300);
            return false;
        }
        $('#contentError').hide();
    });
});
</script>
@endpush
