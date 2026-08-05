@extends('layouts.app')

@section('title', 'Gestion des Commandes')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <h3 class="fw-bold mb-4"><i class="bi bi-cart-check me-2"></i>Commandes</h3>

        <!-- Stats -->
        <div class="row g-3 mb-4">
            @foreach(['pending'=>'En attente|warning','preparing'=>'En préparation|info','ready'=>'Prêtes|success','in_delivery'=>'En livraison|primary'] as $status => $info)
                @php [$label, $color] = explode('|', $info); @endphp
                <div class="col-md-3">
                    <div class="card bg-{{ $color }} text-white">
                        <div class="card-body text-center py-3">
                            <h4 class="mb-0">{{ $stats[$status] ?? 0 }}</h4>
                            <small>{{ $label }}</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Filtres -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3">
                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="">Tous statuts</option>
                            @foreach(['pending'=>'En attente','confirmed'=>'Confirmée','preparing'=>'En préparation','ready'=>'Prête','in_delivery'=>'En livraison','delivered'=>'Livrée','cancelled'=>'Annulée'] as $val => $label)
                                <option value="{{ $val }}" {{ request('status')==$val?'selected':'' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="type" class="form-control">
                            <option value="">Tous types</option>
                            <option value="dine_in" {{ request('type')=='dine_in'?'selected':'' }}>Sur place</option>
                            <option value="takeaway" {{ request('type')=='takeaway'?'selected':'' }}>À emporter</option>
                            <option value="delivery" {{ request('type')=='delivery'?'selected':'' }}>Livraison</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="N° commande ou client..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Filtrer</button>
                    </div>
                    <div class="col-md-1">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>N° Commande</th>
                                <th>Client</th>
                                <th>Type</th>
                                <th>Articles</th>
                                <th>Total</th>
                                <th>Paiement</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td><strong>#{{ $order->order_number }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $order->user->avatar_url }}" class="rounded-circle me-2" width="30" height="30">
                                            <div>
                                                <strong>{{ $order->user->full_name }}</strong>
                                                <br><small class="text-muted">{{ $order->user->phone }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $order->type=='dine_in'?'info':($order->type=='takeaway'?'warning':'primary') }}">
                                            {{ $order->type=='dine_in'?'🏠':($order->type=='takeaway'?'🥡':'🛵') }}
                                        </span>
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $order->items->sum('quantity') }}</span></td>
                                    <td><strong>{{ \App\Helpers\CameroonHelper::formatCurrency($order->total) }}</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $order->payment_status=='paid'?'success':'warning' }}">
                                            {{ $order->payment_status=='paid'?'Payé':'En attente' }}
                                        </span>
                                    </td>
                                    <td><span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span></td>
                                    <td><small>{{ $order->created_at->format('d/m/Y H:i') }}</small></td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-primary">
                                            <i class="ri-eye-line mr-0"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center py-5">Aucune commande</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">{{ $orders->links() }}</div>
        </div>
    </div>
</div>
@endsection