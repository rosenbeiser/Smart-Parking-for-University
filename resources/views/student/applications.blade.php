@extends('layouts.app')
@section('title', 'My Applications')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">My Applications</div>
        <div class="page-subtitle">Track all your parking applications and their current status.</div>
    </div>
    <a href="{{ route('student.apply') }}" class="btn btn-primary">➕ New Application</a>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        @if($applications->count())
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Semester</th>
                        <th>Vehicle</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($applications as $app)
                    @php $ticket = $app->parkingTicket; @endphp
                    <tr>
                        <td><span style="font-weight:700; color:var(--orange);">#{{ $app->id }}</span></td>
                        <td>{{ $app->semester?->name ?? '—' }}</td>
                        <td>
                            @if($app->vehicle)
                                <div style="font-weight:600;">{{ $app->vehicle->plate_number }}</div>
                                <div style="font-size:.78rem; color:var(--gray-400);">{{ ucfirst($app->vehicle->vehicle_type) }}</div>
                            @else —
                            @endif
                        </td>
                        <td style="font-size:.875rem; color:var(--gray-600);">{{ $app->created_at?->format('d M Y') }}</td>
                        <td>
                            @php $s = $app->status; @endphp
                            <span class="badge badge-{{ $s }}">
                                {{ $s === 'approved' ? '✅' : ($s === 'pending' ? '⏳' : ($s === 'rejected' ? '❌' : '🔒')) }}
                                {{ ucfirst($s) }}
                            </span>
                            @if($app->admin_comment)
                                <div style="font-size:.75rem; color:var(--gray-400); margin-top:.25rem; max-width:200px; white-space:normal;">
                                    💬 {{ Str::limit($app->admin_comment, 60) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
                                @if($app->status === 'approved' && $ticket)
                                    {{-- Permit available immediately on approval --}}
                                    <a href="{{ route('permit.show', $ticket) }}"
                                       class="btn btn-primary btn-sm"
                                       title="View parking permit">
                                        🎫 Permit
                                    </a>
                                    <a href="{{ route('permit.download', $ticket) }}"
                                       class="btn btn-outline btn-sm"
                                       title="Download PDF">
                                        📥 PDF
                                    </a>
                                    <a href="{{ route('student.renew', $app->id) }}" class="btn btn-outline btn-sm">🔄 Renew</a>
                                @elseif($app->status === 'approved' && !$ticket)
                                    {{-- Approved but ticket not yet issued (edge case) --}}
                                    <span style="font-size:.8rem; color:var(--orange);">⚙️ Ticket generating…</span>
                                @elseif($app->status === 'pending')
                                    <span style="font-size:.8rem; color:var(--gray-400);">⏳ Awaiting review…</span>
                                @else
                                    <a href="{{ route('student.apply') }}" class="btn btn-outline btn-sm">Re-apply</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:1rem 1.5rem; border-top:1px solid var(--gray-100);">
            {{ $applications->links() }}
        </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <p style="font-weight:600; margin-bottom:.5rem;">No applications yet</p>
                <p style="font-size:.875rem; color:var(--gray-400);">Submit your first parking application to get started.</p>
                <a href="{{ route('student.apply') }}" class="btn btn-primary" style="margin-top:1.25rem;">Apply for Parking</a>
            </div>
        @endif
    </div>
</div>
@endsection
