@extends('layout.admin')

@section('title', 'Users | Admin | UnlistedGain')

@section('content')
<div class="admin-main">

    <h1 class="admin-page-title">Users</h1>

    <div class="admin-card">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>User Type</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th>KYC Docs</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>
                            <span class="admin-badge badge-{{ $user->user_type }}">
                                {{ $user->user_type }}
                            </span>
                        </td>
                        <td>{{ $user->created_at ? $user->created_at->format('d M Y') : '—' }}</td>
                        <td>
                            @php
                                $locked = $user->login_locked_until && $user->login_locked_until->isFuture();
                            @endphp
                            @if($locked)
                                <span class="admin-badge badge-locked" title="Locked until {{ $user->login_locked_until->format('d M Y, h:i A') }}">
                                    Locked
                                </span>
                            @elseif($user->failed_login_attempts > 0)
                                <span class="admin-badge badge-warn" title="{{ $user->failed_login_attempts }} failed attempt(s)">
                                    {{ $user->failed_login_attempts }} fail(s)
                                </span>
                            @else
                                <span style="color:#aaa;font-size:12px">OK</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $kycDone = $user->bank_account_no || $user->demat_dp_id || $user->user_pan_no;
                                $kycVerified = $user->bank_verified && $user->demat_verified && $user->user_pan_verified;
                            @endphp
                            <button class="kyc-docs-btn"
                                    data-uid="{{ $user->uid }}"
                                    title="View KYC Documents">
                                @if($kycVerified)
                                    <i class="fa-solid fa-circle-check" style="color:#4a7c20;margin-right:4px"></i> Verified
                                @elseif($kycDone)
                                    <i class="fa-regular fa-clock" style="color:#f57f17;margin-right:4px"></i> Pending
                                @else
                                    <i class="fa-solid fa-circle-minus" style="color:#bbb;margin-right:4px"></i> None
                                @endif
                            </button>
                        </td>
                        <td style="display:flex;gap:6px;flex-wrap:wrap">
                            <button class="priv-btn" data-uid="{{ $user->uid }}">
                                Privileges
                            </button>
                            @if($locked || $user->failed_login_attempts > 0)
                                <button class="reset-btn"
                                        data-uid="{{ $user->uid }}"
                                        data-name="{{ $user->name }}">
                                    Reset Lock
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align:center;color:#aaa;padding:32px">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($users) && method_exists($users, 'links'))
            <div style="margin-top:16px">{{ $users->links() }}</div>
        @endif
    </div>

</div>

{{-- Privilege modal --}}
<div id="privOverlay" class="priv-overlay">
    <div id="privModalWrap"></div>
</div>

{{-- KYC Docs modal --}}
<div id="kycDocsOverlay" class="priv-overlay" style="align-items:flex-start;overflow-y:auto;padding:24px 16px;">
    <div id="kycDocsModalWrap" style="width:100%;max-width:780px;margin:0 auto;"></div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    const BASE = '{{ url("/admin/users") }}';

    // ── Open modal ─────────────────────────────────────────
    $(document).on('click', '.priv-btn', function () {
        const uid = $(this).data('uid');

        $('#privModalWrap').html('<div class="priv-loading">Loading…</div>');
        $('#privOverlay').addClass('open');

        $.get(BASE + '/' + uid + '/privilege')
            .done(function (html) {
                $('#privModalWrap').html(html);
            })
            .fail(function () {
                $('#privModalWrap').html('<div class="priv-loading" style="color:#e53935">Failed to load. Please try again.</div>');
            });
    });

    // ── Close modal ────────────────────────────────────────
    $(document).on('click', '#privOverlay', function (e) {
        if ($(e.target).is('#privOverlay')) closeModal();
    });

    $(document).on('click', '#privModalCloseBtn', function () {
        closeModal();
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });

    function closeModal() {
        $('#privOverlay').removeClass('open');
        $('#privModalWrap').empty();
    }

    // ── Pill toggle active class ───────────────────────────
    $(document).on('change', '.priv-pill input', function () {
        $(this).closest('.priv-pill').toggleClass('active', this.checked);
    });

    // ── Reset lockout ──────────────────────────────────────
    $(document).on('click', '.reset-btn', function () {
        const uid  = $(this).data('uid');
        const name = $(this).data('name');
        const $btn = $(this);

        if (!confirm('Reset login lock and failed attempts for ' + name + '?')) return;

        $.ajax({
            url:     BASE + '/' + uid + '/reset-lockout',
            method:  'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        })
        .done(function (res) {
            if (res.success) {
                const $row = $btn.closest('tr');
                $btn.remove();
                $row.find('.admin-badge.badge-locked, .admin-badge.badge-warn')
                    .replaceWith('<span style="color:#aaa;font-size:12px">OK</span>');
            } else {
                alert(res.message || 'Failed to reset.');
            }
        })
        .fail(function () { alert('Something went wrong.'); });
    });

    // ── KYC Docs modal ────────────────────────────────────
    $(document).on('click', '.kyc-docs-btn', function () {
        const uid = $(this).data('uid');
        $('#kycDocsModalWrap').html('<div class="priv-loading">Loading…</div>');
        $('#kycDocsOverlay').addClass('open');
        $.get(BASE + '/' + uid + '/kyc-docs')
            .done(function (html) { $('#kycDocsModalWrap').html(html); })
            .fail(function ()     { $('#kycDocsModalWrap').html('<div class="priv-loading" style="color:#e53935">Failed to load.</div>'); });
    });

    $(document).on('click', '#kycDocsOverlay', function (e) {
        if ($(e.target).is('#kycDocsOverlay')) closeKycDocsModal();
    });

    function closeKycDocsModal() {
        $('#kycDocsOverlay').removeClass('open');
        $('#kycDocsModalWrap').empty();
    }

    window.closeKycDocsModal = closeKycDocsModal;

    // ── Save privileges ────────────────────────────────────
    $(document).on('submit', '#privForm', function (e) {
        e.preventDefault();

        const uid  = $(this).find('[name=uid]').val();
        const data = {};
        $(this).find('input[type=checkbox]').each(function () {
            data[this.name] = this.checked;
        });

        $.ajax({
            url:         BASE + '/' + uid + '/privilege',
            method:      'POST',
            contentType: 'application/json',
            headers:     { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data:        JSON.stringify(data),
        })
        .done(function (res) {
            $('#privSaveMsg').text(res.message || 'Saved.').css('color', '#4a7c20');
        })
        .fail(function () {
            $('#privSaveMsg').text('Error saving privileges.').css('color', '#e53935');
        });
    });
});
</script>
@endpush
