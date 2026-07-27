@extends('layouts.admin')

@section('title', 'Modifier le BL '.$bl->bl)

@section('content')
    <p>
        <a href="{{ route('admin.bls.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <form method="POST" action="{{ route('admin.bls.update', $bl) }}">
        @csrf
        @method('PUT')
        @include('admin.bls._form')
    </form>

    <div class="card">
        <div class="card-header"><h5>Suivi</h5></div>
        <div class="card-block">
            <div class="row">
                <div class="col-md-4">
                    <form method="POST" action="{{ route('admin.bls.exchange', $bl) }}">
                        @csrf
                        <label>Échange BL</label>
                        <input type="date" name="date_received" class="form-control" value="{{ optional($bl->exchange?->date_received)->format('Y-m-d') }}">
                        <button type="submit" class="btn btn-sm btn-info mt-2">Enregistrer</button>
                    </form>
                </div>
                <div class="col-md-4">
                    <form method="POST" action="{{ route('admin.bls.bad', $bl) }}">
                        @csrf
                        <label>BAD - Date de réception</label>
                        <input type="date" name="date_received" class="form-control mb-2" value="{{ optional($bl->deliveryNote?->date_received)->format('Y-m-d') }}">
                        <label>BAD - Date de validité</label>
                        <input type="date" name="date_valid" class="form-control" value="{{ optional($bl->deliveryNote?->date_valid)->format('Y-m-d') }}">
                        <button type="submit" class="btn btn-sm btn-info mt-2">Enregistrer</button>
                    </form>
                </div>
                <div class="col-md-4">
                    <label>Date de transfert</label>
                    <form method="POST" action="{{ route('admin.bls.transfert', $bl) }}">
                        @csrf
                        <div class="mb-2" style="max-height: 150px; overflow-y: auto;">
                            @forelse ($containers as $container)
                                <div class="form-check">
                                    <input type="checkbox" name="containers[]" value="{{ $container->id }}" class="form-check-input" id="transfertContainer{{ $container->id }}">
                                    <label class="form-check-label" for="transfertContainer{{ $container->id }}">{{ $container->type_tc }} — {{ $container->numero }}</label>
                                </div>
                            @empty
                                <p class="text-muted mb-0">Aucun conteneur.</p>
                            @endforelse
                        </div>
                        <input type="date" name="date_received" class="form-control mb-2" value="{{ now()->format('Y-m-d') }}" required>
                        <button type="submit" class="btn btn-sm btn-info">Enregistrer</button>
                    </form>
                    @if ($transferts->isNotEmpty())
                        <table class="table table-sm table-bordered mt-2">
                            <thead><tr><th>Conteneur</th><th>Date</th><th></th></tr></thead>
                            <tbody>
                                @foreach ($transferts as $transfert)
                                    <tr>
                                        <td>{{ $transfert->parentContainer?->numero }}</td>
                                        <td>{{ $transfert->date_received?->format('d/m/Y') }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.bls.transfert.destroy', [$bl, $transfert]) }}" onsubmit="return confirm('Annuler ce transfert ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
