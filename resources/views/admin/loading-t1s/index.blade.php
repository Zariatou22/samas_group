@extends('layouts.admin')

@section('title', 'T1')

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <a href="{{ route('admin.loading-t1s.create') }}" class="btn btn-outline-primary"><i class="fa fa-plus"></i> Nouveau T1</a>
    </p>

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'ongoing' ? 'active' : '' }}" href="{{ route('admin.loading-t1s.index', ['activeTab' => 'ongoing']) }}">En cours de validité</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'expired' ? 'active' : '' }}" href="{{ route('admin.loading-t1s.index', ['activeTab' => 'expired']) }}">Expirés</a>
        </li>
    </ul>

    <div class="card">
        <div class="card-header"><h5>Liste des T1</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="t1Table">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">N&deg; T1</th>
                            <th class="text-center align-middle">N&deg; B/L</th>
                            <th class="text-center align-middle">MANDATAIRE</th>
                            <th class="text-center align-middle">V&Eacute;HICULE</th>
                            <th class="text-center align-middle">VALIDE JUSQU'AU</th>
                            <th class="text-center align-middle">VALID&Eacute;</th>
                            <th class="text-center align-middle">VALID&Eacute; LE</th>
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
            $('#t1Table').DataTable({
                ajax: '{{ route('admin.loading-t1s.data', ['activeTab' => $activeTab]) }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: [
                    {data: (d) => `<a href="/admin/loading-t1s/${d.id}/edit">${d.t1_number}</a>`},
                    {data: (d) => d.bl ?? '-'},
                    {data: (d) => d.customer_name ?? '-'},
                    {data: (d) => d.vehicle ?? '-'},
                    {data: (d) => d.valid_until ?? '-'},
                    {data: (d) => d.is_validated ? '<span class="badge badge-success">Oui</span>' : '<span class="badge badge-secondary">Non</span>'},
                    {data: (d) => d.validate ?? '—'},
                    {data: (d) => `
                        ${d.is_validated ? '' : `
                        <form method="POST" action="/admin/loading-t1s/${d.id}/validate" class="d-inline" onsubmit="return confirm('Valider ce T1 ?')">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <button class="btn btn-sm btn-success" title="Valider"><i class="fa fa-check"></i></button>
                        </form>`}
                        <a href="/admin/loading-t1s/${d.id}/edit" class="btn btn-sm btn-primary" title="Modifier"><i class="fa fa-edit"></i></a>
                        <form method="POST" action="/admin/loading-t1s/${d.id}" class="d-inline" onsubmit="return confirm('Archiver ce T1 ?')">
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
