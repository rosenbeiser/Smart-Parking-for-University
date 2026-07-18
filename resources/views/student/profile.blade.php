@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">My Profile</div>
        <div class="page-subtitle">Update your personal information displayed on applications.</div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 2fr; gap:1.5rem; align-items:start;">
    {{-- Profile Summary Card --}}
    <div class="card">
        <div class="card-body" style="text-align:center; padding:2rem 1.5rem;">
            <div style="width:80px; height:80px; background:var(--orange-pale); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:2.5rem; margin:0 auto 1rem;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div style="font-weight:800; font-size:1.2rem; color:var(--dark); margin-bottom:.25rem;">{{ $user->name }}</div>
            <div style="font-size:.85rem; color:var(--gray-600); margin-bottom:.75rem;">{{ $user->email }}</div>
            <span class="badge" style="background:var(--orange-pale); color:var(--orange-dark); border:1px solid var(--orange-light); text-transform:uppercase; letter-spacing:.05em; font-size:.75rem;">
                {{ $user->role }}
            </span>
            <div style="margin-top:1.5rem; font-size:.85rem; color:var(--gray-600);">
                @if($user->university_id)
                    <div style="padding:.4rem 0; border-bottom:1px solid var(--gray-100);">🎓 <strong>ID:</strong> {{ $user->university_id }}</div>
                @endif
                @if($user->phone)
                    <div style="padding:.4rem 0; border-bottom:1px solid var(--gray-100);">📞 {{ $user->phone }}</div>
                @endif
                @if($user->department)
                    <div style="padding:.4rem 0;">🏫 {{ $user->department }}</div>
                @endif
            </div>
            <div style="margin-top:1.25rem; padding:.6rem; background:{{ $user->email_verified_at ? '#ECFDF5' : '#FEF2F2' }}; border-radius:8px; font-size:.8rem; font-weight:600; color:{{ $user->email_verified_at ? '#065F46' : '#991B1B' }};">
                {{ $user->email_verified_at ? '✅ Email Verified' : '⚠️ Email Not Verified' }}
            </div>
        </div>
    </div>

    {{-- Edit Form --}}
    <div class="card">
        <div class="card-header"><span class="card-title">✏️ Edit Information</span></div>
        <div class="card-body">
            <form method="POST" action="{{ route('student.profile.post') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">University ID</label>
                        <input type="text" name="university_id" class="form-control"
                               value="{{ old('university_id', $user->university_id) }}" placeholder="e.g. 20230104141">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone', $user->phone) }}" placeholder="01XXXXXXXXX">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Department</label>
                        <input type="text" name="department" class="form-control"
                               value="{{ old('department', $user->department) }}" placeholder="e.g. CSE">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" value="{{ $user->email }}" disabled
                           style="background:var(--gray-50); color:var(--gray-400); cursor:not-allowed;">
                    <span class="form-hint">Email cannot be changed after registration.</span>
                </div>
                <div style="display:flex; justify-content:flex-end;">
                    <button type="submit" class="btn btn-primary">💾 Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
