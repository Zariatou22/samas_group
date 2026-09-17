@extends('layouts.admin')

@section('title', 'Nouvelle facture client')

@section('content')
    <p>
        <a href="{{ route('admin.accounting-invoices.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <form method="POST" action="{{ route('admin.accounting-invoices.store') }}" id="invoiceForm">
        @csrf
        <div class="card">
            <div class="card-header"><h5>Détails de la facture</h5></div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Référence *</label>
                        <input type="text" name="reference" class="form-control" value="{{ old('reference', $nextReference) }}" required maxlength="100">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Client *</label>
                        <select name="customer" id="customerSelect" class="form-control" required onchange="onCustomerChange()">
                            <option value="">Choisir...</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}" @selected(old('customer') == $c->id)>{{ $c->customer_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Date *</label>
                        <input type="datetime-local" name="date_issued" class="form-control" value="{{ old('date_issued', now()->format('Y-m-d\TH:i')) }}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Réduction</label>
                        <input type="number" step="0.01" min="0" name="fees" id="feesInput" class="form-control" value="{{ old('fees', 0) }}" oninput="recalculateTotal()">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>TVA</label>
                        <input type="number" step="0.01" min="0" name="vat" id="vatInput" class="form-control" value="{{ old('vat', 0) }}" oninput="recalculateTotal()">
                    </div>
                    <div class="col-md-2 form-group">
                        <label>Montant</label>
                        <input type="text" id="amountDisplay" class="form-control" value="0.00" disabled>
                    </div>
                    <div class="col-md-2 form-group">
                        <label>Montant TTC</label>
                        <input type="text" id="amountTtcDisplay" class="form-control" value="0.00" disabled>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Champs de la facture</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addFieldRow()"><i class="fa fa-plus"></i> Ajouter une ligne</button>
            </div>
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="fieldsTable">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width:160px">BL</th>
                                <th>LIBELLÉ</th>
                                <th style="width:150px">PRIX UNITAIRE</th>
                                <th style="width:120px">QUANTITÉ</th>
                                <th style="width:150px">MONTANT</th>
                                <th style="width:60px"></th>
                            </tr>
                        </thead>
                        <tbody id="fieldsBody"></tbody>
                    </table>
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
        const availableLabels = @json($labels);
        const regularFields = @json($regularFields);
        const availableBls = @json($bls);
        let fieldIndex = 0;

        function labelOptions(selected) {
            let html = '<option value="">Choisir...</option>';
            availableLabels.forEach(l => {
                html += `<option value="${l.id}" data-unit-price="${l.unit_price}" ${String(selected) === String(l.id) ? 'selected' : ''}>${l.name}</option>`;
            });
            return html;
        }

        function blOptions(selected) {
            const customerId = document.getElementById('customerSelect').value;
            let html = '<option value="">Choisir...</option>';
            availableBls
                .filter(b => !customerId || String(b.customer) === String(customerId))
                .forEach(b => {
                    html += `<option value="${b.id}" ${String(selected) === String(b.id) ? 'selected' : ''}>${b.bl}</option>`;
                });
            return html;
        }

        function onCustomerChange() {
            document.querySelectorAll('#fieldsBody tr').forEach(row => {
                const select = row.querySelector('select[name$="[bl]"]');
                if (select) {
                    const selected = select.value;
                    select.innerHTML = blOptions(selected);
                }
            });
        }

        function addFieldRow(field) {
            const i = fieldIndex++;
            const row = document.createElement('tr');
            row.id = `fieldRow${i}`;
            row.innerHTML = `
                <td>
                    <select name="fields[${i}][bl]" class="form-control">
                        ${blOptions(field?.bl)}
                    </select>
                </td>
                <td>
                    <select name="fields[${i}][label]" class="form-control" onchange="onFieldLabelChange(${i})">
                        ${labelOptions(field?.label)}
                    </select>
                </td>
                <td><input type="number" step="0.01" min="0" name="fields[${i}][unit_price]" id="fieldUnitPrice${i}" class="form-control" value="${field?.unit_price ?? 0}" required oninput="onFieldRowChange(${i})"></td>
                <td><input type="number" step="1" min="1" name="fields[${i}][quantity]" id="fieldQuantity${i}" class="form-control" value="${field?.quantity ?? 1}" required oninput="onFieldRowChange(${i})"></td>
                <td><input type="text" id="fieldAmount${i}" class="form-control" value="${Number((field?.unit_price ?? 0) * (field?.quantity ?? 1)).toFixed(2)}" disabled></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeFieldRow(${i})"><i class="fa fa-trash"></i></button></td>
            `;
            document.getElementById('fieldsBody').appendChild(row);
        }

        function removeFieldRow(i) {
            document.getElementById(`fieldRow${i}`)?.remove();
            recalculateTotal();
        }

        function onFieldLabelChange(i) {
            const select = document.querySelector(`select[name="fields[${i}][label]"]`);
            const option = select.options[select.selectedIndex];
            const unitPrice = option?.dataset?.unitPrice;
            if (unitPrice !== undefined) {
                document.getElementById(`fieldUnitPrice${i}`).value = unitPrice;
            }
            onFieldRowChange(i);
        }

        function onFieldRowChange(i) {
            const unitPrice = parseFloat(document.getElementById(`fieldUnitPrice${i}`).value) || 0;
            const quantity = parseFloat(document.getElementById(`fieldQuantity${i}`).value) || 0;
            document.getElementById(`fieldAmount${i}`).value = (unitPrice * quantity).toFixed(2);
            recalculateTotal();
        }

        function recalculateTotal() {
            let total = 0;
            document.querySelectorAll('#fieldsBody tr').forEach(row => {
                const amountInput = row.querySelector('input[id^="fieldAmount"]');
                total += parseFloat(amountInput?.value) || 0;
            });
            const fees = parseFloat(document.getElementById('feesInput').value) || 0;
            const vat = parseFloat(document.getElementById('vatInput').value) || 0;
            document.getElementById('amountDisplay').value = total.toFixed(2);
            document.getElementById('amountTtcDisplay').value = (total - fees + vat).toFixed(2);
        }

        $(function () {
            regularFields.forEach(f => addFieldRow(f));
            recalculateTotal();
        });
    </script>
@endpush
