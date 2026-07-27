@extends('layouts.admin')

@section('title', 'Groupes')

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <a href="{{ route('admin.groups.create') }}" class="btn btn-outline-primary"><i class="fa fa-plus"></i> Nouveau groupe</a>
    </p>

    <div class="card">
        <div class="card-header"><h5>Liste des groupes</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="groupTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">NOM</th>
                            <th class="text-center align-middle">DESCRIPTION</th>
                            <th class="text-center align-middle">PERMISSIONS</th>
                            <th class="text-center align-middle">MEMBRES</th>
                            <th class="text-center align-middle">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            $('#groupTable').DataTable({
                ajax: '{{ route('admin.groups.data') }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: [
                    {data: (d) => `<a href="/admin/groups/${d.id}/edit">${d.name}</a>`},
                    {data: (d) => d.definition ?? '-'},
                    {data: (d) => d.perms || '-'},
                    {data: (d) => `<span class="badge badge-secondary">${d.users_count}</span>`},
                    {data: (d) => `
                        <a href="/admin/groups/${d.id}/edit" class="btn btn-sm btn-primary" title="Modifier"><i class="fa fa-edit"></i></a>
                        <form method="POST" action="/admin/groups/${d.id}" class="d-inline" onsubmit="return confirm('Supprimer ce groupe ?')">
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
