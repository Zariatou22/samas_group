@extends('layouts.admin')

@section('title', "Lieux d'enlèvement")

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#sourceModal" onclick="openSourceModal()">
            <i class="fa fa-plus"></i> Nouveau
        </button>
    </p>

    <div class="card">
        <div class="card-header"><h5>Lieux d'enlèvement</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="sourceTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">NOM</th>
                            <th class="text-center align-middle">CODE</th>
                            <th class="text-center align-middle">DESCRIPTION</th>
                            <th class="text-center align-middle">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="sourceModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="sourceForm" method="POST" action="{{ route('admin.sources.store') }}">
                @csrf
                <div id="sourceMethodField"></div>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Lieu d'enlèvement</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nom *</label>
                            <input type="text" name="name" id="sourceName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Code</label>
                            <input type="text" name="code" id="sourceCode" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" id="sourceDescription" class="form-control"></textarea>
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
        function openSourceModal(source) {
            const form = document.getElementById('sourceForm');
            const methodField = document.getElementById('sourceMethodField');
            methodField.innerHTML = '';

            if (source) {
                form.action = `/admin/sources/${source.id}`;
                methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
                document.getElementById('sourceName').value = source.name ?? '';
                document.getElementById('sourceCode').value = source.code ?? '';
                document.getElementById('sourceDescription').value = source.description ?? '';
            } else {
                form.action = '{{ route('admin.sources.store') }}';
                form.reset();
            }
        }

        $(function () {
            $('#sourceTable').DataTable({
                ajax: '{{ route('admin.sources.data') }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: [
                    {data: 'name'},
                    {data: (d) => d.code ?? '-'},
                    {data: (d) => d.description ?? '-'},
                    {data: (d) => `
                        <button class="btn btn-sm btn-primary" title="Modifier" onclick='openSourceModal(${JSON.stringify(d)})' data-toggle="modal" data-target="#sourceModal"><i class="fa fa-edit"></i></button>
                        <form method="POST" action="/admin/sources/${d.id}" class="d-inline" onsubmit="return confirm('Archiver ce lieu ?')">
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
