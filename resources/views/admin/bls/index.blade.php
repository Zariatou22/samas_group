@extends('layouts.admin')

@section('title', 'Bons de livraison')

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <a href="{{ route('admin.bls.create') }}" class="btn btn-outline-primary"><i class="fa fa-plus"></i> Nouvel arrivage</a>
    </p>

    <div class="card">
        <div class="card-header">
            <h5>Bons de livraison</h5>
        </div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="blTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">N&deg; B/L</th>
                            <th class="text-center align-middle">MANDATAIRE</th>
                            <th class="text-center align-middle">CLIENT</th>
                            <th class="text-center align-middle">COMPAGNIE</th>
                            <th class="text-center align-middle">TYPE</th>
                            <th class="text-center align-middle">CONTENEURS</th>
                            <th class="text-center align-middle">ETA</th>
                            <th class="text-center align-middle">DATE RÉCEPTION<br>DOC</th>
                            <th class="text-center align-middle">ECHANGE<br>BL</th>
                            <th class="text-center align-middle">BAD</th>
                            <th class="text-center align-middle">DATE DE<br>TRANSFERT</th>
                            <th class="text-center align-middle">OBSERVATIONS</th>
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
            $('#blTable').DataTable({
                ajax: '{{ route('admin.bls.data', ['activeTab' => $activeTab]) }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                pageLength: 25,
                columns: [
                    {data: (d) => `<a href="/admin/bls/${d.id}/edit">${d.bl ?? '-'}</a>`},
                    {data: (d) => d.customer_name ?? '-'},
                    {data: (d) => d.customer_company_name ?? '-'},
                    {data: (d) => d.company_name ?? '-'},
                    {data: (d) => d.type_operation ?? '-'},
                    {data: (d) => d.containers_count ?? 0},
                    {data: (d) => d.eta_date ? moment(d.eta_date).format('DD/MM/YYYY') : '-'},
                    {data: (d) => d.created ? moment(d.created).format('DD/MM/YYYY') : '-'},
                    {data: (d) => d.exchange_date ? moment(d.exchange_date).format('DD/MM/YYYY') : '-'},
                    {data: (d) => d.bad_date ? moment(d.bad_date).format('DD/MM/YYYY') : '-'},
                    {data: (d) => d.transfert_date ? moment(d.transfert_date).format('DD/MM/YYYY') : '-'},
                    {data: (d) => d.observation ?? '-'},
                    {data: (d) => {
                        const startBtn = d.is_started
                            ? ''
                            : `<form method="POST" action="/admin/bls/${d.id}/start" class="d-inline">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="POST">
                                <button class="btn btn-sm btn-warning" title="Démarrer"><i class="fa fa-play"></i></button>
                               </form>`;
                        const completeBtn = (d.is_started && !d.is_completed)
                            ? `<form method="POST" action="/admin/bls/${d.id}/complete" class="d-inline">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button class="btn btn-sm btn-success" title="Clôturer"><i class="fa fa-check"></i></button>
                               </form>`
                            : '';
                        const reopenBtn = d.is_completed
                            ? `<form method="POST" action="/admin/bls/${d.id}/reopen" class="d-inline">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button class="btn btn-sm btn-secondary" title="Rouvrir"><i class="fa fa-undo"></i></button>
                               </form>`
                            : '';
                        return `<a href="/admin/bls/${d.id}/edit" class="btn btn-sm btn-primary" title="Modifier"><i class="fa fa-edit"></i></a> ${startBtn} ${completeBtn} ${reopenBtn}`;
                    }},
                ],
            });
        });
    </script>
@endpush
