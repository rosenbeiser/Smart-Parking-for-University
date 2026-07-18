@extends('layouts.app')
@section('title', 'Review Application #' . $application->id)

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">Application #{{ $application->id }}</div>
        <div class="page-subtitle">
            Submitted {{ $application->created_at?->format('d M Y, h:i A') }}
            @if($application->reviewed_at) · Reviewed {{ \Carbon\Carbon::parse($application->reviewed_at)->format('d M Y') }} @endif
        </div>
    </div>
    <div style="display:flex; gap:.75rem; align-items:center;">
        <span class="badge badge-{{ $application->status }}" style="font-size:.9rem; padding:.4rem 1rem;">
            {{ $application->status === 'approved' ? '✅' : ($application->status === 'pending' ? '⏳' : '❌') }}
            {{ ucfirst($application->status) }}
        </span>
        <a href="{{ route('admin.applications') }}" class="btn btn-outline">← Back</a>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1.2fr; gap:1.5rem; align-items:start;">

    {{-- LEFT: Details --}}
    <div style="display:flex; flex-direction:column; gap:1.25rem;">

        {{-- Applicant Info --}}
        <div class="card">
            <div class="card-header"><span class="card-title">👤 Applicant Information</span></div>
            <div class="card-body" style="font-size:.875rem; color:var(--gray-600);">
                @php
                    $rows = [
                        ['Full Name',    $application->applicant_name],
                        ['University ID',$application->applicant_university_id],
                        ['Email',        $application->applicant_email],
                        ['Phone',        $application->applicant_phone],
                        ['Role',         ucfirst($application->register_as ?? $application->user?->role ?? '—')],
                        ['Department',   $application->user?->department ?? '—'],
                    ];
                @endphp
                @foreach($rows as [$label, $val])
                <div style="display:flex; justify-content:space-between; padding:.5rem 0; border-bottom:1px solid var(--gray-100);">
                    <span style="color:var(--gray-400);">{{ $label }}</span>
                    <span style="font-weight:600; color:var(--dark); text-align:right; max-width:55%;">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Vehicle Info --}}
        @if($application->vehicle)
        <div class="card">
            <div class="card-header"><span class="card-title">🚗 Vehicle</span></div>
            <div class="card-body" style="font-size:.875rem; color:var(--gray-600);">
                <div style="text-align:center; margin-bottom:1rem;">
                    <span style="font-size:2rem; font-weight:900; font-family:monospace; color:var(--dark); background:var(--gray-50); border:2px solid var(--gray-200); padding:.4rem 1.2rem; border-radius:8px; display:inline-block;">
                        {{ $application->vehicle->plate_number }}
                    </span>
                </div>
                @php
                    $vrows = [
                        ['Type',   ucfirst($application->vehicle->vehicle_type)],
                        ['Brand',  $application->vehicle->brand],
                        ['Model',  $application->vehicle->model],
                        ['Color',  ucfirst($application->vehicle->color)],
                        ['Reg. No.', $application->vehicle->registration_number],
                    ];
                @endphp
                @foreach($vrows as [$label, $val])
                <div style="display:flex; justify-content:space-between; padding:.5rem 0; border-bottom:1px solid var(--gray-100);">
                    <span style="color:var(--gray-400);">{{ $label }}</span>
                    <span style="font-weight:600; color:var(--dark);">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif


    </div>

    {{-- RIGHT: Documents + Review --}}
    <div style="display:flex; flex-direction:column; gap:1.25rem;">

        {{-- Ticket Info (if approved) --}}
        @if($application->parkingTicket)
        <div class="card" style="border:2px solid #A7F3D0; background:#F0FDF4;">
            <div class="card-body">
                <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1rem;">
                    <span style="font-size:2rem;">🎫</span>
                    <div>
                        <div style="font-weight:800; font-size:1.1rem; color:#065F46;">Permit Issued</div>
                        <div style="font-size:.8rem; color:#047857;">Ticket ID: <strong style="font-family:monospace;">{{ $application->parkingTicket->ticket_id }}</strong></div>
                    </div>
                </div>
                <div style="display:flex; gap:.75rem;">
                    <a href="{{ route('permit.show', $application->parkingTicket) }}" class="btn btn-success btn-sm" style="flex:1; justify-content:center;">🎫 View Permit</a>
                    <a href="{{ route('permit.download', $application->parkingTicket) }}" class="btn btn-outline btn-sm" style="flex:1; justify-content:center;">📥 Download PDF</a>
                </div>
            </div>
        </div>
        @endif

        {{-- Documents --}}
        <div class="card">
            <div class="card-header"><span class="card-title">📄 Uploaded Documents</span></div>
            <div class="card-body" style="display:flex; flex-direction:column; gap:.75rem;">
                @forelse($application->documents as $doc)
                @php
                    $labels = ['license'=>'Driving License','registration'=>'Vehicle Registration','id_card'=>'University ID Card','vehicle_photo'=>'Vehicle Photo','insurance'=>'Insurance'];
                    $icons  = ['license'=>'🪪','registration'=>'📋','id_card'=>'🎓','vehicle_photo'=>'📷','insurance'=>'🛡️'];
                    $label  = $labels[$doc->document_type] ?? ucfirst(str_replace('_',' ',$doc->document_type));
                    $icon   = $icons[$doc->document_type] ?? '📄';
                    $ext    = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
                    $isPdf  = $ext === 'pdf';
                @endphp
                <div style="border:1px solid var(--gray-200); border-radius:10px; overflow:hidden;">
                    <div style="padding:.85rem 1rem; background:var(--gray-50); display:flex; align-items:center; justify-content:space-between;">
                        <div style="display:flex; align-items:center; gap:.6rem;">
                            <span style="font-size:1.3rem;">{{ $icon }}</span>
                            <div>
                                <div style="font-weight:600; font-size:.875rem;">{{ $label }}</div>
                                <div style="font-size:.72rem; color:var(--gray-400);">
                                    {{ strtoupper($ext) }}
                                    @if($doc->is_verified) · <span style="color:#065F46;">✅ Verified</span> @else · <span style="color:var(--orange-dark);">⏳ Pending</span> @endif
                                </div>
                            </div>
                        </div>
                        <div style="display:flex; gap:.5rem;">
                            <a href="{{ route('documents.view', $doc->id) }}" target="_blank" class="btn btn-outline btn-sm">👁️ View</a>
                            <a href="{{ route('documents.download', $doc->id) }}" class="btn btn-outline btn-sm">📥</a>
                        </div>
                    </div>
                    @if(!$isPdf)
                    <div style="max-height:180px; overflow:hidden; background:#000;">
                        <img src="{{ route('admin.documents.view', $doc->id) }}"
                             alt="{{ $label }}"
                             style="width:100%; object-fit:cover; opacity:.9; display:block;">
                    </div>
                    @endif
                </div>
                @empty
                <div style="text-align:center; padding:1.5rem; color:var(--gray-400);">No documents attached.</div>
                @endforelse
            </div>
        </div>

        {{-- Review Form --}}
        @if($application->status === 'pending')
        <div class="card" style="border:2px solid var(--orange-light);">
            <div class="card-header" style="background:var(--orange-pale);">
                <span class="card-title">⚖️ Make a Decision</span>
            </div>
            <div class="card-body">

                {{-- Shared comment + slot fields --}}
                <div class="form-group">
                    <label class="form-label">Admin Comment</label>
                    <textarea id="shared_comment" rows="3" class="form-control {{ $errors->has('admin_comment') ? 'is-invalid' : '' }}"
                              placeholder="Required when rejecting. Optional when approving.">{{ old('admin_comment') }}</textarea>
                    @error('admin_comment') <span class="invalid-feedback" style="display:block;">{{ $message }}</span> @enderror
                    <span id="comment-error" style="display:none; color:#EF4444; font-size:.8rem; margin-top:.35rem;">
                        ⚠ A comment is required when rejecting.
                    </span>
                </div>
                <div class="form-group">
                    <label class="form-label">Parking Slot <span style="font-weight:400; color:var(--gray-400);">(if approving)</span></label>
                    <input type="text" id="shared_slot" class="form-control"
                           value="{{ old('parking_slot') }}" placeholder="e.g. Block-A-12">
                </div>

                <div style="display:flex; gap:.75rem; margin-top:.5rem;">

                    {{-- APPROVE form --}}
                    <form id="form-approve"
                          method="POST"
                          action="{{ route('admin.applications.review', $application->id) }}"
                          style="flex:1;">
                        @csrf
                        <input type="hidden" name="status" value="approved">
                        <input type="hidden" name="admin_comment" id="approve_comment">
                        <input type="hidden" name="parking_slot"  id="approve_slot">
                        <button type="button"
                                class="btn btn-success"
                                style="width:100%; justify-content:center;"
                                onclick="submitReview('approve')">
                            ✅ Approve
                        </button>
                    </form>

                    {{-- REJECT form --}}
                    <form id="form-reject"
                          method="POST"
                          action="{{ route('admin.applications.review', $application->id) }}"
                          style="flex:1;">
                        @csrf
                        <input type="hidden" name="status" value="rejected">
                        <input type="hidden" name="admin_comment" id="reject_comment">
                        <input type="hidden" name="parking_slot"  id="reject_slot">
                        <button type="button"
                                class="btn btn-danger"
                                style="width:100%; justify-content:center;"
                                onclick="submitReview('reject')">
                            ❌ Reject
                        </button>
                    </form>

                </div>
            </div>
        </div>

@push('scripts')
<script>
function submitReview(action) {
    const commentEl = document.getElementById('shared_comment');
    const slotEl    = document.getElementById('shared_slot');
    const errEl     = document.getElementById('comment-error');

    // Clear previous error
    commentEl.style.borderColor = '';
    if (errEl) errEl.style.display = 'none';

    if (action === 'reject' && !commentEl.value.trim()) {
        commentEl.style.borderColor = '#EF4444';
        commentEl.style.outline = '2px solid #EF444440';
        if (errEl) { errEl.style.display = 'block'; }
        commentEl.focus();
        return;
    }

    document.getElementById(action + '_comment').value = commentEl.value;
    document.getElementById(action + '_slot').value    = slotEl.value;

    // Visual feedback
    const btn = event.currentTarget;
    btn.disabled = true;
    btn.textContent = action === 'approve' ? '⏳ Approving…' : '⏳ Rejecting…';

    document.getElementById('form-' + action).submit();
}
</script>
@endpush
        @else
        <div class="card">
            <div class="card-header"><span class="card-title">📝 Review Decision</span></div>
            <div class="card-body" style="font-size:.875rem; color:var(--gray-600);">
                <div style="display:flex; justify-content:space-between; padding:.5rem 0; border-bottom:1px solid var(--gray-100);">
                    <span>Decision</span>
                    <span class="badge badge-{{ $application->status }}">{{ ucfirst($application->status) }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:.5rem 0; border-bottom:1px solid var(--gray-100);">
                    <span>Reviewed At</span>
                    <span style="font-weight:600; color:var(--dark);">{{ $application->reviewed_at ? \Carbon\Carbon::parse($application->reviewed_at)->format('d M Y, h:i A') : '—' }}</span>
                </div>
                @if($application->admin_comment)
                <div style="padding:.5rem 0;">
                    <div style="color:var(--gray-400); margin-bottom:.35rem;">Comment</div>
                    <div style="background:var(--gray-50); border-radius:8px; padding:.75rem; color:var(--dark);">
                        {{ $application->admin_comment }}
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
