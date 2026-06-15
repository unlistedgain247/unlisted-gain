@php
    $months = ['January','February','March','April','May','June',
               'July','August','September','October','November','December'];
    $currentYear = date('Y');
    $yesNo = ['Yes' => 'Yes', 'No' => 'No'];
    $ratings = ['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5'];

    $sel = fn($val, $option) => $val == $option ? 'selected' : '';
@endphp

<style>
.ov-overlay {
    display: flex;
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,0.55);
    z-index: 2100;
    align-items: center;
    justify-content: center;
    padding: 16px;
    backdrop-filter: blur(2px);
}
.ov-modal {
    background: #fff;
    border-radius: 12px;
    width: 100%;
    max-width: 860px;
    max-height: 92vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 24px 60px rgba(0,0,0,0.22);
    animation: privSlideIn 0.2s cubic-bezier(0.34,1.56,0.64,1);
    overflow: hidden;
}
.ov-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 22px;
    border-bottom: 1px solid #e2e8f0;
    flex-shrink: 0;
}
.ov-header h3 { margin:0; font-size:16px; font-weight:700; color:#1a1a1a; }
.ov-close {
    background:#f1f5f9; border:none; border-radius:8px;
    width:32px; height:32px; display:flex; align-items:center;
    justify-content:center; cursor:pointer; color:#64748b;
    transition:background 0.15s;
}
.ov-close:hover { background:#e2e8f0; color:#1a1a1a; }
.ov-body {
    padding: 20px 22px;
    overflow-y: auto;
    flex: 1;
}
.ov-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 22px;
    border-top: 1px solid #e2e8f0;
    background: #fafafa;
    flex-shrink: 0;
}
.ov-save-msg { font-size:13px; font-weight:500; }
.ov-save-btn {
    display:inline-flex; align-items:center; gap:7px;
    padding:9px 22px; background:#87b942; color:#fff;
    border:none; border-radius:8px; font-size:13px;
    font-weight:600; cursor:pointer;
    transition:background 0.15s, transform 0.1s;
}
.ov-save-btn:hover { background:#6e9735; }
.ov-save-btn:active { transform:scale(0.98); }

/* grid helpers */
.ov-row { display:grid; gap:14px; margin-bottom:14px; }
.ov-cols-3 { grid-template-columns: repeat(3,1fr); }
.ov-cols-4 { grid-template-columns: repeat(4,1fr); }
.ov-cols-6 { grid-template-columns: repeat(6,1fr); }
.ov-cols-2 { grid-template-columns: repeat(2,1fr); }
.ov-span-full { grid-column: 1 / -1; }

.ov-field label {
    display:block; font-size:12px; font-weight:600;
    color:#64748b; margin-bottom:5px; text-transform:uppercase;
    letter-spacing:0.04em;
}
.ov-field input[type=text],
.ov-field select,
.ov-field textarea {
    width:100%; padding:8px 10px;
    border:1.5px solid #e2e8f0; border-radius:7px;
    font-size:13px; color:#1a1a1a; outline:none;
    transition:border-color 0.15s, box-shadow 0.15s;
    background:#fff; box-sizing:border-box;
}
.ov-field input:focus,
.ov-field select:focus,
.ov-field textarea:focus {
    border-color:#87b942;
    box-shadow:0 0 0 3px rgba(135,185,66,0.12);
}
.ov-field textarea { resize:vertical; min-height:120px; }

/* radio row */
.ov-radios { display:flex; align-items:center; gap:20px; flex-wrap:wrap; }
.ov-radios label {
    display:flex; align-items:center; gap:6px;
    font-size:13px; font-weight:500; color:#334155;
    cursor:pointer; text-transform:none; letter-spacing:0;
}
.ov-radios input[type=radio] { accent-color:#87b942; width:15px; height:15px; }

/* logo row */
.ov-logo-wrap { display:flex; align-items:center; gap:8px; }
.ov-logo-wrap input[type=file] {
    flex:1; padding:6px 8px; font-size:12px;
    border:1.5px solid #e2e8f0; border-radius:7px; cursor:pointer;
}
.ov-logo-dl {
    color:#2196f3; font-size:18px; cursor:pointer; flex-shrink:0;
    text-decoration:none;
}

@media(max-width:700px) {
    .ov-cols-3,.ov-cols-4,.ov-cols-6 { grid-template-columns:1fr 1fr; }
    .ov-cols-6 { grid-template-columns:1fr 1fr 1fr; }
}
</style>

<div class="ov-overlay" id="overviewOverlay" onclick="if(event.target===this)closeOverviewModal()">
<div class="ov-modal">

    <div class="ov-header">
        <h3>Edit — {{ $stock->UL_STOCKS_COMPNAME }}</h3>
        <button class="ov-close" onclick="closeOverviewModal()" type="button">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <form id="overviewForm" data-fincode="{{ $stock->UL_STOCKS_FINCODE }}">
        @csrf
        <div class="ov-body">

            {{-- Company Type --}}
            <div class="ov-field" style="margin-bottom:14px">
                <label>Company Type</label>
                <div class="ov-radios">
                    <label>
                        <input type="radio" name="UL_STOCKS_COMPNAME_TYPE" value="unlisted" checked>
                        Unlisted
                    </label>
                </div>
            </div>

            {{-- Row: Company Name | Upload Logo | Industry --}}
            <div class="ov-row ov-cols-3">
                <div class="ov-field">
                    <label>Company Name</label>
                    <input type="text" name="UL_STOCKS_COMPNAME"
                           value="{{ $stock->UL_STOCKS_COMPNAME }}">
                </div>
                <div class="ov-field">
                    <label>Upload Logo</label>
                    <div class="ov-logo-wrap">
                        <input type="file" name="logo" accept="image/*">
                        @if($stock->UL_STOCKS_LOGO_LINK)
                            <a href="{{ asset($stock->UL_STOCKS_LOGO_LINK) }}" target="_blank"
                               class="ov-logo-dl" title="View current logo">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        @else
                            <span class="ov-logo-dl" style="color:#ccc">
                                <i class="fa-solid fa-download"></i>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="ov-field">
                    <label>Industry</label>
                    <select name="UL_STOCKS_IND_CODE">
                        <option value="">— Select —</option>
                        @foreach($industries as $ind)
                            <option value="{{ $ind->IM_IND_CODE }}"
                                @selected($stock->UL_STOCKS_IND_CODE == $ind->IM_IND_CODE)>
                                {{ $ind->IM_INDUSTRY }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Row: ISIN | Short Name | Category --}}
            <div class="ov-row ov-cols-3">
                <div class="ov-field">
                    <label>ISIN</label>
                    <input type="text" name="UL_STOCKS_ISIN" value="{{ $stock->UL_STOCKS_ISIN }}">
                </div>
                <div class="ov-field">
                    <label>Short Name</label>
                    <input type="text" name="UL_STOCKS_S_NAME" value="{{ $stock->UL_STOCKS_S_NAME }}">
                </div>
                <div class="ov-field">
                    <label>Category</label>
                    <select name="UL_STOCKS_CATEGORY">
                        <option value="">— Select —</option>
                        <option value="startup_funding" {{ $sel($stock->UL_STOCKS_CATEGORY, 'startup_funding') }}>Startup Funding</option>
                        <option value="pre_ipo"         {{ $sel($stock->UL_STOCKS_CATEGORY, 'pre_ipo') }}>Pre IPO</option>
                        <option value="delisted"        {{ $sel($stock->UL_STOCKS_CATEGORY, 'delisted') }}>Delisted</option>
                    </select>
                </div>
            </div>

            {{-- Row: Inc Month | Inc Year | Website | Status --}}
            <div class="ov-row ov-cols-4">
                <div class="ov-field">
                    <label>Inc Month</label>
                    <select name="UL_STOCKS_INC_MONTH">
                        <option value="">— Select —</option>
                        @foreach($months as $month)
                            <option value="{{ $month }}" {{ $sel($stock->UL_STOCKS_INC_MONTH, $month) }}>{{ $month }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ov-field">
                    <label>Inc Year</label>
                    <select name="UL_STOCKS_INC_YEAR">
                        <option value="">— Select —</option>
                        @for($y = $currentYear; $y >= 1900; $y--)
                            <option value="{{ $y }}" {{ $sel($stock->UL_STOCKS_INC_YEAR, $y) }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="ov-field">
                    <label>Website</label>
                    <input type="text" name="UL_STOCKS_WEBSITE" value="{{ $stock->UL_STOCKS_WEBSITE }}"
                           placeholder="www.example.com">
                </div>
                <div class="ov-field">
                    <label>Status</label>
                    <select name="UL_STOCKS_STATUS">
                        <option value="1" {{ $sel($stock->UL_STOCKS_STATUS, '1') }}>Active</option>
                        <option value="0" {{ $sel($stock->UL_STOCKS_STATUS, '0') }}>Inactive</option>
                    </select>
                </div>
            </div>

            {{-- Row: Company Rating | Valuation Rating | InstaBuy | InstaSell | Buy-Sell | Lot Size --}}
            <div class="ov-row ov-cols-6">
                <div class="ov-field">
                    <label>Company Rating</label>
                    <select name="UL_STOCKS_COMP_RATING">
                        <option value="">Select</option>
                        @foreach($ratings as $r)
                            <option value="{{ $r }}" {{ $sel($stock->UL_STOCKS_COMP_RATING, $r) }}>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ov-field">
                    <label>Valuation Rating</label>
                    <select name="UL_STOCKS_VALUATION_RATING">
                        <option value="">Select</option>
                        @foreach($ratings as $r)
                            <option value="{{ $r }}" {{ $sel($stock->UL_STOCKS_VALUATION_RATING, $r) }}>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ov-field">
                    <label>InstaBuy Flag</label>
                    <select name="UL_STOCKS_INSTA_BUY_FLAG">
                        <option value="">Select</option>
                        @foreach($yesNo as $v => $l)
                            <option value="{{ $v }}" {{ $sel($stock->UL_STOCKS_INSTA_BUY_FLAG, $v) }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ov-field">
                    <label>InstaSell Flag</label>
                    <select name="UL_STOCKS_INSTA_SELL_FLAG">
                        <option value="">Select</option>
                        @foreach($yesNo as $v => $l)
                            <option value="{{ $v }}" {{ $sel($stock->UL_STOCKS_INSTA_SELL_FLAG, $v) }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ov-field">
                    <label>Buy-Sell Flag</label>
                    <select name="UL_STOCKS_BUY_SELL_FLAG">
                        @foreach($yesNo as $v => $l)
                            <option value="{{ $v }}" {{ $sel($stock->UL_STOCKS_BUY_SELL_FLAG ?? 'Yes', $v) }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ov-field">
                    <label>Lot Size</label>
                    <input type="text" name="UL_STOCKS_LOT_SIZE" value="{{ $stock->UL_STOCKS_LOT_SIZE }}">
                </div>
            </div>

            {{-- Row: ROFR | Demat Account Required | Qtr Data Publish --}}
            <div class="ov-row ov-cols-3">
                <div class="ov-field">
                    <label>ROFR</label>
                    <select name="UL_STOCKS_ROFR_FLAG">
                        <option value="">Select</option>
                        @foreach($yesNo as $v => $l)
                            <option value="{{ $v }}" {{ $sel($stock->UL_STOCKS_ROFR_FLAG, $v) }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ov-field">
                    <label>Demat Account Required</label>
                    <select name="UL_STOCKS_DEMAT_ACCOUNT_REQ">
                        <option value="">Select</option>
                        <option value="NSDL"           {{ $sel($stock->UL_STOCKS_DEMAT_ACCOUNT_REQ, 'NSDL') }}>NSDL</option>
                        <option value="CDSL"           {{ $sel($stock->UL_STOCKS_DEMAT_ACCOUNT_REQ, 'CDSL') }}>CDSL</option>
                        <option value="Both" {{ $sel($stock->UL_STOCKS_DEMAT_ACCOUNT_REQ, 'Both') }}>Both (NSDL/CDSL)</option>
                    </select>
                </div>
                <div class="ov-field">
                    <label>Qtr Data Publish</label>
                    <select name="UL_STOCKS_Qtr_Data_Publish">
                        @foreach($yesNo as $v => $l)
                            <option value="{{ $v }}" {{ $sel($stock->UL_STOCKS_Qtr_Data_Publish ?? 'Yes', $v) }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- About --}}
            <div class="ov-field">
                <label>About</label>
                <textarea name="UL_STOCKS_ABOUT" rows="6">{{ $stock->UL_STOCKS_ABOUT }}</textarea>
            </div>

        </div>

        <div class="ov-footer">
            <span id="ovSaveMsg" class="ov-save-msg"></span>
            <button type="submit" class="ov-save-btn">
                <i class="fa-solid fa-floppy-disk"></i> Save Changes
            </button>
        </div>
    </form>

</div>
</div>

<script>
(function () {
    var STOCKS_BASE = window.STOCKS_BASE;
    var CSRF        = $('meta[name="csrf-token"]').attr('content');

    $('#overviewForm').on('submit', function (e) {
        e.preventDefault();
        var fincode = $(this).data('fincode');
        var fd      = new FormData(this);
        var $btn    = $(this).find('.ov-save-btn').prop('disabled', true)
                             .html('<i class="fa-solid fa-spinner fa-spin"></i> Saving…');

        $.ajax({
            url:         STOCKS_BASE + '/' + fincode + '/overview',
            method:      'POST',
            data:        fd,
            processData: false,
            contentType: false,
            headers:     { 'X-CSRF-TOKEN': CSRF },
        })
        .done(function (res) {
            var color = res.success ? '#4a7c20' : '#e53935';
            $('#ovSaveMsg').css('color', color).text(res.message || (res.success ? 'Saved.' : 'Error.'));
            if (res.success) {
                $('[data-fincode="' + fincode + '"]').closest('tr')
                    .find('td:nth-child(2)').text(fd.get('UL_STOCKS_COMPNAME'));
            }
        })
        .fail(function (xhr) {
            var errors = xhr.responseJSON && xhr.responseJSON.errors ? xhr.responseJSON.errors : {};
            var msg    = (errors.logo && errors.logo[0])
                       || (xhr.responseJSON && xhr.responseJSON.message)
                       || 'Request failed.';
            $('#ovSaveMsg').css('color', '#e53935').text(msg);
        })
        .always(function () {
            $btn.prop('disabled', false)
                .html('<i class="fa-solid fa-floppy-disk"></i> Save Changes');
        });
    });
}());
</script>
