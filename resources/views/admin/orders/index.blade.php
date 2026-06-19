@extends('layouts.app')

@section('title', 'Gestion des orders')

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
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <div>
                                    <h4 class="mb-3">Orders</h4>
                                </div>
                                <a href="{{ route('admin.orders.create') }}" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Nouveau Order</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                   
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive rounded mb-3">
                                <table class="table mb-0 tbl-server-info">
                                    <thead class="bg-white text-uppercase">
                                        <tr class="ligth ligth-data">
                                            <th>Number</th>
                                            <th>Type</th>
                                            <th>Statut</th>
                                            <th>Total</th>
                                            <th>Payment method</th>
                                            <th>Payment Status</th>
                                            <th>Delivery adress</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="ligth-body">
                                        @forelse($orders as $order)
                                            <tr>  
                                                 <td>
                                                    <div class="d-flex align-items-center flex">
                                                        
                                                        <div>
                                                            <strong>{{ $order->order_number }}</strong>
                                                        </div>
                                                        <td>
                                                              <div>
                                                            <strong>{{ $order->type }}</strong>
                                                         
                                                        </div>
                                                        </td>
                                                          <td>
                                                    @if($order->status)
                                                        <span class="badge bg-success">Actif</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactif</span>
                                                    @endif
                                                </td>
                                                          <td>
                                                              <div>
                                                            <strong>{{ $order->total }}</strong>
                                                         
                                                        </div>
                                                        </td>
                                                          <td>
                                                              <div>
                                                            <strong>{{ $order->payment_method }}</strong>
                                                         
                                                        </div>
                                                        </td>
                                                             <td>
                                                    @if($order->payment_status)
                                                        <span class="badge bg-success">Paid</span>
                                                    @else
                                                        <span class="badge bg-danger">Refunded</span>
                                                    @endif
                                                </td>
                                                    </div>
                                                </td> 
                                                 <td>
                                                              <div>
                                                            <strong>{{ $order->delivery_address }}</strong>
                                                         
                                                        </div>
                                                        </td>                                          
                                                <td>
                                                    <div class="d-flex align-items-center list-action">
                                                        <button type="button" class="badge badge-info mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Stock" data-bs-target="#stockModal{{ $order->id }}">
                                                            <i class="ri-star-fill mr-2"></i>
                                                        </button>
                                                        <a class="badge bg-success mr-2" data-toggle="tooltip"
                                                            data-placement="top" title="" data-original-title="Edit" href="{{ route('admin.orders.edit', $order) }}">
                                                            <i class="ri-pencil-line mr-0"></i>
                                                        </a>
                                                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="d-inline">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="badge bg-warning mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Supprimer" onclick="return confirm('Supprimer ce order ?')">
                                                                <i class="ri-delete-bin-line mr-0"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <i class="bi bi-inbox display-4 text-muted"></i>
                                                    <p class="mt-2">Aucun order trouvé</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                             {{ $orders->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')

@endpush