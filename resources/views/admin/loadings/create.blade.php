@extends('layouts.admin')

@section('title', $isUnloading ? 'Nouveau dépotage' : 'Nouveau chargement')

@section('content')
    <p>
        <a href="{{ route($listRoute) }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <form method="POST" action="{{ route($storeRoute) }}">
        @csrf
        <div class="card">
            <div class="card-header"><h5>Bon de livraison et autorisation</h5></div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>BL *</label>
                        <select name="bl" id="blSelect" class="form-control" required onchange="onBlChange()">
                            <option value="">Choisir...</option>
                            @foreach ($bls as $bl)
                                <option value="{{ $bl->id }}" data-bad-valid="{{ optional($bl->deliveryNote?->date_valid)->format('Y-m-d') }}" @selected(old('bl') == $bl->id)>{{ $bl->bl }} — {{ $bl->mandataire?->customer_name }} (restant : {{ $bl->availableForLoadingType($isUnloading ? 1 : 0) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Validité BAD</label>
                        <input type="date" name="bad_valid_date" id="badValidDate" class="form-control" value="{{ old('bad_valid_date') }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>N&deg; de déclaration *</label>
                        <select name="authorization" id="authSelect" class="form-control" required>
                            <option value="">Choisir un BL d'abord...</option>
                        </select>
                    </div>
                    <div class="col-md-12 form-group">
                        <label>Conteneurs (si applicable)</label>
                        <select name="containers[]" id="containersSelect" class="form-control" multiple>
                        </select>
                        <small class="form-text text-muted">Pour un BL en dépotage : sélectionner les conteneurs enlevés par ce transport.</small>
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
                        <input type="number" name="nb_package" class="form-control" value="{{ old('nb_package', 0) }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Poids *</label>
                        <input type="number" step="0.01" name="quantity" class="form-control" value="{{ old('quantity', 0) }}" required>
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
                        <select name="car" id="carSelect" class="form-control" required onchange="onCarChange()">
                            <option value="">Choisir...</option>
                            @foreach ($cars as $car)
                                <option value="{{ $car->id }}" @selected(old('car') == $car->id)>{{ $car->full_registration }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted" id="carHelper"></small>
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
                    <div class="col-md-6 form-group">
                        <label>Date de chargement *</label>
                        <input type="datetime-local" name="loading_date" class="form-control" value="{{ old('loading_date', now()->format('Y-m-d\TH:i')) }}" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mb-4">
            <button type="submit" class="btn btn-primary btn-lg">Enregistrer</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        const authorizationsByBl = @json($authorizationsByBl);
        const containersByBl = @json($containersByBl);
        const cars = @json($cars);

        function onBlChange() {
            const blSelect = document.getElementById('blSelect');
            const blId = blSelect.value;
            const authSelect = document.getElementById('authSelect');
            const containersSelect = document.getElementById('containersSelect');

            // Pré-remplir la validité BAD du BL sélectionné (peut ensuite être modifiée à la main).
            const selectedOption = blSelect.options[blSelect.selectedIndex];
            document.getElementById('badValidDate').value = selectedOption?.dataset?.badValid || '';

            authSelect.innerHTML = '<option value="">Choisir...</option>';
            (authorizationsByBl[blId] ?? []).forEach(a => {
                const opt = document.createElement('option');
                opt.value = a.id;
                opt.textContent = a.auth_number;
                authSelect.appendChild(opt);
            });

            containersSelect.innerHTML = '';
            (containersByBl[blId] ?? []).forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.label;
                containersSelect.appendChild(opt);
            });
        }

        function onCarChange() {
            const carId = document.getElementById('carSelect').value;
            const car = cars.find(c => String(c.id) === String(carId));
            const helper = document.getElementById('carHelper');
            helper.textContent = car ? `Transporteur : ${car.car_owner?.name ?? '-'} — Chauffeur : ${car.car_driver?.name ?? '-'}` : '';
        }

        $(function () {
            $('#containersSelect').select2 ? null : null;
            if (document.getElementById('blSelect').value) onBlChange();
            if (document.getElementById('carSelect').value) onCarChange();
        });
    </script>
@endpush
