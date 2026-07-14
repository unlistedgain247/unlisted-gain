@php
    $p       = $user->privilege ?? [];
    $ul      = $p['unlisted'] ?? [];
    $pg      = $p['pg'] ?? [];
    $initials = collect(explode(' ', $user->name))->take(2)->map(fn($w) => strtoupper($w[0]))->join('');
    $checked  = fn(bool $val) => $val ? 'checked' : '';
@endphp

<div class="priv-modal">

    {{-- Header --}}
    <div class="priv-modal-header">
        <div class="priv-modal-title">
            <div class="priv-avatar">{{ $initials }}</div>
            <div>
                <span class="priv-modal-label">Manage Privileges</span>
                <h3>{{ $user->name }}</h3>
            </div>
        </div>
        <button class="priv-modal-close" id="privModalCloseBtn" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>

    <form id="privForm">
        <input type="hidden" name="uid" value="{{ $user->uid }}">

        <div class="priv-body">

            {{-- Admin toggle --}}
            <div class="priv-section">
                <div class="priv-section-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    Access Level
                </div>

                <label class="priv-toggle-row priv-toggle-featured">
                    <div class="priv-toggle-info">
                        <span class="priv-toggle-name">Admin</span>
                        <span class="priv-toggle-desc">Full dashboard access</span>
                    </div>
                    <div class="priv-toggle-wrap">
                        <span class="priv-super-badge">Super</span>
                        <label class="priv-switch">
                            <input type="checkbox" name="admin" {{ $checked(!empty($p['admin'])) }}>
                            <span class="priv-slider"></span>
                        </label>
                    </div>
                </label>
            </div>

            {{-- Modules --}}
            <div class="priv-section">
                <div class="priv-section-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                        <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
                    </svg>
                    Modules
                </div>

                <label class="priv-toggle-row">
                    <div class="priv-toggle-info">
                        <span class="priv-toggle-name">User Master</span>
                        <span class="priv-toggle-desc">View & manage all users</span>
                    </div>
                    <label class="priv-switch">
                        <input type="checkbox" name="user_master" {{ $checked(!empty($p['user_master'])) }}>
                        <span class="priv-slider"></span>
                    </label>
                </label>
            </div>

            {{-- Unlisted Access --}}
            <div class="priv-section">
                <div class="priv-section-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>
                    </svg>
                    Unlisted Access
                </div>

                <div class="priv-pill-grid">
                    @php
                        $unlistedItems = [
                            'unlisted_stockx'            => ['label' => 'Stocks',           'key' => 'stockx'],
                            'unlisted_leads'             => ['label' => 'Leads',            'key' => 'leads'],
                            'unlisted_leads_allocation'  => ['label' => 'Leads Allocation', 'key' => 'leads_allocation'],
                            'unlisted_orders'            => ['label' => 'Orders',           'key' => 'orders'],
                            'unlisted_unlisted_reports'  => ['label' => 'Unlisted Reports', 'key' => 'unlisted_reports'],
                            'unlisted_order_backend'     => ['label' => 'Order Backend',    'key' => 'order_backend'],
                        ];
                    @endphp

                    @foreach($unlistedItems as $name => $item)
                        <label class="priv-pill {{ !empty($ul[$item['key']]) ? 'active' : '' }}">
                            <input type="checkbox" name="{{ $name }}" {{ $checked(!empty($ul[$item['key']])) }}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            {{ $item['label'] }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- PG Access --}}
            <div class="priv-section">
                <div class="priv-section-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                    PG Access
                </div>

                <div class="priv-pill-grid">
                    @php
                        $pgItems = [
                            'pg_dashboard'    => ['label' => 'Dashboard',    'key' => 'dashboard'],
                            'pg_margin'       => ['label' => 'Margin',       'key' => 'margin'],
                            'pg_margin_error' => ['label' => 'Margin Error', 'key' => 'margin_error'],
                            'pg_transactions' => ['label' => 'Transactions', 'key' => 'transactions'],
                        ];
                    @endphp

                    @foreach($pgItems as $name => $item)
                        <label class="priv-pill {{ !empty($pg[$item['key']]) ? 'active' : '' }}">
                            <input type="checkbox" name="{{ $name }}" {{ $checked(!empty($pg[$item['key']])) }}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            {{ $item['label'] }}
                        </label>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="priv-modal-footer">
            <span id="privSaveMsg" class="priv-save-msg"></span>
            <button type="submit" class="priv-save-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                </svg>
                Save Privileges
            </button>
        </div>

    </form>
</div>
