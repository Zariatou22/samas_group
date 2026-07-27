@extends('layouts.admin')

@section('title', 'Suivi des conteneurs')

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <div class="card">
        <div class="card-header"><h5>Franchises — Suivi des conteneurs arrivés</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="trackingTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">CONTENEUR</th>
                            <th class="text-center align-middle">N&deg; B/L</th>
                            <th class="text-center align-middle">MANDATAIRE</th>
                            <th class="text-center align-middle">ETA</th>
                            <th class="text-center align-middle">JOURS &Eacute;COUL&Eacute;S</th>
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
            $('#trackingTable').DataTable({
                ajax: '{{ route('admin.container-tracking.data') }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: [
                    {data: (d) => d.numero ?? '-'},
                    {data: (d) => d.bl ?? '-'},
                    {data: (d) => d.customer_name ?? '-'},
                    {data: (d) => d.eta ?? '-'},
                    {data: (d) => {
                        const days = d.days_since_eta ?? 0;
                        const color = days > 14 ? 'danger' : (days > 7 ? 'warning' : 'success');
                        return `<span class="badge badge-${color}">${days}</span>`;
                    }},
                ],
                order: [[3, 'asc']],
            });
        });
    </script>
@endpush
