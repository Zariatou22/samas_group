@extends('layouts.admin')

@section('title', 'Nouvelle déclaration')

@section('content')
    <p>
        <a href="{{ route('admin.authorizations.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <form method="POST" action="{{ route('admin.authorizations.store') }}">
        @csrf
        <div class="card">
            <div class="card-header"><h5>Déclaration</h5></div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>BL *</label>
                        <select name="bl" class="form-control" required>
                            <option value="">Choisir...</option>
                            @foreach ($bls as $bl)
                                <option value="{{ $bl->id }}" @selected(old('bl') == $bl->id)>
                                    {{ $bl->bl }} — {{ $bl->mandataire?->customer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>N&deg; de déclaration *</label>
                        <input type="text" name="auth_number" class="form-control" value="{{ old('auth_number') }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Lieu d'enlèvement *</label>
                        <select name="source" class="form-control" required>
                            <option value="">Choisir...</option>
                            @foreach ($sources as $source)
                                <option value="{{ $source->id }}" @selected(old('source') == $source->id)>{{ $source->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <label>Nb conteneurs *</label>
                        <input type="number" name="nb_container" class="form-control" value="{{ old('nb_container', 0) }}" required>
                    </div>
                    <div class="col-md-2 form-group">
                        <label>Nb colis *</label>
                        <input type="number" name="nb_package" class="form-control" value="{{ old('nb_package', 0) }}" required>
                    </div>
                    <div class="col-md-2 form-group">
                        <label>Poids déclaré *</label>
                        <input type="number" step="0.01" name="quantity" class="form-control" value="{{ old('quantity', 0) }}" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mb-4">
            <button type="submit" class="btn btn-primary btn-lg">Enregistrer</button>
        </div>
    </form>
@endsection
