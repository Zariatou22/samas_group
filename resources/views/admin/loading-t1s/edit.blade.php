@extends('layouts.admin')

@section('title', 'Modifier le T1 '.$loadingT1->t1_number)

@section('content')
    <p>
        <a href="{{ route('admin.loading-t1s.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <form method="POST" action="{{ route('admin.loading-t1s.update', $loadingT1) }}">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-header"><h5>T1</h5></div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label>Chargement</label>
                        <input type="text" class="form-control" value="{{ $loadingT1->parentLoading?->parentBl?->bl }} — {{ $loadingT1->parentLoading?->vehicle?->full_registration }} — {{ $loadingT1->parentLoading?->mandataire?->customer_name }}" disabled>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>N&deg; T1 *</label>
                        <input type="number" name="t1_number" class="form-control" value="{{ old('t1_number', $loadingT1->t1_number) }}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Date de validité *</label>
                        <input type="datetime-local" name="valid_until" class="form-control" value="{{ old('valid_until', $loadingT1->valid_until?->format('Y-m-d\TH:i')) }}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Date de validation</label>
                        <input type="datetime-local" name="validate" class="form-control" value="{{ old('validate', $loadingT1->validate?->format('Y-m-d\TH:i')) }}">
                        <small class="form-text text-muted">Laisser vide tant que le T1 n'est pas validé.</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mb-4">
            <button type="submit" class="btn btn-primary btn-lg">Enregistrer</button>
        </div>
    </form>
@endsection
