@extends('layouts.admin')

@section('title', 'Utilisateurs')

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <a href="{{ route('admin.users.create') }}" class="btn btn-outline-primary"><i class="fa fa-plus"></i> Nouvel utilisateur</a>
    </p>

    <div class="card">
        <div class="card-header"><h5>Liste des utilisateurs</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="userTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">NOM COMPLET</th>
                            <th class="text-center align-middle">EMAIL</th>
                            <th class="text-center align-middle">GROUPES</th>
                            <th class="text-center align-middle">BLOQU&Eacute;</th>
                            <th class="text-center align-middle">DERNI&Egrave;RE CONNEXION</th>
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
            $('#userTable').DataTable({
                ajax: '{{ route('admin.users.data') }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: [
                    {data: (d) => `<a href="/admin/users/${d.id}/edit">${d.fullname}</a>`},
                    {data: (d) => d.email ?? '-'},
                    {data: (d) => d.groups || '-'},
                    {data: (d) => d.banned ? '<span class="badge badge-danger">Oui</span>' : '<span class="badge badge-success">Non</span>'},
                    {data: (d) => d.last_login ?? 'Jamais'},
                    {data: (d) => `
                        <a href="/admin/users/${d.id}/edit" class="btn btn-sm btn-primary" title="Modifier"><i class="fa fa-edit"></i></a>
                        <form method="POST" action="/admin/users/${d.id}" class="d-inline" onsubmit="return confirm('Supprimer cet utilisateur ?')">
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
