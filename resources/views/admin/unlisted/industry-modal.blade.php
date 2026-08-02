@once
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/unlisted-industry-modal.css') }}?v={{ filemtime(public_path('assets/css/admin/unlisted-industry-modal.css')) }}">
@endpush
@endonce

<div id="industryOverlay" class="ind-overlay" onclick="if(event.target===this)closeIndustryModal()">
    <div class="ind-modal">

        <div class="ind-header">
            <div class="ind-title">
                <i class="fa-solid fa-industry" style="color:#87b942;font-size:14px"></i>
                Add Industry
            </div>
            <button class="ind-close" onclick="closeIndustryModal()" type="button">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="ind-body">
            <div class="ind-field">
                <label>Industry Name</label>
                <input type="text" id="indNameInput" class="ind-input"
                       placeholder="e.g. Renewable Energy" maxlength="100">
                <div class="ind-slug-preview" id="indSlugPreview"></div>
            </div>
        </div>

        <div class="ind-footer">
            <span class="ind-msg" id="indMsg"></span>
            <button type="button" class="ind-submit-btn" id="indSubmitBtn">
                <i class="fa-solid fa-plus"></i> Add Industry
            </button>
        </div>

    </div>
</div>

@push('scripts')
<script>
(function () {
    var STORE_URL = '{{ route("admin.unlisted.industries.store") }}';
    var CSRF      = $('meta[name="csrf-token"]').attr('content');

    $('#industryNavBtn').on('click', function () {
        $('#industryOverlay').addClass('open');
        $('#indNameInput').focus();
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') closeIndustryModal();
    });

    $('#indNameInput').on('input', function () {
        $(this).removeClass('error');
        $('#indMsg').text('');
        var slug = slugify($(this).val().trim());
        if (slug) {
            $('#indSlugPreview').html('Slug: <span>' + slug + '</span>');
        } else {
            $('#indSlugPreview').text('');
        }
    });

    $('#indNameInput').on('keydown', function (e) {
        if (e.key === 'Enter') submitIndustry();
    });

    $('#indSubmitBtn').on('click', submitIndustry);

    function submitIndustry() {
        var name = $('#indNameInput').val().trim();
        if (!name) {
            $('#indNameInput').addClass('error').focus();
            return;
        }

        var $btn = $('#indSubmitBtn').prop('disabled', true)
                     .html('<i class="fa-solid fa-spinner fa-spin"></i> Adding…');

        $.ajax({
            url:         STORE_URL,
            method:      'POST',
            contentType: 'application/json',
            headers:     { 'X-CSRF-TOKEN': CSRF },
            data:        JSON.stringify({ name: name }),
        })
        .done(function (res) {
            if (res.success) {
                $('#indMsg').css('color', '#4a7c20')
                    .text(res.industry.name + ' added (code: ' + res.industry.code + ', slug: ' + res.industry.slug + ')');
                $('#indNameInput').val('');
                $('#indSlugPreview').text('');
            } else {
                $('#indMsg').css('color', '#e53935').text(res.message || 'Something went wrong.');
            }
        })
        .fail(function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.name && xhr.responseJSON.errors.name[0])
                   || (xhr.responseJSON && xhr.responseJSON.message)
                   || 'Request failed.';
            $('#indMsg').css('color', '#e53935').text(msg);
        })
        .always(function () {
            $btn.prop('disabled', false)
                .html('<i class="fa-solid fa-plus"></i> Add Industry');
        });
    }

    function slugify(str) {
        return str.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/[\s]+/g, '-')
            .replace(/-+/g, '-');
    }
}());

function closeIndustryModal() {
    $('#industryOverlay').removeClass('open');
    $('#indMsg').text('');
    $('#indNameInput').val('').removeClass('error');
    $('#indSlugPreview').text('');
}
</script>
@endpush
