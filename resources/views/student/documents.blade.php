@extends('layouts.app')
@section('title', 'My Documents')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">My Documents</div>
        <div class="page-subtitle">All documents you've uploaded with your parking applications.</div>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        @if($documents->count())
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Uploaded</th>
                        <th>AI Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($documents as $doc)
                    @php
                        $icons = [
                            'license'      => '🪪',
                            'registration' => '📋',
                            'id_card'      => '🎓',
                            'vehicle_photo'=> '📷',
                            'insurance'    => '🛡️',
                        ];
                        $labels = [
                            'license'      => 'Driving License',
                            'registration' => 'Vehicle Registration',
                            'id_card'      => 'University ID Card',
                            'vehicle_photo'=> 'Vehicle Photo',
                            'insurance'    => 'Insurance',
                        ];
                        $icon  = $icons[$doc->document_type]  ?? '📄';
                        $label = $labels[$doc->document_type] ?? ucfirst(str_replace('_', ' ', $doc->document_type));
                    @endphp
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:.75rem;">
                                <span style="font-size:1.75rem;">{{ $icon }}</span>
                                <div>
                                    <div style="font-weight:600;">{{ $label }}</div>
                                    <div style="font-size:.75rem; color:var(--gray-400);">{{ basename($doc->file_path) }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:.875rem; color:var(--gray-600);">{{ $doc->created_at?->format('d M Y, h:i A') }}</td>
                        <td>
                            @if($doc->is_verified)
                                <span class="badge badge-approved">✅ Verified</span>
                            @else
                                <span class="badge badge-pending">⏳ Pending</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; gap:.5rem;">
                                <a href="{{ route('documents.view', $doc->id) }}" target="_blank" class="btn btn-outline btn-sm">👁️ View</a>
                                <a href="{{ route('documents.download', $doc->id) }}" class="btn btn-outline btn-sm">📥 Download</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">📄</div>
                <p style="font-weight:600; margin-bottom:.5rem;">No documents yet</p>
                <p style="font-size:.875rem; color:var(--gray-400);">Documents are uploaded when you submit a parking application.</p>
                <a href="{{ route('student.apply') }}" class="btn btn-primary" style="margin-top:1.25rem;">Apply for Parking</a>
            </div>
        @endif
    </div>
</div>
@endsection
