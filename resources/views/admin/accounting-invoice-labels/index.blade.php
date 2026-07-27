@extends('layouts.admin')

@section('title', 'Libellés de facturation clients')

@section('content')
    <p>
        <a href="{{ route('admin.accounting-invoices.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#labelModal" onclick="openLabelModal()">
            <i class="fa fa-plus"></i> Nouveau
        </button>
    </p>

    <div class="card">
        <div class="card-header"><h5>Libellés de facturation (factures clients)</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="labelTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">D&Eacute;SIGNATION</th>
                            <th class="text-center align-middle">PRIX UNITAIRE</th>
                            <th class="text-center align-middle">DESCRIPTION</th>
                            <th class="text-center align-middle">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="labelModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="labelForm" method="POST" action="{{ route('admin.accounting-invoice-labels.store') }}">
                @csrf
                <div id="labelMethodField"></div>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Libellé</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Désignation *</label>
                            <input type="text" name="name" id="labelName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Prix unitaire *</label>
                            <input type="number" step="0.01" min="0" name="unit_price" id="labelUnitPrice" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" id="labelDescription" class="form-control"></textarea>
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
        function openLabelModal(label) {
            const form = document.getElementById('labelForm');
            const methodField = document.getElementById('labelMethodField');
            methodField.innerHTML = '';

            if (label) {
                form.action = `/admin/accounting-invoice-labels/${label.id}`;
                methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
                document.getElementById('labelName').value = label.name ?? '';
                document.getElementById('labelUnitPrice').value = label.unit_price ?? 0;
                document.getElementById('labelDescription').value = label.description ?? '';
            } else {
                form.action = '{{ route('admin.accounting-invoice-labels.store') }}';
                form.reset();
            }
        }

        $(function () {
            $('#labelTable').DataTable({
                ajax: '{{ route('admin.accounting-invoice-labels.data') }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: [
                    {data: 'name'},
                    {data: (d) => Number(d.unit_price ?? 0).toFixed(2)},
                    {data: (d) => d.description ?? '-'},
                    {data: (d) => `
                        <button class="btn btn-sm btn-primary" title="Modifier" onclick='openLabelModal(${JSON.stringify(d)})' data-toggle="modal" data-target="#labelModal"><i class="fa fa-edit"></i></button>
                        <form method="POST" action="/admin/accounting-invoice-labels/${d.id}" class="d-inline" onsubmit="return confirm('Archiver ce libellé ?')">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button class="btn btn-sm btn-danger" title="Archiver"><i class="fa fa-trash"></i></button>
                        </form>
                    `},
                ],
            });
        });
    </script>
@endpush
