@extends('layouts.app')
@section('title', 'Parking Permit')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">🎫 Parking Permit</div>
        <div class="page-subtitle">Official parking permit issued by AUST ParKar System</div>
    </div>
    <div style="display:flex; gap:.75rem;">
        <a href="{{ route('permit.download', $ticket) }}" class="btn btn-primary">📥 Download PDF</a>
        <a href="{{ route('student.applications') }}" class="btn btn-outline">← Back</a>
    </div>
</div>

{{-- Permit Card --}}
<div style="max-width:720px; margin:0 auto;">
    <div style="background:white; border-radius:20px; overflow:hidden; box-shadow:0 20px 60px rgba(249,115,22,.2); border:2px solid var(--orange-light);">

        {{-- Header --}}
        <div style="background:linear-gradient(135deg, var(--orange-dark) 0%, var(--orange) 100%); padding:2rem 2.5rem; color:white; display:flex; align-items:center; justify-content:space-between;">
            <div>
                <div style="font-size:.8rem; font-weight:600; letter-spacing:.15em; opacity:.8; text-transform:uppercase; margin-bottom:.4rem;">Ahsanullah University of Science & Technology</div>
                <div style="font-size:1.8rem; font-weight:900; letter-spacing:-.5px;">🚗 ParKar System</div>
                <div style="font-size:.85rem; opacity:.85; margin-top:.25rem;">AI-Assisted Parking Permission</div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:.75rem; opacity:.7; margin-bottom:.25rem;">TICKET ID</div>
                <div style="font-size:1.1rem; font-weight:900; letter-spacing:.08em; font-family:monospace; background:rgba(255,255,255,.15); padding:.5rem 1rem; border-radius:8px;">
                    {{ $ticket->ticket_id }}
                </div>
            </div>
        </div>

        {{-- Status Banner --}}
        <div style="background:#ECFDF5; border-bottom:2px solid #A7F3D0; padding:.75rem 2.5rem; display:flex; align-items:center; gap:.75rem;">
            <span style="font-size:1.3rem;">✅</span>
            <span style="font-weight:700; color:#065F46; font-size:.95rem;">PERMIT VALID — AUTHORIZED ENTRY</span>
            <span style="margin-left:auto; font-size:.8rem; color:#047857;">Issued: {{ $ticket->issue_date?->format('d M Y') ?? now()->format('d M Y') }}</span>
        </div>

        {{-- Main Content --}}
        <div style="padding:2rem 2.5rem; display:grid; grid-template-columns:1fr 1fr; gap:2rem;">
            {{-- Applicant Info --}}
            <div>
                <div style="font-size:.75rem; font-weight:700; color:var(--orange); letter-spacing:.1em; text-transform:uppercase; margin-bottom:1rem;">👤 Permit Holder</div>
                <div style="margin-bottom:.75rem;">
                    <div style="font-size:.75rem; color:var(--gray-400); text-transform:uppercase; letter-spacing:.05em;">Full Name</div>
                    <div style="font-size:1.1rem; font-weight:700; color:var(--dark);">{{ $application->applicant_name }}</div>
                </div>
                <div style="margin-bottom:.75rem;">
                    <div style="font-size:.75rem; color:var(--gray-400); text-transform:uppercase; letter-spacing:.05em;">University ID</div>
                    <div style="font-size:1rem; font-weight:700; color:var(--dark); font-family:monospace;">{{ $application->applicant_university_id }}</div>
                </div>
                <div style="margin-bottom:.75rem;">
                    <div style="font-size:.75rem; color:var(--gray-400); text-transform:uppercase; letter-spacing:.05em;">Role</div>
                    <div style="font-weight:600; color:var(--dark);">{{ ucfirst($application->register_as ?? $application->user?->role ?? '—') }}</div>
                </div>
                @if($application->user?->department)
                <div>
                    <div style="font-size:.75rem; color:var(--gray-400); text-transform:uppercase; letter-spacing:.05em;">Department</div>
                    <div style="font-weight:600; color:var(--dark);">{{ $application->user->department }}</div>
                </div>
                @endif
            </div>

            {{-- Vehicle Info --}}
            <div>
                <div style="font-size:.75rem; font-weight:700; color:var(--orange); letter-spacing:.1em; text-transform:uppercase; margin-bottom:1rem;">🚗 Vehicle Details</div>
                @if($application->vehicle)
                <div style="margin-bottom:.75rem;">
                    <div style="font-size:.75rem; color:var(--gray-400); text-transform:uppercase; letter-spacing:.05em;">License Plate</div>
                    <div style="font-size:1.3rem; font-weight:900; color:var(--dark); font-family:monospace; letter-spacing:.1em; background:var(--gray-50); padding:.4rem .8rem; border-radius:6px; display:inline-block; margin-top:.2rem; border:2px solid var(--gray-200);">
                        {{ $application->vehicle->plate_number }}
                    </div>
                </div>
                <div style="margin-bottom:.75rem;">
                    <div style="font-size:.75rem; color:var(--gray-400); text-transform:uppercase; letter-spacing:.05em;">Vehicle</div>
                    <div style="font-weight:600; color:var(--dark);">{{ $application->vehicle->brand }} {{ $application->vehicle->model }}</div>
                </div>
                <div style="margin-bottom:.75rem;">
                    <div style="font-size:.75rem; color:var(--gray-400); text-transform:uppercase; letter-spacing:.05em;">Color / Type</div>
                    <div style="font-weight:600; color:var(--dark);">{{ ucfirst($application->vehicle->color) }} · {{ ucfirst($application->vehicle->vehicle_type) }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Semester + Parking Slot --}}
        <div style="padding:1.25rem 2.5rem; background:var(--orange-pale); border-top:1px solid var(--orange-light); display:grid; grid-template-columns:1fr 1fr 1fr; gap:1.5rem;">
            <div>
                <div style="font-size:.72rem; color:var(--orange-dark); text-transform:uppercase; letter-spacing:.05em; margin-bottom:.3rem;">📅 Semester</div>
                <div style="font-weight:700; color:var(--dark);">{{ $application->semester?->name ?? 'N/A' }}</div>
                @if($application->semester)
                <div style="font-size:.78rem; color:var(--gray-600);">
                    {{ \Carbon\Carbon::parse($application->semester->start_date)->format('d M Y') }} —
                    {{ \Carbon\Carbon::parse($application->semester->end_date)->format('d M Y') }}
                </div>
                @endif
            </div>
            <div>
                <div style="font-size:.72rem; color:var(--orange-dark); text-transform:uppercase; letter-spacing:.05em; margin-bottom:.3rem;">🅿️ Parking Slot</div>
                <div style="font-weight:700; color:var(--dark);">{{ $ticket->parking_slot ?? 'General' }}</div>
            </div>
            <div>
                <div style="font-size:.72rem; color:var(--orange-dark); text-transform:uppercase; letter-spacing:.05em; margin-bottom:.3rem;">📞 Contact</div>
                <div style="font-weight:600; color:var(--dark); font-size:.875rem;">{{ $application->applicant_phone ?? '—' }}</div>
            </div>
        </div>

        {{-- Footer --}}
        <div style="padding:1.25rem 2.5rem; border-top:1px solid var(--gray-200); display:flex; align-items:center; justify-content:space-between;">
            <div style="font-size:.78rem; color:var(--gray-400);">
                This permit is valid for the specified semester only. Must be presented to security personnel on request.
            </div>
            <div style="font-size:.78rem; color:var(--gray-400); text-align:right;">
                ParKar System · AUST<br>
                Generated: {{ now()->format('d M Y, h:i A') }}
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div style="display:flex; gap:1rem; margin-top:1.5rem; justify-content:center;">
        <a href="{{ route('permit.download', $ticket) }}" class="btn btn-primary" style="padding:.85rem 2rem; font-size:1rem;">
            📥 Download Permit as PDF
        </a>
        <button onclick="window.print()" class="btn btn-outline" style="padding:.85rem 2rem; font-size:1rem;">
            🖨️ Print
        </button>
    </div>
</div>
@endsection
