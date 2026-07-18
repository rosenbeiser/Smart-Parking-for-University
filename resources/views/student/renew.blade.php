@extends('layouts.app')
@section('title', 'Renew Application')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">Renew Application #{{ $application->id }}</div>
        <div class="page-subtitle">Carry forward your existing data to the new semester with a quick renewal.</div>
    </div>
    <a href="{{ route('student.applications') }}" class="btn btn-outline">← Back</a>
</div>

<div style="display:grid; grid-template-columns:1fr 1.4fr; gap:1.5rem; align-items:start;">

    {{-- Summary card --}}
    <div class="card">
        <div class="card-header"><span class="card-title">📋 Current Application Summary</span></div>
        <div class="card-body">
            <div style="font-size:.875rem; color:var(--gray-600);">
                <div style="display:flex; justify-content:space-between; padding:.5rem 0; border-bottom:1px solid var(--gray-100);">
                    <span>Applicant</span><span style="font-weight:600; color:var(--dark);">{{ $application->applicant_name }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:.5rem 0; border-bottom:1px solid var(--gray-100);">
                    <span>AUST ID</span><span style="font-weight:600; color:var(--dark);">{{ $application->applicant_university_id }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:.5rem 0; border-bottom:1px solid var(--gray-100);">
                    <span>Previous Semester</span><span style="font-weight:600; color:var(--dark);">{{ $application->semester?->name }}</span>
                </div>
                @if($application->vehicle)
                <div style="display:flex; justify-content:space-between; padding:.5rem 0; border-bottom:1px solid var(--gray-100);">
                    <span>Vehicle Plate</span><span style="font-weight:600; color:var(--dark);">{{ $application->vehicle->plate_number }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:.5rem 0; border-bottom:1px solid var(--gray-100);">
                    <span>Vehicle Type</span><span style="font-weight:600; color:var(--dark);">{{ ucfirst($application->vehicle->vehicle_type) }}</span>
                </div>
                @endif
                <div style="display:flex; justify-content:space-between; padding:.5rem 0; border-bottom:1px solid var(--gray-100);">
                    <span>Documents</span><span style="font-weight:600; color:#065F46;">{{ $application->documents->count() }} attached</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:.5rem 0;">
                    <span>Status</span><span class="badge badge-{{ $application->status }}">{{ ucfirst($application->status) }}</span>
                </div>
            </div>

            <div style="margin-top:1.25rem; padding:1rem; background:var(--orange-pale); border-radius:8px; font-size:.85rem; color:var(--orange-dark); border:1px solid var(--orange-light);">
                ✅ Your existing vehicle details and attached documents will be <strong>carried forward</strong> automatically. No re-upload needed.
            </div>
        </div>
    </div>

    {{-- Renewal Form --}}
    <div class="card">
        <div class="card-header"><span class="card-title">🔄 Submit Renewal</span></div>
        <div class="card-body">
            <form method="POST" action="{{ route('student.renew.post', $application->id) }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Renewal Note <span style="color:var(--gray-400); font-weight:400;">(Optional)</span></label>
                    <textarea name="review_note" class="form-control" rows="3"
                              placeholder="Any changes or notes for the admin (e.g. vehicle changed colour, address update)...">{{ old('review_note') }}</textarea>
                    <span class="form-hint">This note will be visible to the reviewing administrator.</span>
                </div>

                <div style="background:var(--orange-pale); border-radius:10px; padding:1.25rem; border:1px solid var(--orange-light); margin-bottom:1.5rem;">
                    <label style="display:flex; align-items:flex-start; gap:.75rem; cursor:pointer;">
                        <input type="checkbox" name="acknowledged" value="1"
                               style="width:18px; height:18px; accent-color:var(--orange); margin-top:2px;"
                               required {{ old('acknowledged') ? 'checked' : '' }}>
                        <span style="font-size:.9rem; color:var(--gray-800); line-height:1.6;">
                            I confirm that all previously submitted information is still accurate and valid. I agree to the university parking terms and policies for the new semester.
                        </span>
                    </label>
                    @error('acknowledged') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div style="display:flex; gap:.75rem; justify-content:flex-end;">
                    <a href="{{ route('student.applications') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">🔄 Submit Renewal</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
