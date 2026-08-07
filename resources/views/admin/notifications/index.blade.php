@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Notifications</h4>
            <form action="{{ route('admin.notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-primary btn-sm">Tout marquer comme lu</button>
            </form>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($notifications as $notif)
                        <div class="list-group-item d-flex justify-content-between align-items-start {{ $notif->is_read ? '' : 'bg-light' }}">
                            <div>
                                <h6 class="mb-1">{{ $notif->title }}</h6>
                                <p class="mb-1 text-muted small">{{ $notif->message }}</p>
                                <small class="text-muted">{{ $notif->created_at->format('d/m/Y à H:i') }}</small>
                            </div>
                            @if(!$notif->is_read || $notif->action_url)
                                <form action="{{ route('admin.notifications.read', $notif) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                                        {{ $notif->action_url ? 'Voir' : 'Marquer comme lu' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-bell-slash display-6"></i>
                            <p class="mt-2">Aucune notification.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="mt-4">{{ $notifications->links() }}</div>
    </div>
</div>
@endsection
