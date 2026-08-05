@extends('layouts.app')

@section('title', 'Réservations')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <h3 class="fw-bold mb-4"><i class="bi bi-calendar-check me-2"></i>Réservations</h3>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                        <small>En attente de confirmation</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0">{{ $stats['confirmed_today'] }}</h4>
                        <small>Confirmées aujourd'hui</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0">{{ $stats['total_today'] }}</h4>
                        <small>Total aujourd'hui</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.reservations.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <select name="status" class="form-control">
                            <option value="">Tous statuts</option>
                            @foreach(['pending'=>'En attente','confirmed'=>'Confirmée','cancelled'=>'Annulée','completed'=>'Terminée'] as $val => $label)
                                <option value="{{ $val }}" {{ request('status')==$val?'selected':'' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100"><i class="bi bi-filter"></i> Filtrer</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Client</th>
                            <th>Date / Heure</th>
                            <th>Personnes</th>
                            <th>Table</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservations as $reservation)
                            <tr>
                                <td>{{ $reservation->reservation_number }}</td>
                                <td>
                                    {{ $reservation->customer_name }}
                                    <div class="small text-muted">{{ $reservation->customer_phone }}</div>
                                </td>
                                <td>{{ $reservation->date->format('d/m/Y') }} à {{ $reservation->time->format('H:i') }}</td>
                                <td>{{ $reservation->guests_count }}</td>
                                <td>{{ $reservation->table_number ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-{{ match($reservation->status) {
                                        'confirmed' => 'success',
                                        'cancelled' => 'danger',
                                        'completed' => 'secondary',
                                        default => 'warning',
                                    } }}">
                                        {{ ucfirst($reservation->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.reservations.show', $reservation) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">Aucune réservation.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $reservations->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
