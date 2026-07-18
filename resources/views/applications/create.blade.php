@extends('layouts.app')

@section('title', 'Apply for Parking')

@section('content')
<div style="max-width:640px;">

    <div style="display:flex; align-items:baseline; justify-content:space-between; flex-wrap:wrap; gap:.5rem; margin-bottom:1.75rem;">
        <h1 style="font-size:1.6rem; font-weight:700; color:var(--dark);">
            Parking Application
        </h1>
        {{-- Location line from ip-api.com --}}
        <span style="font-size:.82rem; color:var(--gray-600); background:var(--gray-100);
                     padding:.3rem .75rem; border-radius:999px;">
            📍 Applying from: {{ $location['city'] }}, {{ $location['country'] }}
            <span style="color:var(--gray-400);">({{ $location['isp'] }})</span>
        </span>
    </div>

    <div style="background:white; border:1px solid var(--gray-200); border-radius:var(--radius); padding:2rem;">

        <form method="POST" action="{{ route('applications.store') }}">
            @csrf

            {{-- Vehicle Number --}}
            <div style="margin-bottom:1.25rem;">
                <label for="vehicle_number"
                       style="display:block; font-size:.875rem; font-weight:600;
                              color:var(--gray-800); margin-bottom:.4rem;">
                    Vehicle Number
                </label>
                <input
                    type="text"
                    id="vehicle_number"
                    name="vehicle_number"
                    value="{{ old('vehicle_number') }}"
                    placeholder="e.g. DHA-1234"
                    required
                    style="width:100%; padding:.65rem .9rem; border:1px solid var(--gray-200);
                           border-radius:8px; font-size:.9rem; font-family:inherit;
                           outline:none; transition:border-color .2s;"
                    onfocus="this.style.borderColor='var(--orange)'"
                    onblur="this.style.borderColor='var(--gray-200)'">
                @error('vehicle_number')
                    <p style="color:#DC2626; font-size:.8rem; margin-top:.3rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Vehicle Type --}}
            <div style="margin-bottom:1.25rem;">
                <label for="vehicle_type"
                       style="display:block; font-size:.875rem; font-weight:600;
                              color:var(--gray-800); margin-bottom:.4rem;">
                    Vehicle Type
                </label>
                <select
                    id="vehicle_type"
                    name="vehicle_type"
                    required
                    style="width:100%; padding:.65rem .9rem; border:1px solid var(--gray-200);
                           border-radius:8px; font-size:.9rem; font-family:inherit;
                           background:white; outline:none; cursor:pointer; transition:border-color .2s;"
                    onfocus="this.style.borderColor='var(--orange)'"
                    onblur="this.style.borderColor='var(--gray-200)'">
                    <option value="" disabled {{ old('vehicle_type') ? '' : 'selected' }}>Select vehicle type…</option>
                    <option value="car"        {{ old('vehicle_type') === 'car'        ? 'selected' : '' }}>🚗 Car</option>
                    <option value="motorcycle" {{ old('vehicle_type') === 'motorcycle' ? 'selected' : '' }}>🏍️ Motorcycle</option>
                    <option value="bicycle"    {{ old('vehicle_type') === 'bicycle'    ? 'selected' : '' }}>🚲 Bicycle</option>
                </select>
                @error('vehicle_type')
                    <p style="color:#DC2626; font-size:.8rem; margin-top:.3rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Semester --}}
            <div style="margin-bottom:1.75rem;">
                <label for="semester"
                       style="display:block; font-size:.875rem; font-weight:600;
                              color:var(--gray-800); margin-bottom:.4rem;">
                    Semester
                </label>
                <input
                    type="text"
                    id="semester"
                    name="semester"
                    value="{{ old('semester') }}"
                    placeholder="e.g. Fall 2025"
                    required
                    style="width:100%; padding:.65rem .9rem; border:1px solid var(--gray-200);
                           border-radius:8px; font-size:.9rem; font-family:inherit;
                           outline:none; transition:border-color .2s;"
                    onfocus="this.style.borderColor='var(--orange)'"
                    onblur="this.style.borderColor='var(--gray-200)'">
                @error('semester')
                    <p style="color:#DC2626; font-size:.8rem; margin-top:.3rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                style="width:100%; padding:.8rem 1.5rem; background:var(--orange); color:white;
                       border:none; border-radius:var(--radius); font-size:1rem; font-weight:600;
                       font-family:inherit; cursor:pointer; transition:background .2s;"
                onmouseover="this.style.background='var(--orange-dark)'"
                onmouseout="this.style.background='var(--orange)'">
                Submit Application
            </button>
        </form>

    </div>
</div>
@endsection
