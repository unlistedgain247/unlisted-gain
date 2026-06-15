@once
@push('styles')
<style>
    .ind-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        z-index: 2000;
        align-items: center;
        justify-content: center;
        padding: 16px;
        backdrop-filter: blur(2px);
    }

    .ind-overlay.open {
        display: flex;
    }

    .ind-modal {
        background: #fff;
        border-radius: 14px;
        width: 100%;
        max-width: 440px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.2);
        animation: privSlideIn 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        overflow: hidden;
    }

    .ind-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
        flex-shrink: 0;
    }

    .ind-title {
        display: flex;
        align-items: center;
        gap: 9px;
        font-size: 15px;
        font-weight: 700;
        color: #1a1a1a;
    }

    .ind-close {
        background: #f1f5f9;
        border: none;
        border-radius: 8px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #64748b;
        transition: background 0.15s, color 0.15s;
    }

    .ind-close:hover {
        background: #e2e8f0;
        color: #1a1a1a;
    }

    .ind-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .ind-field label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .ind-input {
        width: 100%;
        padding: 9px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: 13px;
        color: #1a1a1a;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
        box-sizing: border-box;
    }

    .ind-input:focus {
        border-color: #87b942;
        box-shadow: 0 0 0 3px rgba(135, 185, 66, 0.12);
    }

    .ind-input.error {
        border-color: #e53935;
        box-shadow: 0 0 0 3px rgba(229, 57, 53, 0.1);
    }

    .ind-slug-preview {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 4px;
        min-height: 16px;
    }

    .ind-slug-preview span {
        color: #64748b;
        font-weight: 500;
    }

    .ind-footer {
        padding: 14px 20px;
        border-top: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .ind-msg {
        font-size: 13px;
        font-weight: 500;
        flex: 1;
    }

    .ind-submit-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 20px;
        background: #87b942;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: background 0.15s, transform 0.1s;
        flex-shrink: 0;
    }

    .ind-submit-btn:hover { background: #6e9735; }
    .ind-submit-btn:active { transform: scale(0.97); }
    .ind-submit-btn:disabled { opacity: 0.6; cursor: not-allowed; }
</style>
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
