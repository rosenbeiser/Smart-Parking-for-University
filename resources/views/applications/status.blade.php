@extends('layouts.app')

@section('title', 'Application Status')

@section('content')
<div style="max-width:640px;">

    <h1 style="font-size:1.6rem; font-weight:700; color:var(--dark); margin-bottom:1.5rem;">
        Application Status
    </h1>

    {{-- Server-rendered static status (rendered at page load) --}}
    <div style="background:white; border:1px solid var(--gray-200); border-radius:var(--radius); padding:1.5rem; margin-bottom:1.5rem;">
        <p style="font-size:.82rem; color:var(--gray-600); margin-bottom:.5rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600;">
            Current Status (server-rendered)
        </p>
        <div style="display:flex; align-items:center; gap:.75rem;">
            @php
                $statusColors = [
                    'pending'  => ['bg'=>'#FEF3C7', 'text'=>'#92400E'],
                    'approved' => ['bg'=>'#D1FAE5', 'text'=>'#065F46'],
                    'rejected' => ['bg'=>'#FEE2E2', 'text'=>'#991B1B'],
                    'expired'  => ['bg'=>'#F3F4F6', 'text'=>'#374151'],
                ];
                $colors = $statusColors[$application->status] ?? ['bg'=>'#F3F4F6','text'=>'#374151'];
            @endphp
            <span style="background:{{ $colors['bg'] }}; color:{{ $colors['text'] }};
                         padding:.35rem .9rem; border-radius:999px; font-size:.85rem;
                         font-weight:700; text-transform:uppercase; letter-spacing:.05em;">
                {{ $application->status }}
            </span>
            <span style="font-size:.85rem; color:var(--gray-600);">
                Last updated {{ $application->updated_at->diffForHumans() }}
            </span>
        </div>

        <div style="margin-top:1rem; display:grid; grid-template-columns:1fr 1fr; gap:.75rem;">
            <div>
                <p style="font-size:.75rem; color:var(--gray-400); margin-bottom:.2rem;">Application ID</p>
                <p style="font-size:.9rem; font-weight:600; color:var(--dark);">#{{ $application->id }}</p>
            </div>
            <div>
                <p style="font-size:.75rem; color:var(--gray-400); margin-bottom:.2rem;">Semester</p>
                <p style="font-size:.9rem; font-weight:600; color:var(--dark);">
                    {{ optional($application->semester)->name ?? 'N/A' }}
                </p>
            </div>
        </div>
    </div>

    <hr style="border:none; border-top:1px solid var(--gray-200); margin-bottom:1.5rem;">

    {{-- AJAX Live Status Section --}}
    <div style="background:white; border:1px solid var(--gray-200); border-radius:var(--radius); padding:1.5rem;">
        <h2 style="font-size:1.1rem; font-weight:700; color:var(--dark); margin-bottom:.5rem;">
            🔄 Live Status
        </h2>
        <p style="font-size:.875rem; color:var(--gray-600); margin-bottom:1.25rem;">
            Click the button below to fetch the latest status without reloading the page.
            This uses an AJAX request in the background so the page stays fully interactive.
        </p>

        <button
            id="checkStatusBtn"
            data-application-id="{{ $application->id }}"
            style="display:inline-flex; align-items:center; gap:.5rem; background:var(--orange);
                   color:white; padding:.65rem 1.25rem; border:none; border-radius:var(--radius);
                   font-size:.9rem; font-weight:600; font-family:inherit; cursor:pointer;
                   transition:background .2s;"
            onmouseover="this.style.background='var(--orange-dark)'"
            onmouseout="this.style.background='var(--orange)'">
            Check Status
        </button>

        <div id="statusResult" style="margin-top:1rem;"></div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('js/parkar-ajax.js') }}"></script>
@endpush
