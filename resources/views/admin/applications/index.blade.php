@extends('layouts.app')
@section('title', 'Applications')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">Parking Applications</div>
        <div class="page-subtitle">Review, approve, or reject student and faculty applications.</div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-body" style="padding:1rem 1.5rem;">
        <form method="GET" action="{{ route('admin.applications') }}" style="display:flex; gap:.75rem; align-items:center; flex-wrap:wrap;">
            <div style="flex:1; min-width:220px;">
                <input type="text" name="search" value="{{ $search }}" class="form-control"
                       placeholder="🔍  Search by name, ID, or email..." style="margin-bottom:0;">
            </div>
            <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
                @php $statuses = ['' => 'All', 'pending' => '⏳ Pending', 'approved' => '✅ Approved', 'rejected' => '❌ Rejected']; @endphp
                @foreach($statuses as $val => $label)
                <a href="{{ route('admin.applications', array_merge(request()->query(), ['status' => $val, 'page' => 1])) }}"
                   class="btn btn-sm {{ $status === $val ? 'btn-primary' : 'btn-outline' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Search</button>
            @if($search || $status)
                <a href="{{ route('admin.applications') }}" class="btn btn-outline btn-sm">✕ Clear</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        @if($applications->count())
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Applicant</th>
                        <th>Role</th>
                        <th>Vehicle</th>
                        <th>Semester</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($applications as $app)
                    @php
                        $payment = $app->payments->sortByDesc('created_at')->first();
                    @endphp
                    <tr>
                        <td><span style="font-weight:700; color:var(--orange);">#{{ $app->id }}</span></td>
                        <td>
                            <div style="font-weight:600; font-size:.9rem;">{{ $app->applicant_name }}</div>
                            <div style="font-size:.75rem; color:var(--gray-400);">{{ $app->applicant_university_id }}</div>
                            <div style="font-size:.72rem; color:var(--gray-400);">{{ $app->applicant_email }}</div>
                        </td>
                        <td>
                            <span class="badge" style="background:var(--orange-pale); color:var(--orange-dark); border:1px solid var(--orange-light); font-size:.7rem;">
                                {{ ucfirst($app->register_as ?? $app->user?->role ?? '—') }}
                            </span>
                        </td>
                        <td>
                            @if($app->vehicle)
                                <div style="font-weight:600; font-size:.875rem; font-family:monospace;">{{ $app->vehicle->plate_number }}</div>
                                <div style="font-size:.75rem; color:var(--gray-400);">{{ ucfirst($app->vehicle->vehicle_type) }}</div>
                            @else <span style="color:var(--gray-400);">—</span>
                            @endif
                        </td>
                        <td style="font-size:.875rem;">{{ $app->semester?->name ?? '—' }}</td>
                        <td style="font-size:.8rem; color:var(--gray-600);">{{ $app->created_at?->format('d M Y') }}</td>
                        <td>
                            <span class="badge badge-{{ $app->status }}">
                                {{ $app->status === 'approved' ? '✅' : ($app->status === 'pending' ? '⏳' : '❌') }}
                                {{ ucfirst($app->status) }}
                            </span>
                        </td>
                        <td>
                            @if($app->status === 'approved')
                                @if($payment)
                                    <span class="badge badge-{{ $payment->status }}" style="font-size:.7rem;">
                                        {{ $payment->status === 'confirmed' ? '✅' : ($payment->status === 'pending' ? '⏳' : '❌') }}
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                @else
                                    <span style="color:var(--gray-400); font-size:.8rem;">Unpaid</span>
                                @endif
                            @else
                                <span style="color:var(--gray-400); font-size:.8rem;">—</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.applications.show', $app->id) }}" class="btn btn-primary btn-sm">
                                {{ $app->status === 'pending' ? '🔍 Review' : '👁️ View' }}
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:1rem 1.5rem; border-top:1px solid var(--gray-100); display:flex; align-items:center; justify-content:space-between;">
            <span style="font-size:.8rem; color:var(--gray-400);">
                Showing {{ $applications->firstItem() }}–{{ $applications->lastItem() }} of {{ $applications->total() }} applications
            </span>
            {{ $applications->links() }}
        </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <p style="font-weight:600;">No applications found</p>
                <p style="font-size:.875rem; color:var(--gray-400); margin-top:.5rem;">Try adjusting your search or filter.</p>
            </div>
        @endif
    </div>
</div>
@endsection
