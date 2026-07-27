@extends('layouts.admin')

@section('title', "Type d'emballage")

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#productTypeModal" onclick="openProductTypeModal()">
            <i class="fa fa-plus"></i> Nouveau
        </button>
    </p>

    <div class="card">
        <div class="card-header"><h5>Types de produit</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="productTypeTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">NOM</th>
                            <th class="text-center align-middle">DESCRIPTION</th>
                            <th class="text-center align-middle">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="productTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="productTypeForm" method="POST" action="{{ route('admin.product-types.store') }}">
                @csrf
                <div id="productTypeMethodField"></div>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Type de produit</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nom *</label>
                            <input type="text" name="name" id="productTypeName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" id="productTypeDescription" class="form-control"></textarea>
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
        function openProductTypeModal(pt) {
            const form = document.getElementById('productTypeForm');
            const methodField = document.getElementById('productTypeMethodField');
            methodField.innerHTML = '';

            if (pt) {
                form.action = `/admin/product-types/${pt.id}`;
                methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
                document.getElementById('productTypeName').value = pt.name ?? '';
                document.getElementById('productTypeDescription').value = pt.description ?? '';
            } else {
                form.action = '{{ route('admin.product-types.store') }}';
                form.reset();
            }
        }

        $(function () {
            $('#productTypeTable').DataTable({
                ajax: '{{ route('admin.product-types.data') }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: [
                    {data: 'name'},
                    {data: (d) => d.description ?? '-'},
                    {data: (d) => `
                        <button class="btn btn-sm btn-primary" title="Modifier" onclick='openProductTypeModal(${JSON.stringify(d)})' data-toggle="modal" data-target="#productTypeModal"><i class="fa fa-edit"></i></button>
                        <form method="POST" action="/admin/product-types/${d.id}" class="d-inline" onsubmit="return confirm('Archiver ce type ?')">
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
