@extends('layouts.admin')

@section('title', 'Reçus d\'avance transport')

@section('content')
    <p>
        <a href="{{ route('admin.invoice-labels.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour aux libellés</a>
        <a href="{{ route('admin.invoice-advances.create') }}" class="btn btn-outline-primary"><i class="fa fa-plus"></i> Nouveau reçu</a>
    </p>

    <div class="card">
        <div class="card-header"><h5>Reçus d'avance transport</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="advanceTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">N&deg;</th>
                            <th class="text-center align-middle">DATE</th>
                            <th class="text-center align-middle">CHAUFFEUR</th>
                            <th class="text-center align-middle">CAMION</th>
                            <th class="text-center align-middle">BL</th>
                            <th class="text-center align-middle">DESTINATION</th>
                            <th class="text-center align-middle">TOTAL</th>
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
            $('#advanceTable').DataTable({
                ajax: '{{ route('admin.invoice-advances.data') }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                order: [],
                columns: [
                    {data: (d) => `<a href="/admin/invoice-advances/${d.id}/edit">${d.reference}</a>`},
                    {data: (d) => d.date_issued ?? '-'},
                    {data: (d) => d.driver_name ?? '-'},
                    {data: (d) => d.car ?? '-'},
                    {data: (d) => d.bl ?? '-'},
                    {data: (d) => d.destination ?? '-'},
                    {data: (d) => Number(d.total ?? 0).toLocaleString('fr-FR', {minimumFractionDigits: 2})},
                    {data: (d) => `
                        <a href="/admin/invoice-advances/${d.id}/edit" class="btn btn-sm btn-primary" title="Modifier"><i class="fa fa-edit"></i></a>
                        <a href="/admin/invoice-advances/${d.id}/print" class="btn btn-sm btn-secondary" title="Imprimer" target="_blank"><i class="fa fa-print"></i></a>
                        <form method="POST" action="/admin/invoice-advances/${d.id}" class="d-inline" onsubmit="return confirm('Archiver ce reçu ?')">
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
