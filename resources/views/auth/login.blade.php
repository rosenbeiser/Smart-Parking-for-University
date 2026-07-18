@extends('layouts.guest')

@section('content')
<h2 class="guest-card-title">Welcome back</h2>
<p class="guest-card-subtitle">Sign in to your ParKar account</p>

<form method="POST" action="{{ route('login.post') }}">
    @csrf
    <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
               value="{{ old('email') }}" placeholder="yourname@aust.edu" required autofocus>
        @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
               placeholder="••••••••" required>
        @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
    <div class="form-group" style="display:flex; align-items:center; gap:.5rem;">
        <input type="checkbox" id="remember" name="remember" style="width:16px;height:16px;accent-color:var(--orange);">
        <label for="remember" style="font-size:.875rem;color:var(--gray-600);cursor:pointer;">Keep me signed in</label>
    </div>
    <button type="submit" class="btn btn-primary">Sign In →</button>
</form>

<div class="auth-footer">
    Don't have an account? <a href="{{ route('register') }}">Create one</a>
</div>
@endsection
