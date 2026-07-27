@extends('layouts.admin')

@section('title', 'Rôles et permissions')

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#permModal" onclick="openPermModal()">
            <i class="fa fa-plus"></i> Nouvelle permission
        </button>
    </p>

    <div class="card">
        <div class="card-header"><h5>Liste des permissions</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="permTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">NOM</th>
                            <th class="text-center align-middle">DESCRIPTION</th>
                            <th class="text-center align-middle">GROUPES</th>
                            <th class="text-center align-middle">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="permModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="permForm" method="POST" action="{{ route('admin.perms.store') }}">
                @csrf
                <div id="permMethodField"></div>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Permission</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nom *</label>
                            <input type="text" name="name" id="permName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="definition" id="permDefinition" class="form-control"></textarea>
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
        function openPermModal(perm) {
            const form = document.getElementById('permForm');
            const methodField = document.getElementById('permMethodField');
            methodField.innerHTML = '';

            if (perm) {
                form.action = `/admin/perms/${perm.id}`;
                methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
                document.getElementById('permName').value = perm.name ?? '';
                document.getElementById('permDefinition').value = perm.definition ?? '';
            } else {
                form.action = '{{ route('admin.perms.store') }}';
                form.reset();
            }
        }

        $(function () {
            $('#permTable').DataTable({
                ajax: '{{ route('admin.perms.data') }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: [
                    {data: 'name'},
                    {data: (d) => d.definition ?? '-'},
                    {data: (d) => `<span class="badge badge-secondary">${d.groups_count}</span>`},
                    {data: (d) => `
                        <button class="btn btn-sm btn-primary" title="Modifier" onclick='openPermModal(${JSON.stringify(d)})' data-toggle="modal" data-target="#permModal"><i class="fa fa-edit"></i></button>
                        <form method="POST" action="/admin/perms/${d.id}" class="d-inline" onsubmit="return confirm('Supprimer cette permission ?')">
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
