@extends('layouts.app')

@section('title', 'Commande #' . $order->order_number)

@section('content')
<div class="content-page">
    <div class="container-fluid">
        {{-- En-tête --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">
                    <i class="bi bi-receipt me-2"></i>Commande #{{ $order->order_number }}
                </h3>
                <small class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</small>
            </div>
            <div>
                <a href="{{ route('admin.orders.print', $order) }}" class="btn btn-outline-secondary me-2" target="_blank">
                    <i class="bi bi-printer"></i> Imprimer
                </a>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
            </div>
        </div>

        <div class="row g-4">
            {{-- Colonne gauche : Détails --}}
            <div class="col-md-8">
                
                {{-- Statut et progression --}}
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📊 Statut de la commande</h5>
                        <span class="badge bg-{{ $order->status_color }} fs-6 px-3 py-2">{{ $order->status_label }}</span>
                    </div>
                    <div class="card-body">
                        {{-- Barre de progression --}}
                        @php
                            $statuses = [
                                'pending' => 'En attente',
                                'confirmed' => 'Confirmée',
                                'preparing' => 'En préparation',
                                'ready' => 'Prête',
                                'in_delivery' => 'En livraison',
                                'delivered' => 'Livrée',
                                'completed' => 'Terminée'
                            ];
                            $statusKeys = array_keys($statuses);
                            $currentIndex = array_search($order->status, $statusKeys);
                            if ($currentIndex === false) $currentIndex = 0;
                        @endphp
                        
                        <div class="d-flex justify-content-between mb-4">
                            @foreach($statuses as $status => $label)
                                @php $index = array_search($status, $statusKeys); @endphp
                                <div class="text-center flex-fill">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center 
                                        {{ $index < $currentIndex ? 'bg-success text-white' : ($index == $currentIndex ? 'bg-primary text-white' : 'bg-light text-muted') }}"
                                        style="width:35px;height:35px;font-size:14px;">
                                        @if($index < $currentIndex)
                                            <i class="bi bi-check-lg"></i>
                                        @elseif($index == $currentIndex && !in_array($order->status, ['delivered','completed']))
                                            <i class="bi bi-arrow-right"></i>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </div>
                                    <br>
                                    <small class="text-muted" style="font-size:11px;">{{ $label }}</small>
                                </div>
                                @if(!$loop->last)
                                    <div class="flex-fill d-flex align-items-center px-1">
                                        <div class="progress flex-grow-1" style="height:3px;">
                                            <div class="progress-bar {{ $index < $currentIndex ? 'bg-success' : 'bg-light' }}" 
                                                style="width: {{ $index < $currentIndex ? '100' : '0' }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        {{-- Boutons d'action --}}
                        <div class="d-flex flex-wrap gap-2">
                            @if($order->status == 'pending')
                                <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="confirmed">
                                    <button type="submit" class="btn btn-info">
                                        <i class="bi bi-check-circle"></i> Confirmer la commande
                                    </button>
                                </form>
                            @endif

                            @if($order->status == 'confirmed')
                                <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="preparing">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-fire"></i> Commencer la préparation
                                    </button>
                                </form>
                            @endif

                            @if($order->status == 'preparing')
                                <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="ready">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-all"></i> Marquer comme prête
                                    </button>
                                </form>
                            @endif

                            @if($order->status == 'ready' && $order->type == 'delivery')
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#assignDeliveryModal">
                                    <i class="bi bi-truck"></i> Assigner un livreur
                                </button>
                            @endif

                            @if($order->status == 'ready' && $order->type != 'delivery')
                                <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-all"></i> Marquer comme terminée
                                    </button>
                                </form>
                            @endif

                            @if(in_array($order->status, ['in_delivery']))
                                <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="delivered">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-box-seam"></i> Marquer comme livrée
                                    </button>
                                </form>
                            @endif

                            @if(in_array($order->status, ['delivered']))
                                <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-star"></i> Terminer la commande
                                    </button>
                                </form>
                            @endif

                            @if(in_array($order->status, ['pending', 'confirmed']))
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#cancelModal">
                                    <i class="bi bi-x-circle"></i> Annuler la commande
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Articles commandés --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">🛒 Articles commandés ({{ $order->items->sum('quantity') }})</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produit</th>
                                        <th class="text-center">Prix unitaire</th>
                                        <th class="text-center">Quantité</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $item->product->primary_image_url ?? 'https://via.placeholder.com/40' }}" 
                                                        class="rounded me-3" width="40" height="40" style="object-fit:cover;">
                                                    <div>
                                                        <strong>{{ $item->product_name }}</strong>
                                                        @if($item->special_instructions)
                                                            <br><small class="text-muted fst-italic">"{{ $item->special_instructions }}"</small>
                                                        @endif
                                                        @if($item->options->count() > 0)
                                                            <br><small class="text-muted">
                                                                @foreach($item->options as $option)
                                                                    + {{ $option->item_name }} ({{ number_format($option->price, 2) }}€)
                                                                @endforeach
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">{{ number_format($item->unit_price, 2) }} €</td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-end fw-bold">{{ number_format($item->total_price, 2) }} €</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end">Sous-total</td>
                                        <td class="text-end">{{ number_format($order->subtotal, 2) }} €</td>
                                    </tr>
                                    @if($order->discount_amount > 0)
                                        <tr>
                                            <td colspan="3" class="text-end text-success">Réduction</td>
                                            <td class="text-end text-success">-{{ number_format($order->discount_amount, 2) }} €</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="3" class="text-end">TVA</td>
                                        <td class="text-end">{{ number_format($order->tax_amount, 2) }} €</td>
                                    </tr>
                                    @if($order->delivery_fee > 0)
                                        <tr>
                                            <td colspan="3" class="text-end">Frais de livraison</td>
                                            <td class="text-end">{{ number_format($order->delivery_fee, 2) }} €</td>
                                        </tr>
                                    @endif
                                    <tr class="fw-bold fs-5">
                                        <td colspan="3" class="text-end">Total</td>
                                        <td class="text-end text-primary">{{ number_format($order->total, 2) }} €</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Historique des statuts --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">📝 Historique</h5>
                    </div>
                    <div class="card-body">
                        @forelse($order->statusHistory as $history)
                            <div class="d-flex mb-3">
                                <div class="me-3">
                                    <div class="bg-{{ $loop->first ? 'primary' : 'success' }} text-white rounded-circle d-flex align-items-center justify-content-center" 
                                        style="width:30px;height:30px;">
                                        <i class="bi bi-{{ $loop->first ? 'clock' : 'check' }} small"></i>
                                    </div>
                                </div>
                                <div>
                                    <strong>
                                        @php
                                            $statusLabels = [
                                                'pending' => 'Commande créée',
                                                'confirmed' => 'Commande confirmée',
                                                'preparing' => 'En préparation',
                                                'ready' => 'Commande prête',
                                                'in_delivery' => 'En cours de livraison',
                                                'delivered' => 'Commande livrée',
                                                'completed' => 'Commande terminée',
                                                'cancelled' => 'Commande annulée',
                                            ];
                                        @endphp
                                        {{ $statusLabels[$history->status] ?? $history->status }}
                                    </strong>
                                    @if($history->comment)
                                        <p class="mb-0 text-muted">{{ $history->comment }}</p>
                                    @endif
                                    <small class="text-muted">
                                        {{ $history->created_at->format('d/m/Y H:i') }} 
                                        par {{ $history->user->full_name ?? 'Système' }}
                                    </small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center">Aucun historique</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Colonne droite : Infos client, livraison, paiement --}}
            <div class="col-md-4">
                {{-- Client --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">👤 Client</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ $order->user->avatar_url }}" class="rounded-circle me-3" width="50" height="50">
                            <div>
                                <strong>{{ $order->user->full_name }}</strong>
                                <br><small class="text-muted">{{ $order->user->email }}</small>
                            </div>
                        </div>
                        <p class="mb-1"><i class="bi bi-telephone me-2"></i>{{ $order->user->phone ?? 'Non renseigné' }}</p>
                        @if($order->user->created_at)
                            <p class="mb-0"><i class="bi bi-calendar me-2"></i>Client depuis {{ $order->user->created_at->format('m/Y') }}</p>
                        @endif
                    </div>
                </div>

                {{-- Livraison --}}
                @if($order->type == 'delivery')
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">📍 Livraison</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-1"><strong>Adresse :</strong></p>
                            <p class="mb-2">{{ $order->delivery_address }}</p>
                            <p class="mb-1"><strong>Code postal :</strong> {{ $order->delivery_postal_code }}</p>
                            <p class="mb-1"><strong>Ville :</strong> {{ $order->delivery_city }}</p>
                            
                            @if($order->delivery_instructions)
                                <hr>
                                <p class="mb-1"><strong>Instructions :</strong></p>
                                <p class="text-muted">{{ $order->delivery_instructions }}</p>
                            @endif

                            @if($order->deliveryPerson)
                                <hr>
                                <p class="mb-1"><strong>Livreur assigné :</strong></p>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $order->deliveryPerson->avatar_url }}" class="rounded-circle me-2" width="35" height="35">
                                    <div>
                                        <strong>{{ $order->deliveryPerson->full_name }}</strong>
                                        <br><small class="text-muted">{{ $order->deliveryPerson->phone }}</small>
                                    </div>
                                </div>
                            @endif

                            @if($order->estimated_delivery_time)
                                <hr>
                                <p class="mb-0">
                                    <i class="bi bi-clock me-2"></i>
                                    Temps estimé : <strong>{{ $order->estimated_delivery_time }} minutes</strong>
                                </p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Type de commande --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">📋 Informations</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <strong>Type :</strong>
                            <span class="badge bg-{{ $order->type=='dine_in'?'info':($order->type=='takeaway'?'warning':'primary') }}">
                                @if($order->type == 'dine_in')
                                    🏠 Sur place
                                @elseif($order->type == 'takeaway')
                                    🥡 À emporter
                                @else
                                    🛵 Livraison
                                @endif
                            </span>
                        </p>
                        @if($order->table_number)
                            <p class="mb-2"><strong>Table :</strong> {{ $order->table_number }}</p>
                        @endif
                        <p class="mb-2">
                            <strong>Paiement :</strong> 
                            <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : ($order->payment_status == 'pending' ? 'warning' : 'danger') }}">
                                {{ $order->payment_status == 'paid' ? 'Payé' : ($order->payment_status == 'pending' ? 'En attente' : $order->payment_status) }}
                            </span>
                        </p>
                        <p class="mb-2">
                            <strong>Méthode :</strong> {{ ucfirst($order->payment_method ?? 'N/A') }}
                        </p>
                        <p class="mb-0">
                            <strong>Source :</strong> {{ $order->source ?? 'Web' }}
                        </p>
                    </div>
                </div>

                {{-- Notes --}}
                @if($order->notes || $order->kitchen_notes)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">📝 Notes</h5>
                        </div>
                        <div class="card-body">
                            @if($order->notes)
                                <p class="mb-2"><strong>Client :</strong></p>
                                <p class="text-muted">{{ $order->notes }}</p>
                            @endif
                            @if($order->kitchen_notes)
                                <hr>
                                <p class="mb-2"><strong>Cuisine :</strong></p>
                                <p class="text-muted">{{ $order->kitchen_notes }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal Assigner Livreur --}}
<div class="modal fade" id="assignDeliveryModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.orders.delivery', $order) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assigner un livreur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($deliveryPersons->count() > 0)
                        <select name="delivery_person_id" class="form-select" required>
                            <option value="">Choisir un livreur...</option>
                            @foreach($deliveryPersons as $person)
                                <option value="{{ $person->id }}">{{ $person->full_name }} ({{ $person->phone }})</option>
                            @endforeach
                        </select>
                    @else
                        <div class="alert alert-warning">Aucun livreur disponible</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    @if($deliveryPersons->count() > 0)
                        <button type="submit" class="btn btn-primary">Assigner</button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Annuler --}}
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.orders.cancel', $order) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Annuler la commande</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Vous êtes sur le point d'annuler la commande <strong>#{{ $order->order_number }}</strong>.</p>
                    <label class="form-label">Raison de l'annulation *</label>
                    <textarea name="reason" class="form-control" rows="3" required placeholder="Expliquez la raison..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-danger">Confirmer l'annulation</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
    .progress-steps .step.active { background-color: #0d6efd; color: white; }
    .progress-steps .step.completed { background-color: #198754; color: white; }
    .progress-steps .step.pending { background-color: #e9ecef; color: #6c757d; }
</style>
@endpush