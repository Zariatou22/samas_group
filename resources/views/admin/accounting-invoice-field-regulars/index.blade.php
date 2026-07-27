@extends('layouts.admin')

@section('title', 'Champs récurrents de factures clients')

@section('content')
    <p>
        <a href="{{ route('admin.accounting-invoices.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#fieldModal" onclick="openFieldModal()">
            <i class="fa fa-plus"></i> Nouveau
        </button>
    </p>

    <div class="card">
        <div class="card-header"><h5>Champs récurrents (repris par défaut sur chaque nouvelle facture client)</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="fieldTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">LIBELL&Eacute;</th>
                            <th class="text-center align-middle">PRIX UNITAIRE</th>
                            <th class="text-center align-middle">QUANTIT&Eacute;</th>
                            <th class="text-center align-middle">MONTANT</th>
                            <th class="text-center align-middle">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="fieldModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="fieldForm" method="POST" action="{{ route('admin.accounting-invoice-field-regulars.store') }}">
                @csrf
                <div id="fieldMethodField"></div>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Champ récurrent</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé</label>
                            <select name="label" id="fieldLabel" class="form-control" onchange="onLabelChange()">
                                <option value="">Choisir...</option>
                                @foreach ($labels as $label)
                                    <option value="{{ $label->id }}" data-unit-price="{{ $label->unit_price }}">{{ $label->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Prix unitaire *</label>
                            <input type="number" step="0.01" min="0" name="unit_price" id="fieldUnitPrice" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Quantité *</label>
                            <input type="number" step="1" min="1" name="quantity" id="fieldQuantity" value="1" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function onLabelChange() {
            const select = document.getElementById('fieldLabel');
            const option = select.options[select.selectedIndex];
            const unitPrice = option?.dataset?.unitPrice;
            if (unitPrice !== undefined) {
                document.getElementById('fieldUnitPrice').value = unitPrice;
            }
        }

        function openFieldModal(field) {
            const form = document.getElementById('fieldForm');
            const methodField = document.getElementById('fieldMethodField');
            methodField.innerHTML = '';

            if (field) {
                form.action = `/admin/accounting-invoice-field-regulars/${field.id}`;
                methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
                document.getElementById('fieldLabel').value = field.label ?? '';
                document.getElementById('fieldUnitPrice').value = field.unit_price ?? 0;
                document.getElementById('fieldQuantity').value = field.quantity ?? 1;
            } else {
                form.action = '{{ route('admin.accounting-invoice-field-regulars.store') }}';
                form.reset();
            }
        }

        $(function () {
            $('#fieldTable').DataTable({
                ajax: '{{ route('admin.accounting-invoice-field-regulars.data') }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: [
                    {data: (d) => d.label_name ?? '-'},
                    {data: (d) => Number(d.unit_price ?? 0).toFixed(2)},
                    {data: (d) => d.quantity ?? 0},
                    {data: (d) => Number(d.amount ?? 0).toFixed(2)},
                    {data: (d) => `
                        <button class="btn btn-sm btn-primary" title="Modifier" onclick='openFieldModal(${JSON.stringify(d)})' data-toggle="modal" data-target="#fieldModal"><i class="fa fa-edit"></i></button>
                        <form method="POST" action="/admin/accounting-invoice-field-regulars/${d.id}" class="d-inline" onsubmit="return confirm('Supprimer ce champ ?')">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button class="btn btn-sm btn-danger" title="Supprimer"><i class="fa fa-trash"></i></button>
                        </form>
                    `},
                ],
            });
        });
    </script>
@endpush
