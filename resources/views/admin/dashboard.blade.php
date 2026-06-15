@extends('layout.admin')

@section('title', 'Admin Dashboard | UnlistedGain')

@section('content')
<div class="admin-main">

    <h1 class="admin-page-title">Dashboard</h1>

    <div class="admin-stats">
        <div class="admin-stat-card">
            <span class="stat-number">{{ $totalUsers }}</span>
            <span class="stat-label">Total Users</span>
        </div>
        <div class="admin-stat-card">
            <span class="stat-number">{{ $adminUsers }}</span>
            <span class="stat-label">Admin Users</span>
        </div>
        <div class="admin-stat-card">
            <span class="stat-number">{{ $memberUsers }}</span>
            <span class="stat-label">Members</span>
        </div>
    </div>

    <div class="admin-card">
        <p style="color:#888;margin:0">Welcome to the admin panel. Use the navigation tabs above to manage different sections.</p>
    </div>

</div>
@endsection
