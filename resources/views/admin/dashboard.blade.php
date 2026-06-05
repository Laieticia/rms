@extends('layouts.app')

@section('title', 'Tableau de Bord')

@section('content')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @if(session('success'))
                <div class="alert text-white bg-success" role="alert">
                    <div class="iq-alert-icon">
                        <i class="ri-alert-line"></i>
                    </div>
                    <div class="iq-alert-text">{{ session('success') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
                @endif
                @if(session('error'))
                <div class="alert text-white bg-danger" role="alert">
                    <div class="iq-alert-icon">
                        <i class="ri-information-line"></i>
                    </div>
                    <div class="iq-alert-text">{{ session('error') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
                @endif
            </div>
        </div>
        <div class="row">
            <!-- En-tête de bienvenue -->
            <div class="col-lg-4">
                <div class="card card-transparent card-block card-stretch card-height border-none">
                    <div class="card-body p-0 mt-lg-2 mt-0">
                        <h3 class="mb-3">
                            Bonjour, {{ auth()->user()->full_name }} 👋
                        </h3>
                        <p class="mb-0 mr-4">
                            Votre tableau de bord vous donne un aperçu des performances clés de votre restaurant.
                        </p>
                        <div class="mt-3">
                            <span class="badge bg-primary">{{ ucfirst($period) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Cartes statistiques -->
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4 card-total-sale">
                                    <div class="icon iq-icon-box-2 bg-info-light">
                                        <i class="bi bi-cart-check fs-3 text-info"></i>
                                    </div>
                                    <div>
                                        <p class="mb-2">Total Commandes</p>
                                        <h4>{{ $todayStats['orders'] }}</h4>
                                    </div>
                                </div>
                                <div class="iq-progress-bar mt-2">
                                    <span class="bg-info iq-progress progress-1" data-percent="{{ $stats['pending_orders'] > 0 ? round(($todayStats['orders'] / ($todayStats['orders'] + $stats['pending_orders'])) * 100) : 0 }}"></span>
                                </div>
                                <small class="text-muted">{{ $stats['pending_orders'] }} en attente</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4 card-total-sale">
                                    <div class="icon iq-icon-box-2 bg-danger-light">
                                        <i class="bi bi-currency-euro fs-3 text-danger"></i>
                                    </div>
                                    <div>
                                        <p class="mb-2">Revenu Total</p>
                                        <h4>{{ number_format($todayStats['revenue'], 0) }} €</h4>
                                    </div>
                                </div>
                                <div class="iq-progress-bar mt-2">
                                    <span class="bg-danger iq-progress progress-1" data-percent="70"></span>
                                </div>
                                <small class="text-muted">Panier moyen: {{ number_format($todayStats['average_order'], 2) }} €</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4 card-total-sale">
                                    <div class="icon iq-icon-box-2 bg-success-light">
                                        <i class="bi bi-people fs-3 text-success"></i>
                                    </div>
                                    <div>
                                        <p class="mb-2">Clients</p>
                                        <h4>{{ $stats['total_customers'] }}</h4>
                                    </div>
                                </div>
                                <div class="iq-progress-bar mt-2">
                                    <span class="bg-success iq-progress progress-1" data-percent="{{ $stats['total_customers'] > 0 ? round(($todayStats['new_customers'] / $stats['total_customers']) * 100) : 0 }}"></span>
                                </div>
                                <small class="text-muted">{{ $todayStats['new_customers'] }} nouveaux</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Graphique Overview -->
            <div class="col-lg-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Aperçu des ventes</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <div class="dropdown">
                                <span class="dropdown-toggle dropdown-bg btn" id="dropdownMenuButton001" data-bs-toggle="dropdown">
                                    {{ $period == 'today' ? 'Aujourd\'hui' : ($period == 'week' ? 'Cette semaine' : ($period == 'month' ? 'Ce mois' : 'Cette année')) }}
                                    <i class="ri-arrow-down-s-line ml-1"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right shadow-none" aria-labelledby="dropdownMenuButton001">
                                    <a class="dropdown-item" href="?period=today">Aujourd'hui</a>
                                    <a class="dropdown-item" href="?period=week">Cette semaine</a>
                                    <a class="dropdown-item" href="?period=month">Ce mois</a>
                                    <a class="dropdown-item" href="?period=year">Cette année</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="layout1-chart1" height="200"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Graphique Commandes par type -->
            <div class="col-lg-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Commandes par type</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <div class="dropdown">
                                <span class="dropdown-toggle dropdown-bg btn" id="dropdownMenuButton002" data-bs-toggle="dropdown">
                                    {{ $period == 'today' ? 'Aujourd\'hui' : ($period == 'week' ? 'Cette semaine' : 'Ce mois') }}
                                    <i class="ri-arrow-down-s-line ml-1"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right shadow-none" aria-labelledby="dropdownMenuButton002">
                                    <a class="dropdown-item" href="?period=today">Aujourd'hui</a>
                                    <a class="dropdown-item" href="?period=week">Cette semaine</a>
                                    <a class="dropdown-item" href="?period=month">Ce mois</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="layout1-chart-2" style="min-height: 300px;"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Top Produits -->
            <div class="col-lg-8">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">🏆 Top Produits</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-primary view-btn font-size-14">Voir tout</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled row top-product mb-0">
                            @forelse($topProducts as $index => $product)
                                <li class="col-lg-3 col-md-6">
                                    <div class="card card-block card-stretch card-height mb-0">
                                        <div class="card-body text-center">
                                            <div class="bg-{{ ['warning', 'danger', 'info', 'success'][$index % 4] }}-light rounded p-3">
                                                <img src="{{ $product->primary_image_url ?? asset('admin/assets/images/product/0'.($index+1).'.png') }}" 
                                                     class="style-img img-fluid m-auto" 
                                                     style="max-height: 120px;"
                                                     alt="{{ $product->name }}">
                                            </div>
                                            <div class="style-text text-left mt-3">
                                                <h5 class="mb-1">{{ $product->name }}</h5>
                                                <p class="mb-0 text-muted">{{ $product->total_sold ?? 0 }} vendus</p>
                                                <p class="mb-0 fw-bold">{{ number_format($product->price, 2) }} €</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="col-12 text-center py-4">
                                    <p class="text-muted">Aucune vente cette période</p>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Meilleurs produits de tous les temps -->
            <div class="col-lg-4">
                <div class="card card-transparent card-block card-stretch mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between p-0">
                        <div class="header-title">
                            <h4 class="card-title mb-0">⭐ Meilleures ventes</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <div><a href="{{ route('admin.products.index') }}" class="btn btn-primary view-btn font-size-14">Voir tout</a></div>
                        </div>
                    </div>
                </div>
                
                @foreach($topProducts->take(2) as $product)
                    <div class="card card-block card-stretch card-height-helf mb-3">
                        <div class="card-body card-item-right">
                            <div class="d-flex align-items-top">
                                <div class="bg-warning-light rounded p-2">
                                    <img src="{{ $product->primary_image_url ?? asset('admin/assets/images/product/04.png') }}" 
                                         class="style-img img-fluid m-auto" 
                                         style="width:60px;height:60px;object-fit:cover;"
                                         alt="{{ $product->name }}">
                                </div>
                                <div class="style-text text-left ms-3">
                                    <h5 class="mb-2">{{ $product->name }}</h5>
                                    <p class="mb-2">Total vendus : <strong>{{ $product->total_sold ?? 0 }}</strong></p>
                                    <p class="mb-0">Revenu : <strong>{{ number_format(($product->total_sold ?? 0) * $product->price, 2) }} €</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Commandes en attente et préparation -->
            <div class="col-lg-4">
                <div class="card card-block card-stretch card-height-helf">
                    <div class="card-body">
                        <div class="d-flex align-items-top justify-content-between">
                            <div class="">
                                <p class="mb-0">Commandes en attente</p>
                                <h5>{{ $stats['pending_orders'] }}</h5>
                            </div>
                            <div class="card-header-toolbar d-flex align-items-center">
                                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="btn btn-sm btn-warning">Voir</a>
                            </div>
                        </div>
                        <div class="mt-3">
                            @foreach($pendingOrders->take(3) as $order)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small>#{{ $order->order_number }}</small>
                                    <small>{{ $order->created_at->diffForHumans() }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="card card-block card-stretch card-height-helf">
                    <div class="card-body">
                        <div class="d-flex align-items-top justify-content-between">
                            <div class="">
                                <p class="mb-0">En préparation</p>
                                <h5>{{ $stats['preparing_orders'] }}</h5>
                            </div>
                            <div class="card-header-toolbar d-flex align-items-center">
                                <a href="{{ route('admin.orders.index', ['status' => 'preparing']) }}" class="btn btn-sm btn-info">Voir</a>
                            </div>
                        </div>
                        <div class="mt-3">
                            <p class="mb-1">Prêtes : <strong>{{ $stats['ready_orders'] }}</strong></p>
                            <p class="mb-0">En livraison : <strong>{{ $stats['in_delivery_orders'] }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Résumé des commandes -->
            <div class="col-lg-8">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">📋 Dernières commandes</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-primary view-btn font-size-14">Voir tout</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>N° Commande</th>
                                        <th>Client</th>
                                        <th>Type</th>
                                        <th>Total</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentOrders as $order)
                                        <tr>
                                            <td><strong>#{{ $order->order_number }}</strong></td>
                                            <td>{{ $order->user->full_name }}</td>
                                            <td>
                                                @if($order->type == 'dine_in')
                                                    <span class="badge bg-info">Sur place</span>
                                                @elseif($order->type == 'takeaway')
                                                    <span class="badge bg-warning">À emporter</span>
                                                @else
                                                    <span class="badge bg-primary">Livraison</span>
                                                @endif
                                            </td>
                                            <td><strong>{{ number_format($order->total, 2) }} €</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span>
                                            </td>
                                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Aucune commande récente</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>  
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold"><i class="bi bi-speedometer2 me-2"></i>Tableau de bord</h3>
        <div>
            <!-- Sélecteur de période -->
            <select class="form-select d-inline-block w-auto me-2" onchange="window.location.href='?period='+this.value">
                <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                <option value="yesterday" {{ $period == 'yesterday' ? 'selected' : '' }}>Hier</option>
                <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Cette semaine</option>
                <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Ce mois</option>
                <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Cette année</option>
            </select>
            <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                <i class="bi bi-printer"></i> Imprimer
            </button>
        </div>
    </div>
    
    <!-- Cartes statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50">Commandes</h6>
                            <h2 class="mb-0">{{ $todayStats['orders'] }}</h2>
                            <small>{{ $period == 'today' ? 'aujourd\'hui' : 'cette période' }}</small>
                        </div>
                        <i class="bi bi-cart-check display-4 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50">Revenu</h6>
                            <h2 class="mb-0">{{ number_format($todayStats['revenue'], 0) }} €</h2>
                            <small>total des ventes</small>
                        </div>
                        <i class="bi bi-currency-euro display-4 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50">Nouveaux clients</h6>
                            <h2 class="mb-0">{{ $todayStats['new_customers'] }}</h2>
                            <small>première commande</small>
                        </div>
                        <i class="bi bi-person-plus display-4 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-white-50">Panier moyen</h6>
                            <h2 class="mb-0">{{ number_format($todayStats['average_order'], 2) }} €</h2>
                            <small>par commande</small>
                        </div>
                        <i class="bi bi-graph-up display-4 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <!-- Commandes en attente -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <h5 class="mb-0">📋 Commandes en attente</h5>
                    <span class="badge bg-warning fs-6">{{ $pendingOrders->count() }}</span>
                </div>
                <div class="card-body">
                    @forelse($pendingOrders as $order)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width:40px;height:40px;">
                                        <i class="bi bi-clock text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <strong>#{{ $order->order_number }}</strong>
                                    <br><small class="text-muted">{{ $order->user->full_name }}</small>
                                    <br><small class="text-muted">{{ number_format($order->total, 2) }} €</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-warning mb-1">{{ $order->status_label }}</span>
                                <br><small class="text-muted">{{ $order->created_at->diffForHumans() }}</small>
                            </div>
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="bi bi-inbox display-4 text-muted"></i>
                            <p class="text-muted mt-2">Aucune commande en attente</p>
                        </div>
                    @endforelse
                    
                    @if($pendingOrders->count() > 0)
                        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="btn btn-outline-warning btn-sm w-100">
                            Voir toutes les commandes en attente
                        </a>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Top Produits -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">🏆 Top 5 Produits</h5>
                </div>
                <div class="card-body">
                    @forelse($topProducts as $index => $product)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-{{ $index < 3 ? 'warning' : 'secondary' }} me-2" 
                                      style="width:25px;height:25px;display:inline-flex;align-items:center;justify-content:center;">
                                    {{ $index + 1 }}
                                </span>
                                <img src="{{ $product->primary_image_url ?? 'https://via.placeholder.com/40' }}" 
                                     class="rounded me-2" width="40" height="40" style="object-fit:cover;" alt="">
                                <div>
                                    <strong>{{ $product->name }}</strong>
                                    <br><small class="text-muted">{{ $product->category->name ?? 'N/A' }} - {{ number_format($product->price, 2) }} €</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success">{{ $product->total_sold ?? 0 }} ventes</span>
                                <br><small class="text-muted">{{ number_format(($product->total_sold ?? 0) * $product->price, 2) }} €</small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="bi bi-box-seam display-4 text-muted"></i>
                            <p class="text-muted mt-2">Aucune vente cette période</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <!-- Graphique des ventes -->
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">📊 Ventes des 7 derniers jours</h5>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="80"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Avis récents -->
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">⭐ Avis récents</h5>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-primary btn-sm">Voir tout</a>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($recentReviews as $review)
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $review->user->avatar_url }}" class="rounded-circle me-2" width="30" height="30" alt="">
                                            <strong>{{ $review->user->full_name }}</strong>
                                        </div>
                                        <div class="text-warning">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }} small"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="mb-1 small">{{ Str::limit($review->comment, 100) }}</p>
                                    <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-3">
                                <p class="text-muted">Aucun avis récent</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique des ventes (ligne)
    const ctx1 = document.getElementById('layout1-chart1');
    if (ctx1) {
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: {!! json_encode($salesChart->pluck('date')) !!},
                datasets: [{
                    label: 'Revenus (€)',
                    data: {!! json_encode($salesChart->pluck('revenue')) !!},
                    borderColor: '#0dcaf0',
                    backgroundColor: 'rgba(13, 202, 240, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointBackgroundColor: '#0dcaf0',
                }, {
                    label: 'Commandes',
                    data: {!! json_encode($salesChart->pluck('orders')) !!},
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointBackgroundColor: '#dc3545',
                    yAxisID: 'y1',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: '€' }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        title: { display: true, text: 'Nb' }
                    }
                }
            }
        });
    }

    // Graphique par type (doughnut)
    const ctx2 = document.getElementById('layout1-chart-2');
    if (ctx2) {
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($orderTypes->pluck('type')->map(fn($t) => match($t) {'dine_in' => 'Sur place', 'takeaway' => 'À emporter', 'delivery' => 'Livraison', default => $t})) !!},
                datasets: [{
                    data: {!! json_encode($orderTypes->pluck('count')) !!},
                    backgroundColor: ['#0dcaf0', '#ffc107', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Animation des barres de progression
    document.querySelectorAll('.iq-progress').forEach(function(progress) {
        const percent = progress.getAttribute('data-percent');
        setTimeout(function() {
            progress.style.width = percent + '%';
        }, 500);
    });
});
</script>
@endpush