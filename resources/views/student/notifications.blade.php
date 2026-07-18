@extends('layouts.app')
@section('title', 'Notifications')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div class="page-title">Notifications 🔔</div>
        <div class="page-subtitle">Your latest updates from the ParKar system.</div>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        @if($notifications->count())
            @foreach($notifications as $notif)
            <div style="display:flex; align-items:flex-start; gap:1rem; padding:1.25rem 1.5rem; border-bottom:1px solid var(--gray-100); transition:background .15s;"
                 onmouseover="this.style.background='var(--orange-pale)'" onmouseout="this.style.background=''">
                <div style="width:40px; height:40px; border-radius:50%; background:{{ $notif->is_read ? 'var(--gray-100)' : 'var(--orange-pale)' }}; display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0;">
                    🔔
                </div>
                <div style="flex:1;">
                    <div style="display:flex; align-items:center; gap:.75rem; margin-bottom:.25rem;">
                        <span style="font-weight:{{ $notif->is_read ? '500' : '700' }}; color:var(--dark);">{{ $notif->title ?? 'Notification' }}</span>
                        @if(!$notif->is_read)
                            <span style="width:8px; height:8px; background:var(--orange); border-radius:50%; display:inline-block; flex-shrink:0;"></span>
                        @endif
                    </div>
                    <div style="font-size:.9rem; color:var(--gray-600); line-height:1.5;">{{ $notif->message ?? $notif->body ?? '' }}</div>
                    <div style="font-size:.75rem; color:var(--gray-400); margin-top:.35rem;">
                        {{ $notif->created_at?->diffForHumans() ?? '—' }}
                    </div>
                </div>
            </div>
            @endforeach
            <div style="padding:1rem 1.5rem; border-top:1px solid var(--gray-100);">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="empty-state" style="padding:4rem;">
                <div class="empty-icon">🔔</div>
                <p style="font-weight:600; margin-bottom:.5rem;">All caught up!</p>
                <p style="font-size:.875rem; color:var(--gray-400);">You have no notifications at the moment.</p>
            </div>
        @endif
    </div>
</div>
@endsection
