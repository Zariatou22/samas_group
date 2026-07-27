@extends('layouts.admin')

@section('title', 'Compagnies de shipping')

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#companyModal" onclick="openCompanyModal()">
            <i class="fa fa-plus"></i> Nouveau
        </button>
    </p>

    <div class="card">
        <div class="card-header"><h5>Compagnies de shipping</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="companyTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">NOM</th>
                            <th class="text-center align-middle">ADRESSE</th>
                            <th class="text-center align-middle">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="companyModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="companyForm" method="POST" action="{{ route('admin.companies.store') }}">
                @csrf
                <div id="companyMethodField"></div>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Compagnie de shipping</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nom *</label>
                            <input type="text" name="name" id="companyName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Adresse</label>
                            <textarea name="address" id="companyAddress" class="form-control"></textarea>
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
        function openCompanyModal(company) {
            const form = document.getElementById('companyForm');
            const methodField = document.getElementById('companyMethodField');
            methodField.innerHTML = '';

            if (company) {
                form.action = `/admin/companies/${company.id}`;
                methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
                document.getElementById('companyName').value = company.name ?? '';
                document.getElementById('companyAddress').value = company.address ?? '';
            } else {
                form.action = '{{ route('admin.companies.store') }}';
                form.reset();
            }
        }

        $(function () {
            $('#companyTable').DataTable({
                ajax: '{{ route('admin.companies.data') }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: [
                    {data: 'name'},
                    {data: (d) => d.address ?? '-'},
                    {data: (d) => `
                        <button class="btn btn-sm btn-primary" title="Modifier" onclick='openCompanyModal(${JSON.stringify(d)})' data-toggle="modal" data-target="#companyModal"><i class="fa fa-edit"></i></button>
                        <form method="POST" action="/admin/companies/${d.id}" class="d-inline" onsubmit="return confirm('Archiver cette compagnie ?')">
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
