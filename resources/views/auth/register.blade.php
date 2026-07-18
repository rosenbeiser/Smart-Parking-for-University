@extends('layouts.guest')

@section('content')
<h2 class="guest-card-title">Create Account</h2>
<p class="guest-card-subtitle">Join ParKar to apply for university parking access</p>

<form method="POST" action="{{ route('register.post') }}">
    @csrf
    <div class="form-group">
        <label class="form-label" for="name">Full Name</label>
        <input type="text" id="name" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
               value="{{ old('name') }}" placeholder="Your full name" required>
        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="email">University Email</label>
        <input type="email" id="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
               value="{{ old('email') }}" placeholder="yourname@aust.edu" required>
        @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
        <span class="form-hint">Your role (student / teacher) is detected from your email domain.</span>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
        <div class="form-group">
            <label class="form-label" for="university_id">University ID</label>
            <input type="text" id="university_id" name="university_id" class="form-control"
                   value="{{ old('university_id') }}" placeholder="e.g. 20230104141">
        </div>
        <div class="form-group">
            <label class="form-label" for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" class="form-control"
                   value="{{ old('phone') }}" placeholder="01XXXXXXXXX">
        </div>
    </div>
    <div class="form-group">
        <label class="form-label" for="department">Department</label>
        <input type="text" id="department" name="department" class="form-control"
               value="{{ old('department') }}" placeholder="e.g. Computer Science & Engineering">
    </div>
    <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
               placeholder="Min. 8 characters" required>
        @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="password_confirmation">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
               placeholder="Repeat your password" required>
    </div>
    <button type="submit" class="btn btn-primary">Create Account →</button>
</form>

<div class="auth-footer">
    Already have an account? <a href="{{ route('login') }}">Sign in</a>
</div>
@endsection
