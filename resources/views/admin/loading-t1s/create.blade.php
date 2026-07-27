@extends('layouts.admin')

@section('title', 'Nouveau T1')

@section('content')
    <p>
        <a href="{{ route('admin.loading-t1s.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <form method="POST" action="{{ route('admin.loading-t1s.store') }}">
        @csrf
        <div class="card">
            <div class="card-header"><h5>T1</h5></div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label>Chargement *</label>
                        <select name="loading" class="form-control" required>
                            <option value="">Choisir...</option>
                            @foreach ($loadings as $loading)
                                <option value="{{ $loading->id }}" @selected(old('loading') == $loading->id)>
                                    {{ $loading->parentBl?->bl }} — {{ $loading->vehicle?->full_registration }} — {{ $loading->mandataire?->customer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>N&deg; T1 *</label>
                        <input type="number" name="t1_number" class="form-control" value="{{ old('t1_number') }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Date de validité *</label>
                        <input type="datetime-local" name="valid_until" class="form-control" value="{{ old('valid_until', now()->addDays(3)->format('Y-m-d\TH:i')) }}" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mb-4">
            <button type="submit" class="btn btn-primary btn-lg">Enregistrer</button>
        </div>
    </form>
@endsection
