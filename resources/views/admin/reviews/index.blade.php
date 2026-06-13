@extends('layouts.app')

@section('title', 'Gestion des Avis')

@section('content')
<div class="container-fluid">
    <h3 class="fw-bold mb-4"><i class="bi bi-star me-2"></i>Avis clients</h3>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.reviews.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Tous</option>
                        <option value="pending" {{ request('status')=='pending'?'selected':'' }}>En attente</option>
                        <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approuvés</option>
                        <option value="featured" {{ request('status')=='featured'?'selected':'' }}>En vedette</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="rating" class="form-select">
                        <option value="">Toutes les notes</option>
                        @for($i=5;$i>=1;$i--)
                            <option value="{{$i}}" {{ request('rating')==$i?'selected':'' }}>{{$i}} ⭐</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @forelse($reviews as $review)
                <div class="border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex align-items-center">
                            <img src="{{ $review->user->avatar_url }}" class="rounded-circle me-2" width="40" height="40">
                            <div>
                                <strong>{{ $review->user->full_name }}</strong>
                                <div class="text-warning">
                                    @for($i=1;$i<=5;$i++)<i class="bi bi-star{{$i<=$review->rating?'-fill':''}} small"></i>@endfor
                                </div>
                            </div>
                        </div>
                        <div>
                            @if(!$review->is_approved)<span class="badge bg-warning">En attente</span>@endif
                            @if($review->is_featured)<span class="badge bg-info">⭐</span>@endif
                            <small class="d-block">{{ $review->created_at->format('d/m/Y') }}</small>
                        </div>
                    </div>
                    <p class="mt-2 mb-2">{{ $review->comment }}</p>
                    @if($review->admin_response)
                        <div class="bg-light p-2 rounded"><strong>Réponse:</strong> {{ $review->admin_response }}</div>
                    @endif
                    <div class="d-flex gap-2 mt-2">
                        @if(!$review->is_approved)
                            <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-success">Approuver</button>
                            </form>
                        @endif
                        <a href="{{ route('admin.reviews.show', $review) }}" class="btn btn-sm btn-outline-primary">Détails</a>
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer?')">Supprimer</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-center py-4 text-muted">Aucun avis</p>
            @endforelse
            {{ $reviews->links() }}
        </div>
    </div>
</div>
@endsection