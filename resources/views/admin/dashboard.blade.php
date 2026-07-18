@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">Admin Dashboard</div>
        <div class="page-subtitle">{{ now()->format('l, F j, Y') }} &nbsp;·&nbsp; Logged in as <strong>{{ Auth::user()->name }}</strong></div>
    </div>
    <a href="{{ route('admin.applications') }}" class="btn btn-primary">📋 View All Applications</a>
</div>

@if(session('error'))
    <div class="alert alert-error">⚠️ {{ session('error') }}</div>
@endif

{{-- Quick Stats --}}
@php
    $totalApps    = \App\Models\ParkingApplication::count();
    $pendingApps  = \App\Models\ParkingApplication::where('status','pending')->count();
    $approvedApps = \App\Models\ParkingApplication::where('status','approved')->count();
    $rejectedApps = \App\Models\ParkingApplication::where('status','rejected')->count();
@endphp

<div class="stat-grid" style="margin-bottom:1.75rem;">
    <div class="stat-card orange-card">
        <div class="stat-icon orange">📋</div>
        <div>
            <div class="stat-value">{{ $totalApps }}</div>
            <div class="stat-label">Total Applications</div>
        </div>
    </div>
    <div class="stat-card orange-card">
        <div class="stat-icon orange">⏳</div>
        <div>
            <div class="stat-value">{{ $pendingApps }}</div>
            <div class="stat-label">Awaiting Review</div>
        </div>
    </div>
    <div class="stat-card green-card">
        <div class="stat-icon green">✅</div>
        <div>
            <div class="stat-value">{{ $approvedApps }}</div>
            <div class="stat-label">Approved</div>
        </div>
    </div>
    <div class="stat-card red-card">
        <div class="stat-icon red">❌</div>
        <div>
            <div class="stat-value">{{ $rejectedApps }}</div>
            <div class="stat-label">Rejected</div>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; align-items:start;">
    {{-- Admin Info Card --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">🔐 Session Information</span>
        </div>
        <div class="card-body" style="padding:0;">
            <table style="width:100%; border-collapse:collapse; font-size:.9rem;">
                <tbody>
                    <tr style="border-bottom:1px solid var(--gray-100);">
                        <td style="padding:.85rem 1.25rem; font-weight:700; color:var(--gray-500); width:130px; font-size:.78rem; text-transform:uppercase; letter-spacing:.05em;">Name</td>
                        <td style="padding:.85rem 1.25rem; color:var(--dark); font-weight:600;">{{ Auth::user()->name }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100);">
                        <td style="padding:.85rem 1.25rem; font-weight:700; color:var(--gray-500); font-size:.78rem; text-transform:uppercase; letter-spacing:.05em;">Email</td>
                        <td style="padding:.85rem 1.25rem; color:var(--gray-700);">{{ Auth::user()->email }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--gray-100);">
                        <td style="padding:.85rem 1.25rem; font-weight:700; color:var(--gray-500); font-size:.78rem; text-transform:uppercase; letter-spacing:.05em;">Role</td>
                        <td style="padding:.85rem 1.25rem;">
                            <span class="badge" style="background:linear-gradient(135deg,rgba(249,115,22,.15),rgba(234,88,12,.1)); color:var(--orange-dark); border:1px solid var(--orange-light); text-transform:uppercase; letter-spacing:.05em;">
                                {{ Auth::user()->role }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:.85rem 1.25rem; font-weight:700; color:var(--gray-500); font-size:.78rem; text-transform:uppercase; letter-spacing:.05em;">Session ID</td>
                        <td style="padding:.85rem 1.25rem; color:var(--gray-400); font-family:monospace; font-size:.8rem;">{{ substr(session()->getId(), 0, 20) }}…</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">⚡ Quick Actions</span>
        </div>
        <div class="card-body" style="display:flex; flex-direction:column; gap:.75rem;">
            <a href="{{ route('admin.applications') }}"
               style="display:flex; align-items:center; gap:1rem; padding:1rem 1.25rem;
                      background:var(--gray-50); border:1.5px solid var(--gray-200); border-radius:var(--radius-sm);
                      text-decoration:none; color:var(--dark); transition:all .18s; font-weight:600;"
               onmouseover="this.style.borderColor='var(--orange)'; this.style.background='var(--orange-pale)'; this.style.color='var(--orange-dark)'"
               onmouseout="this.style.borderColor='var(--gray-200)'; this.style.background='var(--gray-50)'; this.style.color='var(--dark)'">
                <span style="font-size:1.5rem;">📋</span>
                <div>
                    <div style="font-size:.9rem;">Applications</div>
                    <div style="font-size:.75rem; color:var(--gray-400); font-weight:400; margin-top:.1rem;">{{ $pendingApps }} pending review</div>
                </div>
                <span style="margin-left:auto; color:var(--gray-300); font-size:1.1rem;">›</span>
            </a>
            <a href="{{ route('admin.dashboard') }}"
               style="display:flex; align-items:center; gap:1rem; padding:1rem 1.25rem;
                      background:var(--gray-50); border:1.5px solid var(--gray-200); border-radius:var(--radius-sm);
                      text-decoration:none; color:var(--dark); transition:all .18s; font-weight:600;"
               onmouseover="this.style.borderColor='var(--orange)'; this.style.background='var(--orange-pale)'; this.style.color='var(--orange-dark)'"
               onmouseout="this.style.borderColor='var(--gray-200)'; this.style.background='var(--gray-50)'; this.style.color='var(--dark)'">
                <span style="font-size:1.5rem;">🔄</span>
                <div>
                    <div style="font-size:.9rem;">Refresh Dashboard</div>
                    <div style="font-size:.75rem; color:var(--gray-400); font-weight:400; margin-top:.1rem;">Reload stats and session info</div>
                </div>
                <span style="margin-left:auto; color:var(--gray-300); font-size:1.1rem;">›</span>
            </a>
        </div>
    </div>
</div>
@endsection
