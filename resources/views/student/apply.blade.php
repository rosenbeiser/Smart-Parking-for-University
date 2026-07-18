@extends('layouts.app')
@section('title', 'Apply for Parking')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">New Parking Application</div>
        <div class="page-subtitle">Fill in all sections and upload required documents. AI will verify your documents automatically.</div>
    </div>
    <a href="{{ route('student.dashboard') }}" class="btn btn-outline">← Back</a>
</div>

<form method="POST" action="{{ route('student.apply.post') }}" enctype="multipart/form-data">
    @csrf

    {{-- Section 1: Personal Info --}}
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header"><span class="card-title">👤 Personal Information</span></div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           value="{{ old('name', Auth::user()->name) }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">AUST ID *</label>
                    <input type="text" name="aust_id" class="form-control {{ $errors->has('aust_id') ? 'is-invalid' : '' }}"
                           value="{{ old('aust_id', Auth::user()->university_id) }}" required placeholder="e.g. 20230104141">
                    @error('aust_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           value="{{ old('email', Auth::user()->email) }}" required>
                    @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Contact Phone *</label>
                    <input type="text" name="contact_phone" class="form-control {{ $errors->has('contact_phone') ? 'is-invalid' : '' }}"
                           value="{{ old('contact_phone', Auth::user()->phone) }}" required placeholder="01XXXXXXXXX">
                    @error('contact_phone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            @if($isTeacher)
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Department *</label>
                    <input type="text" name="department" class="form-control {{ $errors->has('department') ? 'is-invalid' : '' }}"
                           value="{{ old('department', Auth::user()->department) }}" required>
                    @error('department') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Academic Role *</label>
                    <select name="academic_role" class="form-select {{ $errors->has('academic_role') ? 'is-invalid' : '' }}" required>
                        <option value="">Select role...</option>
                        <option value="lecturer" {{ old('academic_role') === 'lecturer' ? 'selected' : '' }}>Lecturer</option>
                        <option value="professor" {{ old('academic_role') === 'professor' ? 'selected' : '' }}>Professor</option>
                        <option value="associate_professor" {{ old('academic_role') === 'associate_professor' ? 'selected' : '' }}>Associate Professor</option>
                        <option value="adjunct_professor" {{ old('academic_role') === 'adjunct_professor' ? 'selected' : '' }}>Adjunct Professor</option>
                    </select>
                    @error('academic_role') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            @else
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Study Semester *</label>
                    <select name="study_semester" class="form-select {{ $errors->has('study_semester') ? 'is-invalid' : '' }}" required>
                        <option value="">Select semester...</option>
                        @foreach($studySemesters as $sem)
                        <option value="{{ $sem }}" {{ old('study_semester') === $sem ? 'selected' : '' }}>Year {{ $sem }}</option>
                        @endforeach
                    </select>
                    @error('study_semester') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Department</label>
                    <input type="text" name="department" class="form-control"
                           value="{{ old('department', Auth::user()->department) }}" placeholder="Optional">
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Section 2: Vehicle --}}
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header"><span class="card-title">🚗 Vehicle Information</span></div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Vehicle Type *</label>
                    @php
                        // Lab 9 — Cookies: fall back to cookie value if no old() flash
                        $selectedType = old('vehicle_type', $preferredVehicleType ?? '');
                    @endphp
                    <select name="vehicle_type" class="form-select {{ $errors->has('vehicle_type') ? 'is-invalid' : '' }}" required>
                        <option value="">Select type...</option>
                        <option value="car"        {{ $selectedType === 'car'        ? 'selected' : '' }}>🚗 Car</option>
                        <option value="motorcycle" {{ $selectedType === 'motorcycle' ? 'selected' : '' }}>🏍️ Motorcycle</option>
                        <option value="other"      {{ $selectedType === 'other'      ? 'selected' : '' }}>Other</option>
                    </select>
                    @if($preferredVehicleType && !old('vehicle_type'))
                        <span class="form-hint">🍪 Pre-filled from your last application preference.</span>
                    @endif
                    @error('vehicle_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">License Plate *</label>
                    <input type="text" name="vehicle_plate" class="form-control {{ $errors->has('vehicle_plate') ? 'is-invalid' : '' }}"
                           value="{{ old('vehicle_plate') }}" required placeholder="e.g. DHK-CA-1234" style="text-transform:uppercase;">
                    @error('vehicle_plate') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label">Brand *</label>
                    <input type="text" name="vehicle_brand" class="form-control {{ $errors->has('vehicle_brand') ? 'is-invalid' : '' }}"
                           value="{{ old('vehicle_brand') }}" required placeholder="e.g. Toyota">
                    @error('vehicle_brand') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Model *</label>
                    <input type="text" name="vehicle_model" class="form-control {{ $errors->has('vehicle_model') ? 'is-invalid' : '' }}"
                           value="{{ old('vehicle_model') }}" required placeholder="e.g. Corolla">
                    @error('vehicle_model') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Color *</label>
                    <input type="text" name="vehicle_color" class="form-control {{ $errors->has('vehicle_color') ? 'is-invalid' : '' }}"
                           value="{{ old('vehicle_color') }}" required placeholder="e.g. White">
                    @error('vehicle_color') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Registration Number *</label>
                <input type="text" name="registration_number" class="form-control {{ $errors->has('registration_number') ? 'is-invalid' : '' }}"
                       value="{{ old('registration_number') }}" required placeholder="Vehicle registration certificate number">
                @error('registration_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    {{-- Section 3: Documents --}}
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header">
            <span class="card-title">📄 Required Documents</span>
            <span style="font-size:.8rem; color:var(--orange-dark); background:var(--orange-pale); padding:.25rem .75rem; border-radius:20px;">🤖 AI Verified</span>
        </div>
        <div class="card-body">
            <div style="background:var(--orange-pale); border-radius:8px; padding:1rem; margin-bottom:1.5rem; font-size:.875rem; color:var(--orange-dark);">
                ⚠️ Upload clear, high-quality images or PDFs. Blurry images will be rejected by AI verification. Max file size: 5MB each.
            </div>
            <div class="form-row">
                @php
                    $docFields = [
                        'vehicle_registration_certificate' => ['label' => 'Vehicle Registration Certificate', 'icon' => '📋', 'accept' => '.pdf,.jpg,.jpeg,.png'],
                        'driving_license'                  => ['label' => 'Driving License', 'icon' => '🪪', 'accept' => '.pdf,.jpg,.jpeg,.png'],
                        'university_id_card'               => ['label' => 'University ID Card', 'icon' => '🎓', 'accept' => '.pdf,.jpg,.jpeg,.png'],
                        'vehicle_photo'                    => ['label' => 'Vehicle Photo', 'icon' => '📷', 'accept' => '.jpg,.jpeg,.png'],
                    ];
                @endphp
                @foreach($docFields as $field => $cfg)
                <div class="form-group" style="background:var(--gray-50); border-radius:10px; padding:1.25rem; border:1.5px dashed var(--gray-200); transition:border-color .2s;"
                     onmouseover="this.style.borderColor='var(--orange-light)'" onmouseout="this.style.borderColor='var(--gray-200)'">
                    <div style="font-size:1.5rem; margin-bottom:.5rem;">{{ $cfg['icon'] }}</div>
                    <label class="form-label" for="{{ $field }}">{{ $cfg['label'] }} *</label>
                    <input type="file" id="{{ $field }}" name="documents[{{ $field }}]"
                           class="form-control {{ $errors->has("documents.$field") ? 'is-invalid' : '' }}"
                           accept="{{ $cfg['accept'] }}" required>
                    @error("documents.$field") <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Section 4: Notes & NDA --}}
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header"><span class="card-title">📝 Additional Notes</span></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Notes (Optional)</label>
                <textarea name="notes" class="form-control" rows="3"
                          placeholder="Any additional information you'd like to provide...">{{ old('notes') }}</textarea>
            </div>
            <div style="background:var(--orange-pale); border-radius:10px; padding:1.25rem; border:1px solid var(--orange-light);">
                <label style="display:flex; align-items:flex-start; gap:.75rem; cursor:pointer;">
                    <input type="checkbox" name="nda_signed" value="1" style="width:18px;height:18px;accent-color:var(--orange);margin-top:2px;" required {{ old('nda_signed') ? 'checked' : '' }}>
                    <span style="font-size:.9rem; color:var(--gray-800); line-height:1.6;">
                        I confirm that all information provided is accurate and truthful. I agree to use the parking access responsibly and in accordance with university parking policies. I understand that providing false information may result in permanent disqualification.
                    </span>
                </label>
                @error('nda_signed') <span class="invalid-feedback" style="margin-top:.5rem;">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <div style="display:flex; justify-content:flex-end; gap:1rem;">
        <a href="{{ route('student.dashboard') }}" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">🚀 Submit Application</button>
    </div>
</form>
@endsection
