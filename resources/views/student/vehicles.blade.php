@extends('layouts.app')
@section('title', 'My Vehicles')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">My Vehicles</div>
        <div class="page-subtitle">Vehicles registered under your account via parking applications.</div>
    </div>
</div>

@if($vehicles->count())
<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px,1fr)); gap:1.25rem;">
    @foreach($vehicles as $vehicle)
    <div class="card" style="transition:transform .2s, box-shadow .2s;" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(249,115,22,.12)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
        <div style="padding:1.5rem;">
            <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.25rem;">
                <div style="width:52px; height:52px; border-radius:12px; background:var(--orange-pale); display:flex; align-items:center; justify-content:center; font-size:1.75rem;">
                    {{ $vehicle->vehicle_type === 'motorcycle' ? '🏍️' : '🚗' }}
                </div>
                <div>
                    <div style="font-weight:800; font-size:1.1rem; color:var(--dark);">{{ $vehicle->plate_number }}</div>
                    <div style="font-size:.8rem; color:var(--gray-400);">{{ ucfirst($vehicle->vehicle_type) }}</div>
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:.5rem; font-size:.85rem;">
                <div style="background:var(--gray-50); border-radius:8px; padding:.6rem;">
                    <div style="color:var(--gray-400); font-size:.72rem; text-transform:uppercase; letter-spacing:.05em; margin-bottom:.2rem;">Brand</div>
                    <div style="font-weight:600; color:var(--dark);">{{ $vehicle->brand ?? '—' }}</div>
                </div>
                <div style="background:var(--gray-50); border-radius:8px; padding:.6rem;">
                    <div style="color:var(--gray-400); font-size:.72rem; text-transform:uppercase; letter-spacing:.05em; margin-bottom:.2rem;">Model</div>
                    <div style="font-weight:600; color:var(--dark);">{{ $vehicle->model ?? '—' }}</div>
                </div>
                <div style="background:var(--gray-50); border-radius:8px; padding:.6rem;">
                    <div style="color:var(--gray-400); font-size:.72rem; text-transform:uppercase; letter-spacing:.05em; margin-bottom:.2rem;">Color</div>
                    <div style="font-weight:600; color:var(--dark);">{{ $vehicle->color ?? '—' }}</div>
                </div>
                <div style="background:var(--gray-50); border-radius:8px; padding:.6rem;">
                    <div style="color:var(--gray-400); font-size:.72rem; text-transform:uppercase; letter-spacing:.05em; margin-bottom:.2rem;">Reg. No.</div>
                    <div style="font-weight:600; color:var(--dark); font-size:.8rem; word-break:break-all;">{{ $vehicle->registration_number ?? '—' }}</div>
                </div>
            </div>
            <div style="margin-top:1rem; font-size:.75rem; color:var(--gray-400);">
                Added {{ $vehicle->created_at?->format('d M Y') }}
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="card">
    <div class="empty-state">
        <div class="empty-icon">🚗</div>
        <p style="font-weight:600; margin-bottom:.5rem;">No vehicles registered</p>
        <p style="font-size:.875rem; color:var(--gray-400);">Vehicles are added automatically when you submit a parking application.</p>
        <a href="{{ route('student.apply') }}" class="btn btn-primary" style="margin-top:1.25rem;">Apply for Parking</a>
    </div>
</div>
@endif
@endsection
