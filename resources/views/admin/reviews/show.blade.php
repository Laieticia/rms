@extends('layouts.app')

@section('title', 'Détail de l\'avis')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary mb-3">
            <i class="bi bi-arrow-left"></i> Retour
        </a>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ $review->user->avatar_url }}" class="rounded-circle me-2" width="50" height="50">
                            <div>
                                <strong>{{ $review->user->full_name }}</strong>
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <p>{{ $review->comment }}</p>
                        <small class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</small>
                        
                        @if($review->order)
                            <p class="mt-2">Commande #{{ $review->order->order_number }}</p>
                        @endif
                        
                        @if($review->admin_response)
                            <div class="bg-light p-3 rounded mt-3">
                                <strong>Réponse :</strong>
                                <p class="mb-0">{{ $review->admin_response }}</p>
                                <small>{{ $review->responded_at->format('d/m/Y H:i') }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Actions</div>
                    <div class="card-body">
                        @if(!$review->is_approved)
                            <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="mb-2">
                                @csrf
                                <button class="btn btn-success w-100">Approuver</button>
                            </form>
                        @endif
                        <button class="btn btn-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#respondModal">
                            Répondre
                        </button>
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger w-100" onclick="return confirm('Supprimer ?')">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="respondModal">
        <div class="modal-dialog">
            <form action="{{ route('admin.reviews.respond', $review) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Répondre à l'avis</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <textarea name="response" class="form-control" rows="4" required>{{ $review->admin_response }}</textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Publier</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection