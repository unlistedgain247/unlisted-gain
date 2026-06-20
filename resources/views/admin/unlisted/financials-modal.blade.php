@php
    $financial = $financial ?? null;
    $isEdit    = $financial !== null;

    $now           = now();
    $curYear       = (int) $now->format('Y');
    $curMonth      = (int) $now->format('m');
    $periodOptions = [];

    for ($y = $curYear; $y >= $curYear - 15; $y--) {
        foreach ([12, 9, 6, 3] as $q) {
            if ($y === $curYear && $q > $curMonth) continue;
            $val   = $y * 100 + $q;
            $label = $y . ' - ' . str_pad($q, 2, '0', STR_PAD_LEFT);
            $periodOptions[$val] = $label;
        }
    }

    // display-only period end label for edit mode
    if ($isEdit) {
        $pe       = (int) $financial->UL_FIN_Period_end;
        $peDisplay = intdiv($pe, 100) . ' - ' . str_pad($pe % 100, 2, '0', STR_PAD_LEFT);
    }

    $closeFunc = $isEdit ? 'closeFinancialsEditModal' : 'closeFinancialsModal';
    $editUrl   = $isEdit
        ? url('/admin/unlisted/stocks/' . $stock->UL_STOCKS_FINCODE . '/financials/'
              . $financial->UL_FIN_Period_end . '/' . $financial->UL_FIN_Type . '/' . $financial->UL_FIN_No_months)
        : '';
@endphp

<style>
.fm-overlay {
    display: flex;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, .55);
    z-index: 2200;
    align-items: center;
    justify-content: center;
    padding: 16px;
    backdrop-filter: blur(2px);
}
.fm-modal {
    background: #fff;
    border-radius: 12px;
    width: 100%;
    max-width: 1140px;
    max-height: 92vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 24px 60px rgba(0, 0, 0, .22);
    animation: privSlideIn .2s cubic-bezier(.34, 1.56, .64, 1);
    overflow: hidden;
}
.fm-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 22px;
    border-bottom: 1px solid #e2e8f0;
    flex-shrink: 0;
}
.fm-header h3 { margin: 0; font-size: 16px; font-weight: 700; color: #1a1a1a; }
.fm-close {
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
    transition: background .15s;
}
.fm-close:hover { background: #e2e8f0; color: #1a1a1a; }
/* form must be flex container to pass height down to fm-body */
#finForm {
    display: flex;
    flex-direction: column;
    flex: 1;
    min-height: 0;
    overflow: hidden;
}
.fm-body {
    overflow-y: auto;
    flex: 1;
    min-height: 0;
}
.fm-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 22px;
    border-top: 1px solid #e2e8f0;
    background: #fafafa;
    flex-shrink: 0;
}
.fm-save-msg { font-size: 13px; font-weight: 500; }
.fm-save-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 22px;
    background: #87b942;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background .15s, transform .1s;
}
.fm-save-btn:hover { background: #6e9735; }
.fm-save-btn:active { transform: scale(.98); }
.fm-save-btn:disabled { opacity: .6; cursor: not-allowed; }

/* 4-column section grid */
.fm-sections {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    min-width: 0;
    height: 100%;
}
.fm-section {
    border-right: 1px solid #e2e8f0;
    min-width: 0;
}
.fm-section:last-child { border-right: none; }
.fm-section-title {
    padding: 10px 14px;
    background: #475569;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    position: sticky;
    top: 0;
    z-index: 1;
}
.fm-section-body { padding: 14px; }
.fm-field { margin-bottom: 12px; }
.fm-field:last-child { margin-bottom: 0; }
.fm-field label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.fm-field input[type=number],
.fm-field input[type=text],
.fm-field select {
    width: 100%;
    padding: 7px 9px;
    border: 1.5px solid #e2e8f0;
    border-radius: 7px;
    font-size: 12px;
    color: #1a1a1a;
    outline: none;
    background: #fff;
    box-sizing: border-box;
    transition: border-color .15s, box-shadow .15s;
}
.fm-field input:focus,
.fm-field select:focus {
    border-color: #87b942;
    box-shadow: 0 0 0 3px rgba(135, 185, 66, .12);
}
.fm-field input[readonly] {
    background: #f8fafc;
    color: #64748b;
    cursor: default;
}
.fm-req { color: #e53935; margin-left: 2px; }

@media (max-width: 900px) {
    .fm-sections { grid-template-columns: repeat(2, 1fr); }
    .fm-section:nth-child(2) { border-right: none; }
}
@media (max-width: 560px) {
    .fm-sections { grid-template-columns: 1fr; }
    .fm-section { border-right: none; }
}
</style>

@if($isEdit)
<div class="fm-overlay" onclick="if(event.target===this)closeFinancialsEditModal()">
@else
<div class="fm-overlay" onclick="if(event.target===this)closeFinancialsModal()">
@endif
<div class="fm-modal">

    <div class="fm-header">
        <h3>{{ $isEdit ? 'Edit' : 'Add' }} Financials &mdash; {{ $stock->UL_STOCKS_COMPNAME }}</h3>
        @if($isEdit)
        <button class="fm-close" onclick="closeFinancialsEditModal()" type="button">
        @else
        <button class="fm-close" onclick="closeFinancialsModal()" type="button">
        @endif
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    @if($isEdit)
    <form id="finForm"
          data-fincode="{{ $stock->UL_STOCKS_FINCODE }}"
          data-mode="edit"
          data-edit-url="{{ $editUrl }}">
    @else
    <form id="finForm"
          data-fincode="{{ $stock->UL_STOCKS_FINCODE }}"
          data-mode="add">
    @endif
        @csrf
        <div class="fm-body">
            <div class="fm-sections">

                {{-- ── Column 1: Financials Overview ─────────── --}}
                <div class="fm-section">
                    <div class="fm-section-title">Financials Overview</div>
                    <div class="fm-section-body">

                        <div class="fm-field">
                            <label>Company</label>
                            <input type="text" value="{{ $stock->UL_STOCKS_COMPNAME }}" readonly>
                        </div>

                        <div class="fm-field">
                            <label>Period End <span class="fm-req">*</span></label>
                            <select name="UL_FIN_Period_end" required>
                                <option value="">Select</option>
                                @foreach ($periodOptions as $val => $label)
                                    <option value="{{ $val }}" @selected($isEdit && (int)$financial->UL_FIN_Period_end === $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="fm-field">
                            <label>Type <span class="fm-req">*</span></label>
                            <select name="UL_FIN_Type" required>
                                <option value="">Select</option>
                                <option value="C" @selected($isEdit && $financial->UL_FIN_Type === 'C')>Consolidated</option>
                                <option value="S" @selected($isEdit && $financial->UL_FIN_Type === 'S')>Standalone</option>
                            </select>
                        </div>

                        <div class="fm-field">
                            <label>No. Months <span class="fm-req">*</span></label>
                            <select name="UL_FIN_No_months" required>
                                <option value="">Select</option>
                                <option value="3"  @selected($isEdit && (string)$financial->UL_FIN_No_months === '3')>3</option>
                                <option value="6"  @selected($isEdit && (string)$financial->UL_FIN_No_months === '6')>6</option>
                                <option value="12" @selected($isEdit && (string)$financial->UL_FIN_No_months === '12')>12</option>
                            </select>
                        </div>

                        <div class="fm-field">
                            <label>Unit</label>
                            <select name="UL_FIN_Unit">
                                <option value="">Select</option>
                                @php
                                    $unitLabels = [
                                        1        => '1 (One)',
                                        100      => '100 (1 Hundred)',
                                        1000     => '1000 (1 Thousand)',
                                        10000    => '10000 (10 Thousands)',
                                        100000   => '100000 (1 Lac)',
                                        1000000  => '1000000 (10 Lacs)',
                                        10000000 => '10000000 (1 Crore)',
                                    ];
                                @endphp
                                @foreach ($unitLabels as $u => $label)
                                    <option value="{{ $u }}" @selected($isEdit && (int)($financial->UL_FIN_Unit ?? -1) === $u)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="fm-field">
                            <label>FV</label>
                            <input type="number" step="0.01" name="UL_FIN_FV"
                                   value="{{ $financial?->UL_FIN_FV ?? '' }}">
                        </div>

                        <div class="fm-field">
                            <label>Num Shares</label>
                            <input type="number" step="0.01" name="UL_FIN_NUM_SHARES"
                                   value="{{ $financial?->UL_FIN_NUM_SHARES ?? '' }}">
                        </div>

                        <div class="fm-field">
                            <label>Promoters Holding</label>
                            <input type="number" step="0.01" name="UL_FIN_PROMOTERS_HOLDING"
                                   value="{{ $financial?->UL_FIN_PROMOTERS_HOLDING ?? '' }}">
                        </div>

                        <div class="fm-field">
                            <label>Annual Dividend Per Share</label>
                            <input type="number" step="0.01" name="UL_FIN_ANNUAL_DIVIDEND_PER_SHARE"
                                   value="{{ $financial?->UL_FIN_ANNUAL_DIVIDEND_PER_SHARE ?? '' }}">
                        </div>

                    </div>
                </div>

                {{-- ── Column 2: P&L ─────────────────────────── --}}
                <div class="fm-section">
                    <div class="fm-section-title">P&amp;L</div>
                    <div class="fm-section-body">

                        <div class="fm-field">
                            <label>Net Sales</label>
                            <input type="number" step="0.01" name="UL_FIN_NET_SALES"
                                   value="{{ $financial?->UL_FIN_NET_SALES ?? '' }}">
                        </div>

                        <div class="fm-field">
                            <label>Other Income</label>
                            <input type="number" step="0.01" name="UL_FIN_OTHER_INCOME"
                                   value="{{ $financial?->UL_FIN_OTHER_INCOME ?? '' }}">
                        </div>

                        <div class="fm-field">
                            <label>Total Income</label>
                            <input type="number" step="0.01" name="UL_FIN_TOTAL_INCOME"
                                   value="{{ $financial?->UL_FIN_TOTAL_INCOME ?? '' }}">
                        </div>

                        <div class="fm-field">
                            <label>Expenditure</label>
                            <input type="number" step="0.01" name="UL_FIN_TOTAL_EXPENDITURE"
                                   value="{{ $financial?->UL_FIN_TOTAL_EXPENDITURE ?? '' }}">
                        </div>

                        <div class="fm-field">
                            <label>Operating Profit</label>
                            <input type="number" step="0.01" name="UL_FIN_OPERATING_PROFIT"
                                   value="{{ $financial?->UL_FIN_OPERATING_PROFIT ?? '' }}">
                        </div>

                        <div class="fm-field">
                            <label>Interest</label>
                            <input type="number" step="0.01" name="UL_FIN_INTEREST"
                                   value="{{ $financial?->UL_FIN_INTEREST ?? '' }}">
                        </div>

                        <div class="fm-field">
                            <label>Depreciation</label>
                            <input type="number" step="0.01" name="UL_FIN_DEPRECIATION"
                                   value="{{ $financial?->UL_FIN_DEPRECIATION ?? '' }}">
                        </div>

                        <div class="fm-field">
                            <label>Exceptional Income</label>
                            <input type="number" step="0.01" name="UL_FIN_EXCEPTIONAL_INCOME"
                                   value="{{ $financial?->UL_FIN_EXCEPTIONAL_INCOME ?? '' }}">
                        </div>

                        <div class="fm-field">
                            <label>PBT</label>
                            <input type="number" step="0.01" name="UL_FIN_PBT"
                                   value="{{ $financial?->UL_FIN_PBT ?? '' }}">
                        </div>

                        <div class="fm-field">
                            <label>TAX</label>
                            <input type="number" step="0.01" name="UL_FIN_TAX"
                                   value="{{ $financial?->UL_FIN_TAX ?? '' }}">
                        </div>

                        <div class="fm-field">
                            <label>PAT</label>
                            <input type="number" step="0.01" name="UL_FIN_PAT"
                                   value="{{ $financial?->UL_FIN_PAT ?? '' }}">
                        </div>

                    </div>
                </div>

                {{-- ── Column 3: Balance Sheet ─────────────────── --}}
                <div class="fm-section">
                    <div class="fm-section-title">Balance Sheet</div>
                    <div class="fm-section-body">

                        <div class="fm-field">
                            <label>Shareholder Funds</label>
                            <input type="number" step="0.01" name="UL_FIN_SHAREHOLDER_FUNDS"
                                   value="{{ $financial?->UL_FIN_SHAREHOLDER_FUNDS ?? '' }}">
                        </div>

                        <div class="fm-field">
                            <label>Total Liabilities</label>
                            <input type="number" step="0.01" name="UL_FIN_TOTAL_LIABILITIES" id="finTotalLiab"
                                   value="{{ $financial?->UL_FIN_TOTAL_LIABILITIES ?? '' }}">
                        </div>

                        <div class="fm-field">
                            <label>Total Assets</label>
                            <input type="number" step="0.01" name="UL_FIN_TOTAL_ASSETS" id="finTotalAssets"
                                   value="{{ $financial?->UL_FIN_TOTAL_ASSETS ?? '' }}">
                        </div>

                        <div id="finBalanceErr" style="display:none;font-size:11px;color:#e53935;font-weight:600;margin-bottom:10px;padding:6px 8px;background:#fff5f5;border-radius:6px;border:1px solid #fca5a5">
                            Total Assets ≠ Total Liabilities
                        </div>

                        <div class="fm-field">
                            <label>Total Debt</label>
                            <input type="number" step="0.01" name="UL_FIN_TOTAL_DEBT"
                                   value="{{ $financial?->UL_FIN_TOTAL_DEBT ?? '' }}">
                        </div>

                        <div class="fm-field">
                            <label>Current Liabilities</label>
                            <input type="number" step="0.01" name="UL_FIN_CURRENT_LIABILITIES"
                                   value="{{ $financial?->UL_FIN_CURRENT_LIABILITIES ?? '' }}">
                        </div>

                        <div class="fm-field">
                            <label>Non Current Liabilities</label>
                            <input type="number" step="0.01" name="UL_FIN_NON_CURRENT_LIABILITIES"
                                   value="{{ $financial?->UL_FIN_NON_CURRENT_LIABILITIES ?? '' }}">
                        </div>

                        <div class="fm-field">
                            <label>Current Assets</label>
                            <input type="number" step="0.01" name="UL_FIN_CURRENT_ASSETS"
                                   value="{{ $financial?->UL_FIN_CURRENT_ASSETS ?? '' }}">
                        </div>

                        <div class="fm-field">
                            <label>Non Current Assets</label>
                            <input type="number" step="0.01" name="UL_FIN_NON_CURRENT_ASSETS"
                                   value="{{ $financial?->UL_FIN_NON_CURRENT_ASSETS ?? '' }}">
                        </div>

                    </div>
                </div>

                {{-- ── Column 4: Cash Flow ─────────────────────── --}}
                <div class="fm-section">
                    <div class="fm-section-title">Cash Flow</div>
                    <div class="fm-section-body">

                        <div class="fm-field">
                            <label>Operating Activities</label>
                            <input type="number" step="0.01" name="UL_FIN_CASH_FLOW_FROM_OPERATING_ACTIVITIES"
                                   value="{{ $financial?->UL_FIN_CASH_FLOW_FROM_OPERATING_ACTIVITIES ?? '' }}">
                        </div>

                        <div class="fm-field">
                            <label>Investing Activities</label>
                            <input type="number" step="0.01" name="UL_FIN_CASH_FLOW_FORM_INVESTING_ACTIVITIES"
                                   value="{{ $financial?->UL_FIN_CASH_FLOW_FORM_INVESTING_ACTIVITIES ?? '' }}">
                        </div>

                        <div class="fm-field">
                            <label>Financing Activities</label>
                            <input type="number" step="0.01" name="UL_FIN_CASH_FLOW_FROM_FINANCING_ACTIVITIES"
                                   value="{{ $financial?->UL_FIN_CASH_FLOW_FROM_FINANCING_ACTIVITIES ?? '' }}">
                        </div>

                        <div class="fm-field">
                            <label>Free Cash Flow</label>
                            <input type="number" step="0.01" name="UL_FIN_FREE_CASH_FLOW"
                                   value="{{ $financial?->UL_FIN_FREE_CASH_FLOW ?? '' }}">
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <div class="fm-footer">
            <span id="finSaveMsg" class="fm-save-msg"></span>
            <button type="submit" class="fm-save-btn">
                <i class="fa-solid fa-floppy-disk"></i> {{ $isEdit ? 'Update' : 'Save' }}
            </button>
        </div>
    </form>

</div>
</div>

<script>
(function () {
    var STOCKS_BASE = window.STOCKS_BASE;
    var CSRF        = $('meta[name="csrf-token"]').attr('content');
    var isEdit      = $('#finForm').data('mode') === 'edit';
    var url         = isEdit ? $('#finForm').data('edit-url') : STOCKS_BASE + '/' + $('#finForm').data('fincode') + '/financials';
    var method      = isEdit ? 'PUT' : 'POST';

    // balance sheet real-time check
    $('#finTotalAssets, #finTotalLiab').on('input', function () {
        var assets   = parseFloat($('#finTotalAssets').val());
        var liab     = parseFloat($('#finTotalLiab').val());
        var both     = !isNaN(assets) && !isNaN(liab);
        var mismatch = both && assets !== liab;
        $('#finBalanceErr').toggle(mismatch);
        $('#finTotalAssets, #finTotalLiab').css('border-color', mismatch ? '#e53935' : '');
    });

    $('#finForm').on('submit', function (e) {
        e.preventDefault();

        var assets = parseFloat($('#finTotalAssets').val());
        var liab   = parseFloat($('#finTotalLiab').val());
        if (!isNaN(assets) && !isNaN(liab) && assets !== liab) {
            $('#finSaveMsg').css('color', '#e53935').text('Total Assets must equal Total Liabilities.');
            return;
        }

        var $btn = $(this).find('.fm-save-btn').prop('disabled', true)
                          .html('<i class="fa-solid fa-spinner fa-spin"></i> Saving…');
        var data = {};
        $(this).serializeArray().forEach(function (f) { data[f.name] = f.value; });

        $.ajax({
            url:         url,
            method:      method,
            contentType: 'application/json',
            headers:     { 'X-CSRF-TOKEN': CSRF },
            data:        JSON.stringify(data),
        })
        .done(function (res) {
            var color = res.success ? '#4a7c20' : '#e53935';
            $('#finSaveMsg').css('color', color).text(res.message || (res.success ? 'Saved.' : 'Error.'));
            if (res.success && isEdit) {
                setTimeout(function () { closeFinancialsEditModal(); }, 800);
            }
        })
        .fail(function (xhr) {
            var errors   = xhr.responseJSON && xhr.responseJSON.errors ? xhr.responseJSON.errors : {};
            var firstKey = Object.keys(errors)[0];
            var msg      = (firstKey ? errors[firstKey][0] : null)
                         || (xhr.responseJSON && xhr.responseJSON.message)
                         || 'Request failed.';
            $('#finSaveMsg').css('color', '#e53935').text(msg);
        })
        .always(function () {
            $btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk"></i> ' + (isEdit ? 'Update' : 'Save'));
        });
    });
}());
</script>
