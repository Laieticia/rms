@extends('layouts.app')

@section('title', 'Rapports')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h3><i class="bi bi-graph-up me-2"></i>Rapports</h3>
        <div>
            <select onchange="location.href='?period='+this.value" class="form-select d-inline w-auto me-2">
                <option value="today" {{$period=='today'?'selected':''}}>Aujourd'hui</option>
                <option value="week" {{$period=='week'?'selected':''}}>Semaine</option>
                <option value="month" {{$period=='month'?'selected':''}}>Mois</option>
                <option value="year" {{$period=='year'?'selected':''}}>Année</option>
            </select>
            <a href="{{ route('admin.reports.export', ['period'=>$period]) }}" class="btn btn-success"><i class="bi bi-download"></i> CSV</a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        @foreach([['Commandes',$salesStats['total_orders'],'primary','cart-check'],['CA',$salesStats['total_revenue'].'€','success','currency-euro'],['Panier moy.',$salesStats['average_order'].'€','info','graph-up'],['Taux complétion',$salesStats['completion_rate'].'%','warning','check-circle']] as $stat)
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
            @foreach($topCustomers as $i=>$c)<div class="d-flex justify-content-between mb-2"><span>{{$i+1}}. {{$c->full_name}}</span><span>{{number_format($c->total_spent??0,2)}}€</span></div>@endforeach
        </div></div></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('chartType'),{type:'doughnut',data:{labels:{!! json_encode($ordersByType->pluck('type')->map(fn($t)=>match($t){'dine_in'=>'Sur place','takeaway'=>'À emporter','delivery'=>'Livraison',default:$t})) !!},datasets:[{data:{!! json_encode($ordersByType->pluck('count')) !!},backgroundColor:['#0dcaf0','#ffc107','#dc3545']}]}});
new Chart(document.getElementById('chartStatus'),{type:'bar',data:{labels:{!! json_encode($ordersByStatus->pluck('status')) !!},datasets:[{label:'Nb',data:{!! json_encode($ordersByStatus->pluck('count')) !!},backgroundColor:'#198754'}]}});
new Chart(document.getElementById('chartSales'),{type:'line',data:{labels:{!! json_encode($dailySales->pluck('date')) !!},datasets:[{label:'€',data:{!! json_encode($dailySales->pluck('revenue')) !!},borderColor:'#e74c3c',tension:.3}]}});
</script>
@endpush