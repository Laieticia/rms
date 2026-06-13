@extends('layouts.app')

@section('title', 'Paramètres')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4"><i class="bi bi-gear me-2"></i>Paramètres</h3>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><h5>Informations générales</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.restaurant') }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Nom</label><input name="name" class="form-control" value="{{$restaurant->name}}" required></div>
                            <div class="col-md-6"><label class="form-label">Email</label><input name="email" type="email" class="form-control" value="{{$restaurant->email}}" required></div>
                            <div class="col-md-6"><label class="form-label">Téléphone</label><input name="phone" class="form-control" value="{{$restaurant->phone}}" required></div>
                            <div class="col-12"><label class="form-label">Adresse</label><input name="address" class="form-control" value="{{$restaurant->address}}" required></div>
                            <div class="col-md-4"><label class="form-label">Ville</label><input name="city" class="form-control" value="{{$restaurant->city}}" required></div>
                            <div class="col-md-4"><label class="form-label">Code postal</label><input name="postal_code" class="form-control" value="{{$restaurant->postal_code}}" required></div>
                            <div class="col-md-4"><label class="form-label">Commande min (€)</label><input name="minimum_order" type="number" step="0.01" class="form-control" value="{{$restaurant->minimum_order}}"></div>
                            <div class="col-md-3"><label class="form-label">Livraison (€)</label><input name="delivery_fee" type="number" step="0.01" class="form-control" value="{{$restaurant->delivery_fee}}"></div>
                            <div class="col-md-3"><label class="form-label">TVA (%)</label><input name="tax_rate" type="number" step="0.01" class="form-control" value="{{$restaurant->tax_rate}}"></div>
                            <div class="col-md-3"><label class="form-label">Temps livraison (min)</label><input name="estimated_delivery_time" type="number" class="form-control" value="{{$restaurant->estimated_delivery_time}}"></div>
                        </div>
                        <div class="mt-3">
                            <div class="form-check form-switch d-inline me-3"><input name="accepts_delivery" value="1" class="form-check-input" type="checkbox" {{$restaurant->accepts_delivery?'checked':''}}><label>Livraison</label></div>
                            <div class="form-check form-switch d-inline me-3"><input name="accepts_takeaway" value="1" class="form-check-input" type="checkbox" {{$restaurant->accepts_takeaway?'checked':''}}><label>À emporter</label></div>
                            <div class="form-check form-switch d-inline"><input name="accepts_dine_in" value="1" class="form-check-input" type="checkbox" {{$restaurant->accepts_dine_in?'checked':''}}><label>Sur place</label></div>
                        </div>
                        <button class="btn btn-primary mt-3">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header"><h5>Logo</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.restaurant') }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        @if($restaurant->logo_url)<img src="{{$restaurant->logo_url}}" class="img-fluid rounded mb-2" style="max-height:100px">@endif
                        <input name="logo" type="file" class="form-control mb-2" accept="image/*">
                        <button class="btn btn-primary w-100">Upload</button>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h5>Horaires</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.hours') }}" method="POST">
                        @csrf @method('PUT')
                        @foreach($days as $key=>$label)
                            @php $hour = $restaurant->operatingHours->where('day',$key)->first(); @endphp
                            <div class="row g-2 mb-2 align-items-center">
                                <div class="col-3"><strong>{{$label}}</strong></div>
                                <div class="col-3"><input name="hours[{{$key}}][open_time]" type="time" class="form-control form-control-sm" value="{{$hour?->open_time?->format('H:i')??'09:00'}}" {{$hour&&$hour->is_closed?'disabled':''}}></div>
                                <div class="col-3"><input name="hours[{{$key}}][close_time]" type="time" class="form-control form-control-sm" value="{{$hour?->close_time?->format('H:i')??'22:00'}}" {{$hour&&$hour->is_closed?'disabled':''}}></div>
                                <div class="col-3"><div class="form-check"><input name="hours[{{$key}}][is_closed]" value="1" class="form-check-input" type="checkbox" {{$hour&&$hour->is_closed?'checked':''}} onchange="const r=this.closest('.row');r.querySelectorAll('input[type=time]').forEach(i=>i.disabled=this.checked)"><label>Fermé</label></div></div>
                            </div>
                        @endforeach
                        <button class="btn btn-primary w-100 mt-2">Enregistrer horaires</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection