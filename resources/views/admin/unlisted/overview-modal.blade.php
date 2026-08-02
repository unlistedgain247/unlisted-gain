@php
    $months = ['January','February','March','April','May','June',
               'July','August','September','October','November','December'];
    $currentYear = date('Y');
    $yesNo = ['Yes' => 'Yes', 'No' => 'No'];
    $ratings = ['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5'];

    $sel = fn($val, $option) => $val == $option ? 'selected' : '';
@endphp

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

            {{-- Row: Company Rating | Valuation Rating | Buy-Sell | Lot Size --}}
            <div class="ov-row ov-cols-4">
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
                var $a = $('[data-fincode="' + fincode + '"]').closest('tr').find('td:nth-child(2) a');
                $a.contents().filter(function () { return this.nodeType === 3; }).first()
                  .replaceWith(document.createTextNode(fd.get('UL_STOCKS_COMPNAME')));
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
