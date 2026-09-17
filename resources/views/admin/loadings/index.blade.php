@extends('layouts.admin')

@section('title', $title)

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <a href="{{ route($isUnloading ? 'admin.loadings.unloadings.create' : 'admin.loadings.create') }}" class="btn btn-outline-primary"><i class="fa fa-plus"></i> {{ $isUnloading ? 'Dépoter' : 'Charger' }}</a>
    </p>

    <div class="card">
        <div class="card-header"><h5>{{ $isUnloading ? 'Liste des dépotages' : 'Liste des chargements' }}</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="loadingTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">N&deg; B/L</th>
                            <th class="text-center align-middle">D&Eacute;CLARATION</th>
                            <th class="text-center align-middle">MANDATAIRE</th>
                            <th class="text-center align-middle">V&Eacute;HICULE</th>
                            <th class="text-center align-middle">CHAUFFEUR</th>
                            <th class="text-center align-middle">COLIS</th>
                            <th class="text-center align-middle">POIDS</th>
                            <th class="text-center align-middle">T1</th>
                            <th class="text-center align-middle">DATE</th>
                            <th class="text-center align-middle">VALIDIT&Eacute; BAD</th>
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
            $('#loadingTable').DataTable({
                ajax: '{{ route($dataRoute, ['activeTab' => $activeTab]) }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: [
                    {data: (d) => `<a href="/admin/loadings/${d.id}/edit">${d.bl ?? '-'}</a>`},
                    {data: (d) => d.auth_number ?? '-'},
                    {data: (d) => d.customer_name ?? '-'},
                    {data: (d) => d.vehicle ?? '-'},
                    {data: (d) => d.driver_name ?? '-'},
                    {data: (d) => d.nb_package ?? 0},
                    {data: (d) => d.quantity ?? 0},
                    {data: (d) => d.has_t1 ? '<span class="badge badge-success">Oui</span>' : '<span class="badge badge-secondary">Non</span>'},
                    {data: (d) => d.loading_date ?? '-'},
                    {data: (d) => d.bad_valid_date ?? '-'},
                    {data: (d) => `
                        <a href="/admin/loadings/${d.id}/edit" class="btn btn-sm btn-primary" title="Modifier"><i class="fa fa-edit"></i></a>
                        ${d.has_t1 ? '' : `
                        <form method="POST" action="/admin/loadings/${d.id}" class="d-inline" onsubmit="return confirm('Archiver cette opération ?')">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button class="btn btn-sm btn-danger" title="Archiver"><i class="fa fa-trash"></i></button>
                        </form>`}
                    `},
                ],
            });
        });
    </script>
@endpush
