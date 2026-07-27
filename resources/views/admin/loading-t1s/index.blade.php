@extends('layouts.admin')

@section('title', 'T1')

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <a href="{{ route('admin.loading-t1s.create') }}" class="btn btn-outline-primary"><i class="fa fa-plus"></i> Nouveau T1</a>
    </p>

    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card text-center"><div class="card-block"><h4>{{ $counts['total'] }}</h4><small>Total T1</small></div></div>
        </div>
        <div class="col-md-3">
            <div class="card text-center"><div class="card-block"><h4>{{ $counts['ongoing'] }}</h4><small>En cours de validité</small></div></div>
        </div>
        <div class="col-md-3">
            <div class="card text-center"><div class="card-block"><h4>{{ $counts['expired'] }}</h4><small>Expirés</small></div></div>
        </div>
        <div class="col-md-3">
            <div class="card text-center"><div class="card-block"><h4>{{ $counts['waiting'] }}</h4><small>Chargements en attente de T1</small></div></div>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'ongoing' ? 'active' : '' }}" href="{{ route('admin.loading-t1s.index', ['activeTab' => 'ongoing']) }}">En cours de validité</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'expired' ? 'active' : '' }}" href="{{ route('admin.loading-t1s.index', ['activeTab' => 'expired']) }}">Expirés</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'waiting' ? 'active' : '' }}" href="{{ route('admin.loading-t1s.index', ['activeTab' => 'waiting']) }}">En attente de T1</a>
        </li>
    </ul>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Liste des T1</h5>
            @if ($activeTab !== 'waiting')
                <button type="button" class="btn btn-sm btn-success" id="bulkValidateBtn" disabled data-toggle="modal" data-target="#bulkValidateModal">
                    <i class="fa fa-check-double"></i> Valider la sélection
                </button>
            @endif
        </div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="t1Table">
                    <thead class="thead-dark">
                        <tr>
                            @if ($activeTab !== 'waiting')
                                <th class="text-center align-middle"><input type="checkbox" id="selectAllT1"></th>
                            @endif
                            <th class="text-center align-middle">N&deg; T1</th>
                            <th class="text-center align-middle">N&deg; B/L</th>
                            <th class="text-center align-middle">MANDATAIRE</th>
                            <th class="text-center align-middle">V&Eacute;HICULE</th>
                            @if ($activeTab !== 'waiting')
                                <th class="text-center align-middle">VALIDE JUSQU'AU</th>
                                <th class="text-center align-middle">VALID&Eacute;</th>
                                <th class="text-center align-middle">VALID&Eacute; LE</th>
                            @endif
                            <th class="text-center align-middle">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bulkValidateModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.loading-t1s.validate-bulk') }}" id="bulkValidateForm">
                @csrf
                <div id="bulkValidateIds"></div>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Valider les T1 sélectionnés</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Date de validation *</label>
                            <input type="date" name="validate_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Valider</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            const activeTab = @json($activeTab);

            const columns = activeTab === 'waiting' ? [
                {data: (d) => `<a href="/admin/loading-t1s/create">${d.bl ?? '-'}</a>`},
                {data: (d) => d.customer_name ?? '-'},
                {data: (d) => d.vehicle ?? '-'},
                {data: (d) => `<a href="/admin/loading-t1s/create" class="btn btn-sm btn-outline-primary" title="Créer le T1"><i class="fa fa-plus"></i></a>`},
            ] : [
                {data: (d) => `<input type="checkbox" class="t1-checkbox" value="${d.id}" ${d.is_validated ? 'disabled' : ''}>`},
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
            ];

            const dt = $('#t1Table').DataTable({
                ajax: '{{ route('admin.loading-t1s.data', ['activeTab' => $activeTab]) }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: columns,
                drawCallback: function () {
                    updateBulkValidateState();
                },
            });

            function updateBulkValidateState() {
                const checked = $('.t1-checkbox:checked').length;
                $('#bulkValidateBtn').prop('disabled', checked === 0);
            }

            $(document).on('change', '.t1-checkbox', updateBulkValidateState);

            $('#selectAllT1').on('change', function () {
                $('.t1-checkbox:not(:disabled)').prop('checked', this.checked);
                updateBulkValidateState();
            });

            $('#bulkValidateForm').on('submit', function () {
                const ids = $('.t1-checkbox:checked').map(function () { return this.value; }).get();
                $('#bulkValidateIds').empty();
                ids.forEach((id) => {
                    $('#bulkValidateIds').append(`<input type="hidden" name="ids[]" value="${id}">`);
                });
            });
        });
    </script>
@endpush
