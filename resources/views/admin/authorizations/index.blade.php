@extends('layouts.admin')

@section('title', 'Déclarations')

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <a href="{{ route('admin.authorizations.create') }}" class="btn btn-outline-primary"><i class="fa fa-plus"></i> Nouvelle déclaration</a>
    </p>

    <div class="card">
        <div class="card-header"><h5>Liste des déclarations</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="authTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">N&deg; D&Eacute;CLARATION</th>
                            <th class="text-center align-middle">N&deg; B/L</th>
                            <th class="text-center align-middle">MANDATAIRE</th>
                            <th class="text-center align-middle">CLIENT</th>
                            <th class="text-center align-middle">LIEU</th>
                            <th class="text-center align-middle">CONTENEURS</th>
                            <th class="text-center align-middle">COLIS</th>
                            <th class="text-center align-middle">POIDS</th>
                            <th class="text-center align-middle">SOLD&Eacute;E</th>
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
            $('#authTable').DataTable({
                ajax: '{{ route('admin.authorizations.data', ['activeTab' => $activeTab]) }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: [
                    {data: (d) => `<a href="/admin/authorizations/${d.id}/edit">${d.auth_number}</a>`},
                    {data: (d) => d.bl ?? '-'},
                    {data: (d) => d.customer_name ?? '-'},
                    {data: (d) => d.customer_company_name ?? '-'},
                    {data: (d) => d.source_name ?? '-'},
                    {data: (d) => d.nb_container ?? 0},
                    {data: (d) => d.nb_package ?? 0},
                    {data: (d) => d.quantity ?? 0},
                    {data: (d) => d.is_settled ? '<span class="badge badge-success">Oui</span>' : '<span class="badge badge-secondary">Non</span>'},
                    {data: (d) => `
                        <a href="/admin/authorizations/${d.id}/edit" class="btn btn-sm btn-primary" title="Modifier"><i class="fa fa-edit"></i></a>
                        <form method="POST" action="/admin/authorizations/${d.id}" class="d-inline" onsubmit="return confirm('Archiver cette déclaration ?')">
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
