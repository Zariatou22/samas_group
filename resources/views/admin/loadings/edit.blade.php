@extends('layouts.admin')

@section('title', 'Modifier le chargement')

@section('content')
    <p>
        <a href="{{ route('admin.loadings.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <form method="POST" action="{{ route('admin.loadings.update', $loading) }}">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-header"><h5>Bon de livraison et autorisation</h5></div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>BL</label>
                        <input type="text" class="form-control" value="{{ $loading->parentBl?->bl }}" disabled>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>N&deg; de déclaration *</label>
                        <select name="authorization" class="form-control" required>
                            <option value="">Choisir...</option>
                            @foreach ($authorizations as $a)
                                <option value="{{ $a->id }}" @selected(old('authorization', $loading->authorization) == $a->id)>{{ $a->auth_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 form-group">
                        <label>Conteneurs (si applicable)</label>
                        <select name="containers[]" id="containersSelect" class="form-control" multiple>
                            @foreach ($containers as $c)
                                <option value="{{ $c->id }}" @selected(in_array($c->id, old('containers', $selectedContainerIds)))>{{ $c->type_tc }} — {{ $c->numero }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h5>Quantités transportées</h5></div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Nombre de colis *</label>
                        <input type="number" name="nb_package" class="form-control" value="{{ old('nb_package', $loading->nb_package) }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Poids *</label>
                        <input type="number" step="0.01" name="quantity" class="form-control" value="{{ old('quantity', $loading->quantity) }}" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h5>Transport</h5></div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Véhicule *</label>
                        <select name="car" class="form-control" required>
                            <option value="">Choisir...</option>
                            @foreach ($cars as $car)
                                <option value="{{ $car->id }}" @selected(old('car', $loading->car) == $car->id)>{{ $car->full_registration }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Lieu d'enlèvement *</label>
                        <select name="source" class="form-control" required>
                            <option value="">Choisir...</option>
                            @foreach ($sources as $source)
                                <option value="{{ $source->id }}" @selected(old('source', $loading->source) == $source->id)>{{ $source->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Date de chargement *</label>
                        <input type="datetime-local" name="loading_date" class="form-control" value="{{ old('loading_date', $loading->loading_date?->format('Y-m-d\TH:i')) }}" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mb-4">
            <button type="submit" class="btn btn-primary btn-lg">Enregistrer</button>
        </div>
    </form>
@endsection
