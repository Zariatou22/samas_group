@extends('layouts.admin')

@section('title', $receipt->exists ? 'Modifier le reçu '.$receipt->reference : 'Nouveau reçu d\'avance')

@section('content')
    <p>
        <a href="{{ route('admin.invoice-advances.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        @if ($receipt->exists)
            <a href="{{ route('admin.invoice-advances.print', $receipt) }}" class="btn btn-outline-secondary" target="_blank"><i class="fa fa-print"></i> Imprimer</a>
        @endif
    </p>

    <form method="POST" action="{{ $receipt->exists ? route('admin.invoice-advances.update', $receipt) : route('admin.invoice-advances.store') }}">
        @csrf
        @if ($receipt->exists)
            @method('PUT')
        @endif

        <div class="card">
            <div class="card-header"><h5>Reçu d'avance</h5></div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label>Date *</label>
                        <input type="date" name="date_issued" class="form-control" value="{{ old('date_issued', optional($receipt->date_issued)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-5 form-group">
                        <label>Nom et contacts du chauffeur</label>
                        <select name="driver" id="driverSelect" class="form-control">
                            <option value="">Choisir...</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}" @selected(old('driver', $receipt->driver) == $driver->id)>{{ $driver->name }}{{ $driver->contact ? ' — '.$driver->contact : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>N&deg; du camion</label>
                        <input type="text" id="carDisplay" class="form-control" value="{{ $receipt->vehicle?->full_registration }}" disabled>
                        <input type="hidden" name="car" id="carInput" value="{{ old('car', $receipt->car) }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>N&deg;BL/TC</label>
                        <input type="text" id="blDisplay" class="form-control" value="{{ $receipt->parentBl?->bl }}" disabled>
                        <input type="hidden" name="bl" id="blInput" value="{{ old('bl', $receipt->bl) }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Contact client</label>
                        <input type="text" name="contact_client" class="form-control" value="{{ old('contact_client', $receipt->contact_client) }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Contact Transitaire Cincassé</label>
                        <input type="text" name="contact_transitaire" class="form-control" value="{{ old('contact_transitaire', $receipt->contact_transitaire) }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Destination</label>
                        <input type="text" name="destination" class="form-control" value="{{ old('destination', $receipt->destination) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Lignes</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addLineRow()"><i class="fa fa-plus"></i> Ajouter une ligne</button>
            </div>
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>DESIGNATION</th>
                                <th style="width:180px">PRIX TOTAL</th>
                                <th style="width:60px"></th>
                            </tr>
                        </thead>
                        <tbody id="linesBody"></tbody>
                        <tfoot>
                            <tr>
                                <th class="text-right">Total</th>
                                <th><input type="text" id="totalDisplay" class="form-control" disabled></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5>Paiement</h5></div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label>Avance reçu</label>
                        <input type="number" step="0.01" name="avance_recu" class="form-control" value="{{ old('avance_recu', $receipt->avance_recu) }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Reste à payer</label>
                        <input type="number" step="0.01" name="reste_a_payer" class="form-control" value="{{ old('reste_a_payer', $receipt->reste_a_payer) }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Arrêté le présent reçu à la somme de</label>
                        <input type="text" name="arrete_somme" class="form-control" value="{{ old('arrete_somme', $receipt->arrete_somme) }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Reste à payer à destination</label>
                        <input type="text" name="reste_a_payer_destination" class="form-control" value="{{ old('reste_a_payer_destination', $receipt->reste_a_payer_destination) }}">
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
        const existingLines = @json($lines->map(fn ($l) => ['designation' => $l->designation, 'amount' => $l->amount]));
        let lineIndex = 0;

        function addLineRow(line) {
            const i = lineIndex++;
            const row = document.createElement('tr');
            row.id = `lineRow${i}`;
            row.innerHTML = `
                <td><input type="text" name="lines[${i}][designation]" class="form-control" value="${line?.designation ?? ''}" required></td>
                <td><input type="number" step="0.01" min="0" name="lines[${i}][amount]" id="lineAmount${i}" class="form-control" value="${line?.amount ?? 0}" required oninput="recalculateTotal()"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeLineRow(${i})"><i class="fa fa-trash"></i></button></td>
            `;
            document.getElementById('linesBody').appendChild(row);
        }

        function removeLineRow(i) {
            document.getElementById(`lineRow${i}`)?.remove();
            recalculateTotal();
        }

        function recalculateTotal() {
            let total = 0;
            document.querySelectorAll('#linesBody input[id^="lineAmount"]').forEach((input) => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById('totalDisplay').value = total.toFixed(2);
        }

        function onDriverChange() {
            const driverId = document.getElementById('driverSelect').value;
            if (!driverId) {
                return;
            }
            fetch(`/admin/invoice-advances/driver-info/${driverId}`)
                .then((r) => r.json())
                .then((data) => {
                    document.getElementById('carDisplay').value = data.car?.full_registration ?? '';
                    document.getElementById('carInput').value = data.car?.id ?? '';
                    document.getElementById('blDisplay').value = data.bl?.bl ?? '';
                    document.getElementById('blInput').value = data.bl?.id ?? '';
                });
        }

        $(function () {
            document.getElementById('driverSelect').addEventListener('change', onDriverChange);

            if (existingLines.length > 0) {
                existingLines.forEach((l) => addLineRow(l));
            } else {
                addLineRow();
            }
            recalculateTotal();
        });
    </script>
@endpush
