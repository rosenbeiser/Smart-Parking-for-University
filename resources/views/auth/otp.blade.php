@extends('layouts.guest')

@section('content')
<div style="text-align:center; margin-bottom:1.5rem;">
    <div style="font-size:3rem; margin-bottom:.75rem;">📧</div>
    <h2 class="guest-card-title" style="justify-content:center;">Verify Your Email</h2>
    <p class="guest-card-subtitle">We've sent a 6-digit OTP to your email address. Enter it below to activate your account.</p>
</div>

<form method="POST" action="{{ route('otp.verify') }}">
    @csrf
    <div class="form-group">
        <label class="form-label" for="otp" style="text-align:center;display:block;">One-Time Password (OTP)</label>
        <input type="text" id="otp" name="otp"
               class="form-control {{ $errors->has('otp') ? 'is-invalid' : '' }}"
               placeholder="e.g. 483920" maxlength="6" required autofocus
               style="text-align:center; font-size:1.8rem; letter-spacing:.5rem; font-weight:700;">
        @error('otp') <span class="invalid-feedback" style="text-align:center;display:block;">{{ $message }}</span> @enderror
    </div>
    <button type="submit" class="btn btn-primary">Verify OTP →</button>
</form>

<div class="auth-footer" style="margin-top:1.25rem;">
    <a href="{{ route('register') }}">← Back to Register</a> &nbsp;|&nbsp;
    <a href="{{ route('login') }}">Try Login</a>
</div>
@endsection
