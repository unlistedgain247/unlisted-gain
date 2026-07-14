@php
    $bankFilled  = (bool) $user->bank_account_no;
    $dematFilled = (bool) $user->demat_dp_id;
    $panFilled   = (bool) $user->user_pan_no;
    $kycBase     = url('/admin/users') . '/' . $user->uid . '/kyc/';
@endphp
<div style="display:flex;flex-direction:column;gap:14px;padding:4px 0;">

    {{-- Bank --}}
    <div style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;">
        <div style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#fafafa;border-bottom:1px solid #e5e7eb;">
            <div style="width:32px;height:32px;border-radius:8px;background:#e8f5e9;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fa-solid fa-building-columns" style="color:#2e7d32;font-size:14px;"></i>
            </div>
            <div style="flex:1;font-size:13px;font-weight:700;color:#111;">Bank — Cancelled Cheque</div>
            @if($user->bank_verified)
                <span style="padding:2px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#e8f5e9;color:#2e7d32;border:1px solid #a5d6a7;">
                    <i class="fa-solid fa-circle-check"></i> Verified
                </span>
            @elseif($bankFilled)
                <span style="padding:2px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#fff8e1;color:#f57f17;border:1px solid #ffe082;">
                    <i class="fa-regular fa-clock"></i> Pending
                </span>
            @else
                <span style="padding:2px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#f5f5f5;color:#aaa;border:1px solid #e0e0e0;">
                    <i class="fa-solid fa-circle-minus"></i> None
                </span>
            @endif
        </div>
        @if($bankFilled)
        <div style="padding:12px 14px;display:grid;grid-template-columns:1fr 1fr;gap:10px 18px;font-size:12px;">
            <div>
                <div style="font-size:10px;font-weight:600;text-transform:uppercase;color:#999;margin-bottom:2px;">Account Holder</div>
                <div style="color:#111;font-weight:500;">{{ $user->bank_holder_name ?: '—' }}</div>
            </div>
            <div>
                <div style="font-size:10px;font-weight:600;text-transform:uppercase;color:#999;margin-bottom:2px;">Bank Name</div>
                <div style="color:#111;font-weight:500;">{{ $user->bank_name ?: '—' }}</div>
            </div>
            <div>
                <div style="font-size:10px;font-weight:600;text-transform:uppercase;color:#999;margin-bottom:2px;">Account Number</div>
                <div style="color:#111;font-weight:500;letter-spacing:0.04em;">{{ $user->bank_account_no }}</div>
            </div>
            <div>
                <div style="font-size:10px;font-weight:600;text-transform:uppercase;color:#999;margin-bottom:2px;">IFSC Code</div>
                <div style="color:#111;font-weight:500;letter-spacing:0.04em;">{{ $user->bank_ifsc_code ?: '—' }}</div>
            </div>
        </div>
        @else
        <div style="padding:10px 14px;font-size:12px;color:#bbb;">Not submitted</div>
        @endif
        <div style="display:flex;gap:8px;flex-wrap:wrap;padding:0 14px 12px;">
            @if($user->bank_cancelled_check)
                <a href="{{ $kycBase }}bank" target="_blank" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:6px;border:1.5px solid #1565c0;background:#fff;color:#1565c0;font-size:11px;font-weight:600;text-decoration:none;">
                    <i class="fa-regular fa-eye"></i> View
                </a>
                <a href="{{ $kycBase }}bank" download style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:6px;border:1.5px solid #555;background:#fff;color:#555;font-size:11px;font-weight:600;text-decoration:none;">
                    <i class="fa-solid fa-download"></i> Download
                </a>
            @else
                <span style="font-size:12px;color:#bbb;padding:5px 0;">No file uploaded</span>
            @endif
        </div>
    </div>

    {{-- Demat --}}
    <div style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;">
        <div style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#fafafa;border-bottom:1px solid #e5e7eb;">
            <div style="width:32px;height:32px;border-radius:8px;background:#e3f2fd;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fa-solid fa-chart-line" style="color:#1565c0;font-size:14px;"></i>
            </div>
            <div style="flex:1;font-size:13px;font-weight:700;color:#111;">Demat — CML Copy</div>
            @if($user->demat_verified)
                <span style="padding:2px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#e8f5e9;color:#2e7d32;border:1px solid #a5d6a7;">
                    <i class="fa-solid fa-circle-check"></i> Verified
                </span>
            @elseif($dematFilled)
                <span style="padding:2px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#fff8e1;color:#f57f17;border:1px solid #ffe082;">
                    <i class="fa-regular fa-clock"></i> Pending
                </span>
            @else
                <span style="padding:2px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#f5f5f5;color:#aaa;border:1px solid #e0e0e0;">
                    <i class="fa-solid fa-circle-minus"></i> None
                </span>
            @endif
        </div>
        @if($dematFilled)
        <div style="padding:12px 14px;display:grid;grid-template-columns:1fr 1fr;gap:10px 18px;font-size:12px;">
            <div>
                <div style="font-size:10px;font-weight:600;text-transform:uppercase;color:#999;margin-bottom:2px;">DP ID</div>
                <div style="color:#111;font-weight:500;letter-spacing:0.04em;">{{ $user->demat_dp_id }}</div>
            </div>
            <div>
                <div style="font-size:10px;font-weight:600;text-transform:uppercase;color:#999;margin-bottom:2px;">DP Name</div>
                <div style="color:#111;font-weight:500;">{{ $user->demat_dp_name ?: '—' }}</div>
            </div>
        </div>
        @else
        <div style="padding:10px 14px;font-size:12px;color:#bbb;">Not submitted</div>
        @endif
        <div style="display:flex;gap:8px;flex-wrap:wrap;padding:0 14px 12px;">
            @if($user->demat_cml_copy)
                <a href="{{ $kycBase }}demat" target="_blank" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:6px;border:1.5px solid #1565c0;background:#fff;color:#1565c0;font-size:11px;font-weight:600;text-decoration:none;">
                    <i class="fa-regular fa-eye"></i> View
                </a>
                <a href="{{ $kycBase }}demat" download style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:6px;border:1.5px solid #555;background:#fff;color:#555;font-size:11px;font-weight:600;text-decoration:none;">
                    <i class="fa-solid fa-download"></i> Download
                </a>
            @else
                <span style="font-size:12px;color:#bbb;padding:5px 0;">No file uploaded</span>
            @endif
        </div>
    </div>

    {{-- PAN --}}
    <div style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;">
        <div style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#fafafa;border-bottom:1px solid #e5e7eb;">
            <div style="width:32px;height:32px;border-radius:8px;background:#fff8e1;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fa-solid fa-id-card" style="color:#f57f17;font-size:14px;"></i>
            </div>
            <div style="flex:1;font-size:13px;font-weight:700;color:#111;">PAN Card</div>
            @if($user->user_pan_verified)
                <span style="padding:2px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#e8f5e9;color:#2e7d32;border:1px solid #a5d6a7;">
                    <i class="fa-solid fa-circle-check"></i> Verified
                </span>
            @elseif($panFilled)
                <span style="padding:2px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#fff8e1;color:#f57f17;border:1px solid #ffe082;">
                    <i class="fa-regular fa-clock"></i> Pending
                </span>
            @else
                <span style="padding:2px 9px;border-radius:20px;font-size:11px;font-weight:600;background:#f5f5f5;color:#aaa;border:1px solid #e0e0e0;">
                    <i class="fa-solid fa-circle-minus"></i> None
                </span>
            @endif
        </div>
        @if($panFilled)
        <div style="padding:12px 14px;font-size:12px;">
            <div style="font-size:10px;font-weight:600;text-transform:uppercase;color:#999;margin-bottom:2px;">PAN Number</div>
            <div style="font-size:15px;color:#111;font-weight:700;letter-spacing:0.12em;">{{ $user->user_pan_no }}</div>
        </div>
        @else
        <div style="padding:10px 14px;font-size:12px;color:#bbb;">Not submitted</div>
        @endif
        <div style="display:flex;gap:8px;flex-wrap:wrap;padding:0 14px 12px;">
            @if($user->user_pan_image)
                <a href="{{ $kycBase }}pan" target="_blank" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:6px;border:1.5px solid #1565c0;background:#fff;color:#1565c0;font-size:11px;font-weight:600;text-decoration:none;">
                    <i class="fa-regular fa-eye"></i> View
                </a>
                <a href="{{ $kycBase }}pan" download style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:6px;border:1.5px solid #555;background:#fff;color:#555;font-size:11px;font-weight:600;text-decoration:none;">
                    <i class="fa-solid fa-download"></i> Download
                </a>
            @else
                <span style="font-size:12px;color:#bbb;padding:5px 0;">No file uploaded</span>
            @endif
        </div>
    </div>

</div>
