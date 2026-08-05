@extends('layouts.app')

@section('title', 'Rapports')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb-4">
            <h3><i class="bi bi-graph-up me-2"></i>Rapports</h3>
            <div>
                <select onchange="location.href='?period='+this.value" class="form-control d-inline w-auto me-2">
                    <option value="today" {{$period=='today'?'selected':''}}>Aujourd'hui</option>
                    <option value="week" {{$period=='week'?'selected':''}}>Semaine</option>
                    <option value="month" {{$period=='month'?'selected':''}}>Mois</option>
                    <option value="year" {{$period=='year'?'selected':''}}>Année</option>
                </select>
                <a href="{{ route('admin.reports.export', ['period'=>$period]) }}" class="btn btn-success"><i class="bi bi-download"></i> CSV</a>
            </div>
        </div>

        <div class="row g-4 mb-4">
            @foreach([['Commandes',$salesStats['total_orders'],'primary','cart-check'],['CA',\App\Helpers\CameroonHelper::formatCurrency($salesStats['total_revenue']),'success','cash-stack'],['Panier moy.',\App\Helpers\CameroonHelper::formatCurrency($salesStats['average_order']),'info','graph-up'],['Taux complétion',$salesStats['completion_rate'].'%','warning','check-circle']] as $stat)
                <div class="col-md-3">
                    <div class="card bg-{{$stat[2]}} text-white"><div class="card-body"><h6>{{$stat[0]}}</h6><h3>{{$stat[1]}}</h3></div></div>
                </div>
            @endforeach
        </div>

        <div class="row g-4">
            <div class="col-md-6"><div class="card"><div class="card-header">Commandes par type</div><div class="card-body"><canvas id="chartType"></canvas></div></div></div>
            <div class="col-md-6"><div class="card"><div class="card-header">Commandes par statut</div><div class="card-body"><canvas id="chartStatus"></canvas></div></div></div>
            <div class="col-12"><div class="card"><div class="card-header">Ventes</div><div class="card-body"><canvas id="chartSales" height="80"></canvas></div></div></div>
            <div class="col-md-6"><div class="card"><div class="card-header">Top Produits</div><div class="card-body">
                @foreach($topProducts as $i=>$p)<div class="d-flex justify-content-between mb-2"><span>{{$i+1}}. {{$p->name}}</span><span class="badge bg-success">{{$p->total_sold}}</span></div>@endforeach
            </div></div></div>
            <div class="col-md-6"><div class="card"><div class="card-header">Top Clients</div><div class="card-body">
                @foreach($topCustomers as $i=>$c)<div class="d-flex justify-content-between mb-2"><span>{{$i+1}}. {{$c->full_name}}</span><span>{{ \App\Helpers\CameroonHelper::formatCurrency($c->total_spent ?? 0) }}</span></div>@endforeach
            </div></div></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Graphique par type (doughnut)
        const ctxType = document.getElementById('chartType');
        if (ctxType) {
            new Chart(ctxType, {
                type: 'doughnut',
                data: {
                    labels: [
                        @foreach($ordersByType as $item)
                            '{{ match($item->type) {
                                "dine_in" => "Sur place",
                                "takeaway" => "À emporter",
                                "delivery" => "Livraison",
                                default => $item->type
                            } }}',
                        @endforeach
                    ],
                    datasets: [{
                        data: [
                            @foreach($ordersByType as $item)
                                {{ $item->count }},
                            @endforeach
                        ],
                        backgroundColor: ['#0dcaf0', '#ffc107', '#dc3545'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }

        // Graphique par statut (bar)
        const ctxStatus = document.getElementById('chartStatus');
        if (ctxStatus) {
            new Chart(ctxStatus, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($ordersByStatus->pluck('status')->toArray()) !!},
                    datasets: [{
                        label: 'Nombre',
                        data: {!! json_encode($ordersByStatus->pluck('count')->toArray()) !!},
                        backgroundColor: '#198754',
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        // Graphique des ventes (line)
        const ctxSales = document.getElementById('chartSales');
        if (ctxSales) {
            new Chart(ctxSales, {
                type: 'line',
                data: {
                    labels: {!! json_encode($dailySales->pluck('date')->toArray()) !!},
                    datasets: [{
                        label: 'Revenus (FCFA)',
                        data: {!! json_encode($dailySales->pluck('revenue')->toArray()) !!},
                        borderColor: '#e74c3c',
                        backgroundColor: 'rgba(231, 76, 60, 0.1)',
                        fill: true,
                        tension: 0.3,
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: '#e74c3c'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + ' FCFA';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush