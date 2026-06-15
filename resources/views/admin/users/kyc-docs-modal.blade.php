<div style="background:#fff;border-radius:14px;width:100%;max-width:780px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.2);">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px 16px;border-bottom:1px solid #f0f0f0;">
        <div>
            <h3 style="font-size:16px;font-weight:700;color:#1a1a1a;margin:0 0 2px;font-family:'Inter',sans-serif;">KYC Documents</h3>
            <p style="font-size:12px;color:#999;margin:0;font-family:'Inter',sans-serif;">{{ $user->name }} &middot; {{ $user->email }}</p>
        </div>
        <button onclick="closeKycDocsModal()" style="width:32px;height:32px;border-radius:8px;border:none;background:#f5f5f5;color:#666;font-size:20px;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;">&times;</button>
    </div>

    {{-- Body --}}
    <div style="padding:20px 24px 24px;display:flex;flex-direction:column;gap:16px;">

        {{-- ── BANK ── --}}
        @php $bankFilled = (bool) $user->bank_account_no; @endphp
        <div style="border:1px solid #ebebeb;border-radius:12px;overflow:hidden;">
            <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:#fafafa;border-bottom:1px solid #ebebeb;">
                <div style="width:36px;height:36px;border-radius:10px;background:#e8f5e9;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fa-solid fa-building-columns" style="color:#2e7d32;font-size:16px"></i>
                </div>
                <div style="flex:1;font-size:13px;font-weight:700;color:#1a1a1a;font-family:'Inter',sans-serif;">Bank — Cancelled Cheque</div>
                @if($user->bank_verified)
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#e8f5e9;color:#2e7d32;border:1px solid #a5d6a7;white-space:nowrap;font-family:'Inter',sans-serif;"><i class="fa-solid fa-circle-check"></i> Verified</span>
                @elseif($bankFilled)
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#fff8e1;color:#f57f17;border:1px solid #ffe082;white-space:nowrap;font-family:'Inter',sans-serif;"><i class="fa-regular fa-clock"></i> Pending</span>
                @else
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#f5f5f5;color:#aaa;border:1px solid #e0e0e0;white-space:nowrap;font-family:'Inter',sans-serif;"><i class="fa-solid fa-circle-minus"></i> None</span>
                @endif
            </div>
            @if($bankFilled)
            <div style="padding:14px 16px;display:grid;grid-template-columns:1fr 1fr;gap:12px 20px;">
                <div>
                    <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#999;margin-bottom:3px;font-family:'Inter',sans-serif;">Account Holder</div>
                    <div style="font-size:13px;color:#1a1a1a;font-family:'Inter',sans-serif;font-weight:500;">{{ $user->bank_holder_name ?: '—' }}</div>
                </div>
                <div>
                    <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#999;margin-bottom:3px;font-family:'Inter',sans-serif;">Bank Name</div>
                    <div style="font-size:13px;color:#1a1a1a;font-family:'Inter',sans-serif;font-weight:500;">{{ $user->bank_name ?: '—' }}</div>
                </div>
                <div>
                    <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#999;margin-bottom:3px;font-family:'Inter',sans-serif;">Account Number</div>
                    <div style="font-size:13px;color:#1a1a1a;font-family:'Inter',sans-serif;font-weight:500;letter-spacing:0.04em;">{{ $user->bank_account_no }}</div>
                </div>
                <div>
                    <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#999;margin-bottom:3px;font-family:'Inter',sans-serif;">IFSC Code</div>
                    <div style="font-size:13px;color:#1a1a1a;font-family:'Inter',sans-serif;font-weight:500;letter-spacing:0.04em;">{{ $user->bank_ifsc_code ?: '—' }}</div>
                </div>
            </div>
            @else
            <div style="padding:12px 16px;font-size:12px;color:#bbb;font-family:'Inter',sans-serif;">Not submitted</div>
            @endif
            <div style="display:flex;gap:8px;flex-wrap:wrap;padding:0 16px 14px;">
                @if($user->bank_cancelled_check)
                    <a href="{{ route('admin.admin.users.kyc', ['uid' => $user->uid, 'type' => 'bank']) }}" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:7px;border:1.5px solid #1565c0;background:#fff;color:#1565c0;font-size:12px;font-weight:600;text-decoration:none;font-family:'Inter',sans-serif;" onmouseover="this.style.background='#e3f2fd'" onmouseout="this.style.background='#fff'"><i class="fa-regular fa-eye"></i> View</a>
                    <a href="{{ route('admin.admin.users.kyc', ['uid' => $user->uid, 'type' => 'bank']) }}" download style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:7px;border:1.5px solid #555;background:#fff;color:#555;font-size:12px;font-weight:600;text-decoration:none;font-family:'Inter',sans-serif;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='#fff'"><i class="fa-solid fa-download"></i> Download</a>
                @else
                    <span style="font-size:12px;color:#bbb;font-family:'Inter',sans-serif;padding:6px 0;">No file uploaded</span>
                @endif
                @if($bankFilled)
                    <button class="kyc-verify-btn" data-uid="{{ $user->uid }}" data-type="bank" data-verified="{{ $user->bank_verified ? '1' : '0' }}" style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;border:1.5px solid {{ $user->bank_verified ? '#c62828' : '#87b942' }};background:{{ $user->bank_verified ? '#fff0f0' : '#f0f8e8' }};color:{{ $user->bank_verified ? '#c62828' : '#4a7c20' }};">
                        @if($user->bank_verified) <i class="fa-solid fa-xmark"></i> Unverify @else <i class="fa-solid fa-check"></i> Verify @endif
                    </button>
                @endif
            </div>
        </div>

        {{-- ── DEMAT ── --}}
        @php $dematFilled = (bool) $user->demat_dp_id; @endphp
        <div style="border:1px solid #ebebeb;border-radius:12px;overflow:hidden;">
            <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:#fafafa;border-bottom:1px solid #ebebeb;">
                <div style="width:36px;height:36px;border-radius:10px;background:#e3f2fd;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fa-solid fa-chart-line" style="color:#1565c0;font-size:16px"></i>
                </div>
                <div style="flex:1;font-size:13px;font-weight:700;color:#1a1a1a;font-family:'Inter',sans-serif;">Demat — CML Copy</div>
                @if($user->demat_verified)
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#e8f5e9;color:#2e7d32;border:1px solid #a5d6a7;white-space:nowrap;font-family:'Inter',sans-serif;"><i class="fa-solid fa-circle-check"></i> Verified</span>
                @elseif($dematFilled)
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#fff8e1;color:#f57f17;border:1px solid #ffe082;white-space:nowrap;font-family:'Inter',sans-serif;"><i class="fa-regular fa-clock"></i> Pending</span>
                @else
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#f5f5f5;color:#aaa;border:1px solid #e0e0e0;white-space:nowrap;font-family:'Inter',sans-serif;"><i class="fa-solid fa-circle-minus"></i> None</span>
                @endif
            </div>
            @if($dematFilled)
            <div style="padding:14px 16px;display:grid;grid-template-columns:1fr 1fr;gap:12px 20px;">
                <div>
                    <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#999;margin-bottom:3px;font-family:'Inter',sans-serif;">DP ID</div>
                    <div style="font-size:13px;color:#1a1a1a;font-family:'Inter',sans-serif;font-weight:500;letter-spacing:0.04em;">{{ $user->demat_dp_id }}</div>
                </div>
                <div>
                    <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#999;margin-bottom:3px;font-family:'Inter',sans-serif;">DP Name</div>
                    <div style="font-size:13px;color:#1a1a1a;font-family:'Inter',sans-serif;font-weight:500;">{{ $user->demat_dp_name ?: '—' }}</div>
                </div>
            </div>
            @else
            <div style="padding:12px 16px;font-size:12px;color:#bbb;font-family:'Inter',sans-serif;">Not submitted</div>
            @endif
            <div style="display:flex;gap:8px;flex-wrap:wrap;padding:0 16px 14px;">
                @if($user->demat_cml_copy)
                    <a href="{{ route('admin.admin.users.kyc', ['uid' => $user->uid, 'type' => 'demat']) }}" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:7px;border:1.5px solid #1565c0;background:#fff;color:#1565c0;font-size:12px;font-weight:600;text-decoration:none;font-family:'Inter',sans-serif;" onmouseover="this.style.background='#e3f2fd'" onmouseout="this.style.background='#fff'"><i class="fa-regular fa-eye"></i> View</a>
                    <a href="{{ route('admin.admin.users.kyc', ['uid' => $user->uid, 'type' => 'demat']) }}" download style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:7px;border:1.5px solid #555;background:#fff;color:#555;font-size:12px;font-weight:600;text-decoration:none;font-family:'Inter',sans-serif;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='#fff'"><i class="fa-solid fa-download"></i> Download</a>
                @else
                    <span style="font-size:12px;color:#bbb;font-family:'Inter',sans-serif;padding:6px 0;">No file uploaded</span>
                @endif
                @if($dematFilled)
                    <button class="kyc-verify-btn" data-uid="{{ $user->uid }}" data-type="demat" data-verified="{{ $user->demat_verified ? '1' : '0' }}" style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;border:1.5px solid {{ $user->demat_verified ? '#c62828' : '#87b942' }};background:{{ $user->demat_verified ? '#fff0f0' : '#f0f8e8' }};color:{{ $user->demat_verified ? '#c62828' : '#4a7c20' }};">
                        @if($user->demat_verified) <i class="fa-solid fa-xmark"></i> Unverify @else <i class="fa-solid fa-check"></i> Verify @endif
                    </button>
                @endif
            </div>
        </div>

        {{-- ── PAN ── --}}
        @php $panFilled = (bool) $user->user_pan_no; @endphp
        <div style="border:1px solid #ebebeb;border-radius:12px;overflow:hidden;">
            <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:#fafafa;border-bottom:1px solid #ebebeb;">
                <div style="width:36px;height:36px;border-radius:10px;background:#fff8e1;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fa-solid fa-id-card" style="color:#f57f17;font-size:16px"></i>
                </div>
                <div style="flex:1;font-size:13px;font-weight:700;color:#1a1a1a;font-family:'Inter',sans-serif;">PAN Card</div>
                @if($user->user_pan_verified)
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#e8f5e9;color:#2e7d32;border:1px solid #a5d6a7;white-space:nowrap;font-family:'Inter',sans-serif;"><i class="fa-solid fa-circle-check"></i> Verified</span>
                @elseif($panFilled)
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#fff8e1;color:#f57f17;border:1px solid #ffe082;white-space:nowrap;font-family:'Inter',sans-serif;"><i class="fa-regular fa-clock"></i> Pending</span>
                @else
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#f5f5f5;color:#aaa;border:1px solid #e0e0e0;white-space:nowrap;font-family:'Inter',sans-serif;"><i class="fa-solid fa-circle-minus"></i> None</span>
                @endif
            </div>
            @if($panFilled)
            <div style="padding:14px 16px;">
                <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#999;margin-bottom:3px;font-family:'Inter',sans-serif;">PAN Number</div>
                <div style="font-size:15px;color:#1a1a1a;font-family:'Inter',sans-serif;font-weight:600;letter-spacing:0.12em;">{{ $user->user_pan_no }}</div>
            </div>
            @else
            <div style="padding:12px 16px;font-size:12px;color:#bbb;font-family:'Inter',sans-serif;">Not submitted</div>
            @endif
            <div style="display:flex;gap:8px;flex-wrap:wrap;padding:0 16px 14px;">
                @if($user->user_pan_image)
                    <a href="{{ route('admin.admin.users.kyc', ['uid' => $user->uid, 'type' => 'pan']) }}" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:7px;border:1.5px solid #1565c0;background:#fff;color:#1565c0;font-size:12px;font-weight:600;text-decoration:none;font-family:'Inter',sans-serif;" onmouseover="this.style.background='#e3f2fd'" onmouseout="this.style.background='#fff'"><i class="fa-regular fa-eye"></i> View</a>
                    <a href="{{ route('admin.admin.users.kyc', ['uid' => $user->uid, 'type' => 'pan']) }}" download style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:7px;border:1.5px solid #555;background:#fff;color:#555;font-size:12px;font-weight:600;text-decoration:none;font-family:'Inter',sans-serif;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='#fff'"><i class="fa-solid fa-download"></i> Download</a>
                @else
                    <span style="font-size:12px;color:#bbb;font-family:'Inter',sans-serif;padding:6px 0;">No file uploaded</span>
                @endif
                @if($panFilled)
                    <button class="kyc-verify-btn" data-uid="{{ $user->uid }}" data-type="pan" data-verified="{{ $user->user_pan_verified ? '1' : '0' }}" style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;border:1.5px solid {{ $user->user_pan_verified ? '#c62828' : '#87b942' }};background:{{ $user->user_pan_verified ? '#fff0f0' : '#f0f8e8' }};color:{{ $user->user_pan_verified ? '#c62828' : '#4a7c20' }};">
                        @if($user->user_pan_verified) <i class="fa-solid fa-xmark"></i> Unverify @else <i class="fa-solid fa-check"></i> Verify @endif
                    </button>
                @endif
            </div>
        </div>

    </div>
</div>

<script>
$(document).on('click', '.kyc-verify-btn', function () {
    var $btn     = $(this);
    var uid      = $btn.data('uid');
    var type     = $btn.data('type');
    var verified = $btn.data('verified') === '1' || $btn.data('verified') === 1;
    var newVal   = verified ? 0 : 1;

    $btn.prop('disabled', true).css('opacity', '0.6');

    $.ajax({
        url:     '{{ url("/admin/users") }}/' + uid + '/kyc/' + type + '/verify',
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data:    { verified: newVal },
    })
    .done(function (res) {
        if (res.success) {
            $('#kycDocsModalWrap').html('<div class="priv-loading">Loading…</div>');
            $.get('{{ url("/admin/users") }}/' + uid + '/kyc-docs')
                .done(function (html) { $('#kycDocsModalWrap').html(html); });

            var $tableBtn = $('.kyc-docs-btn[data-uid="' + uid + '"]');
            if (res.all_verified) {
                $tableBtn.html('<i class="fa-solid fa-circle-check" style="color:#4a7c20;margin-right:4px"></i> Verified');
            } else {
                $tableBtn.html('<i class="fa-regular fa-clock" style="color:#f57f17;margin-right:4px"></i> Pending');
            }
        }
    })
    .fail(function () { alert('Something went wrong.'); })
    .always(function () { $btn.prop('disabled', false).css('opacity', '1'); });
});
</script>
