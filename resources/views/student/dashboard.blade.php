@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">Welcome back, {{ Auth::user()->name }} 👋</div>
        <div class="page-subtitle">
            {{ ucfirst(Auth::user()->role) }} &nbsp;•&nbsp; {{ Auth::user()->email }}
            @if(Auth::user()->university_id) &nbsp;•&nbsp; ID: {{ Auth::user()->university_id }} @endif
        </div>
    </div>
    <a href="{{ route('student.apply') }}" class="btn btn-primary">➕ New Application</a>
</div>

<!-- Stats -->
<div class="stat-grid">
    <div class="stat-card orange-card">
        <div class="stat-icon orange">📋</div>
        <div><div class="stat-value">{{ $overview['total'] }}</div><div class="stat-label">Total Applications</div></div>
    </div>
    <div class="stat-card green-card">
        <div class="stat-icon green">✅</div>
        <div><div class="stat-value">{{ $overview['approved'] }}</div><div class="stat-label">Approved</div></div>
    </div>
    <div class="stat-card orange-card">
        <div class="stat-icon orange">⏳</div>
        <div><div class="stat-value">{{ $overview['pending'] }}</div><div class="stat-label">Pending Review</div></div>
    </div>
    <div class="stat-card red-card">
        <div class="stat-icon red">❌</div>
        <div><div class="stat-value">{{ $overview['rejected'] }}</div><div class="stat-label">Rejected</div></div>
    </div>
</div>

{{-- Lab 12: Open-Meteo weather widget + AJAX search --}}
@if($weather)
<div class="card" style="margin-bottom:1.5rem; background:linear-gradient(135deg,#1e3a5f,#2d6a9f); color:white; border:none;">
    <div class="card-body" style="padding:1.5rem;">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
            {{-- Left: current weather --}}
            <div style="display:flex; align-items:center; gap:1.5rem;">
                <div style="font-size:3.5rem; line-height:1;">{{ $weather['is_day'] ? '☀️' : '🌙' }}</div>
                <div>
                    <div style="font-size:2.5rem; font-weight:800; line-height:1;">{{ $weather['temperature'] }}°C</div>
                    <div style="font-size:1rem; opacity:.9; margin-top:.25rem;">{{ $weather['description'] }}</div>
                    <div style="font-size:.85rem; opacity:.7; margin-top:.2rem;">📍 {{ $weather['city'] }}, {{ $weather['country'] }}</div>
                    <div style="font-size:.8rem; opacity:.6; margin-top:.1rem;">💨 Wind: {{ $weather['windspeed'] }} km/h</div>
                </div>
            </div>
            {{-- Right: AJAX search (Lab 12 Task 3) --}}
            <div style="flex:1; min-width:250px; max-width:360px;">
                <div style="font-size:.8rem; opacity:.7; margin-bottom:.5rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em;">🔍 AJAX City Search</div>
                <form id="weatherAjaxForm" data-url="{{ route('weather.json') }}" style="display:flex; gap:.5rem;">
                    <input type="text" id="weatherCityInput" placeholder="e.g. Dhaka, London…"
                        style="flex:1; padding:.6rem .9rem; border:none; border-radius:8px; font-family:inherit;
                               font-size:.9rem; background:rgba(255,255,255,.15); color:white;
                               outline:none; transition:background .2s;"
                        onfocus="this.style.background='rgba(255,255,255,.25)'"
                        onblur="this.style.background='rgba(255,255,255,.15)'">
                    <button type="submit" style="padding:.6rem 1rem; background:rgba(255,255,255,.2);
                        border:1.5px solid rgba(255,255,255,.4); color:white; border-radius:8px;
                        font-family:inherit; font-size:.9rem; font-weight:600; cursor:pointer;
                        transition:all .2s;"
                        onmouseover="this.style.background='rgba(255,255,255,.35)'"
                        onmouseout="this.style.background='rgba(255,255,255,.2)'">Search</button>
                </form>
                <div id="weatherAjaxResult" style="margin-top:.75rem; font-size:.875rem;"></div>
            </div>
        </div>
        <div style="margin-top:.75rem; font-size:.7rem; opacity:.5;">Powered by Open-Meteo (free, open-source) · Updates on page load</div>
    </div>
</div>
@endif

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem;">
    <!-- Latest Application -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Latest Application</span>
            <a href="{{ route('student.applications') }}" class="btn btn-outline btn-sm">View All</a>
        </div>
        <div class="card-body">
            @if($latest)
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
                    <span style="font-weight:700; font-size:1.05rem;">Application #{{ $latest->id }}</span>
                    @php $s = $latest->status; @endphp
                    <span class="badge badge-{{ $s }}">
                        {{ $s === 'approved' ? '✅' : ($s === 'pending' ? '⏳' : '❌') }}
                        {{ ucfirst($s) }}
                    </span>
                </div>
                <div style="background:var(--gray-50); border-radius:8px; padding:1rem; font-size:.875rem; color:var(--gray-600);">
                    <div style="display:flex; justify-content:space-between; padding:.3rem 0; border-bottom:1px solid var(--gray-100);">
                        <span>Semester</span><span style="font-weight:600; color:var(--dark);">{{ $latest->semester?->name ?? 'N/A' }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:.3rem 0; border-bottom:1px solid var(--gray-100);">
                        <span>Vehicle</span><span style="font-weight:600; color:var(--dark);">{{ $latest->vehicle?->plate_number ?? 'N/A' }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:.3rem 0; border-bottom:1px solid var(--gray-100);">
                        <span>Submitted</span><span style="font-weight:600; color:var(--dark);">{{ $latest->created_at?->format('d M Y') ?? 'N/A' }}</span>
                    </div>
                    @if($latest->admin_comment)
                    <div style="display:flex; justify-content:space-between; padding:.3rem 0;">
                        <span>Admin Note</span><span style="font-weight:600; color:var(--dark); max-width:55%; text-align:right;">{{ $latest->admin_comment }}</span>
                    </div>
                    @endif
                </div>
                @if($latest->status === 'approved')
                    @if($latest->parkingTicket)
                        <div style="margin-top:1rem; display:flex; gap:.75rem;">
                            <a href="{{ route('permit.show', $latest->parkingTicket) }}" class="btn btn-primary btn-sm" style="flex:1; justify-content:center;">🎫 View Permit</a>
                            <a href="{{ route('permit.download', $latest->parkingTicket) }}" class="btn btn-outline btn-sm" style="flex:1; justify-content:center;">📥 Download PDF</a>
                        </div>
                    @else
                        <div style="margin-top:1rem; font-size:.85rem; color:var(--gray-400);">⚙️ Permit generating…</div>
                    @endif
                @elseif($latest->status === 'approved' && $latest->parkingTicket)
                    <div style="margin-top:1rem;">
                        <a href="{{ route('student.renew', $latest->id) }}" class="btn btn-outline btn-sm" style="width:100%; justify-content:center;">🔄 Renew for Next Semester</a>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-icon">📋</div>
                    <p>No applications yet.</p>
                    <a href="{{ route('student.apply') }}" class="btn btn-primary btn-sm" style="margin-top:1rem;">Apply Now</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Active Semester + Quick Links -->
    <div style="display:flex; flex-direction:column; gap:1.25rem;">
        @if($activeSemester)
        <div class="card">
            <div class="card-header"><span class="card-title">📅 Active Semester</span></div>
            <div class="card-body">
                <div style="font-size:1.2rem; font-weight:700; color:var(--dark); margin-bottom:.75rem;">{{ $activeSemester->name }}</div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:.75rem; font-size:.875rem;">
                    <div style="background:var(--orange-pale); border-radius:8px; padding:.75rem;">
                        <div style="color:var(--gray-600); margin-bottom:.25rem;">Start Date</div>
                        <div style="font-weight:700;">{{ \Carbon\Carbon::parse($activeSemester->start_date)->format('d M Y') }}</div>
                    </div>
                    <div style="background:var(--orange-pale); border-radius:8px; padding:.75rem;">
                        <div style="color:var(--gray-600); margin-bottom:.25rem;">End Date</div>
                        <div style="font-weight:700;">{{ \Carbon\Carbon::parse($activeSemester->end_date)->format('d M Y') }}</div>
                    </div>
                </div>
                @if($activeSemester->semester_fee > 0)
                <div style="margin-top:.75rem; padding:.75rem; background:var(--orange-pale); border-radius:8px; display:flex; align-items:center; justify-content:space-between;">
                    <span style="font-size:.875rem; color:var(--gray-600);">Parking Fee</span>
                    <span style="font-weight:700; color:var(--orange-dark);">৳ {{ number_format($activeSemester->semester_fee, 2) }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header"><span class="card-title">⚡ Quick Actions</span></div>
            <div class="card-body" style="display:flex; flex-direction:column; gap:.6rem;">
                <a href="{{ route('student.apply') }}" class="btn btn-primary" style="justify-content:flex-start;">➕ New Parking Application</a>
                <a href="{{ route('student.applications') }}" class="btn btn-outline" style="justify-content:flex-start;">📋 View My Applications</a>
                <a href="{{ route('student.documents') }}" class="btn btn-outline" style="justify-content:flex-start;">📄 Manage Documents</a>
                <a href="{{ route('student.profile') }}" class="btn btn-outline" style="justify-content:flex-start;">👤 Update Profile</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/parkar-ajax.js') }}"></script>
<script src="{{ asset('js/weather-ajax.js') }}"></script>
@endpush
