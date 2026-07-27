@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <div class="row">
        @foreach ($stats as $stat)
            <div class="col-md-3 col-sm-6">
                <a href="{{ $stat['url'] }}" class="text-decoration-none">
                    <div class="card bg-{{ $stat['color'] }} text-white">
                        <div class="card-block">
                            <h2 class="m-b-0">{{ $stat['count'] }}</h2>
                            <h6 class="m-b-0">{{ $stat['label'] }}</h6>
                            <small>{{ $stat['description'] }}</small>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header"><h5>Conteneurs de cette année</h5></div>
        <div class="card-block">
            <canvas id="containersChart" height="80"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5>Arrivage cette semaine</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="weekArrivalsTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">MANDATAIRE</th>
                            <th class="text-center align-middle">N&deg; B/L</th>
                            <th class="text-center align-middle">CONTENEURS</th>
                            <th class="text-center align-middle">ETA</th>
                            <th class="text-center align-middle">COMPAGNIE</th>
                            <th class="text-center align-middle">MARCHANDISES</th>
                            <th class="text-center align-middle">TYPE</th>
                            <th class="text-center align-middle">&Eacute;CHANGE BL</th>
                            <th class="text-center align-middle">R&Eacute;CEPTION BAD</th>
                            <th class="text-center align-middle">VALIDIT&Eacute; BAD</th>
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
    <script src="{{ asset('vendor/able/assets/js/chart.js/Chart.js') }}"></script>
    <script>
        $(function () {
            $.get('{{ route('admin.dashboard.containers-per-month') }}', function (res) {
                new Chart(document.getElementById('containersChart').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: res.labels,
                        datasets: [{ label: 'Conteneurs', data: res.data, backgroundColor: '#1B75BC' }],
                    },
                });
            });

            $('#weekArrivalsTable').DataTable({
                ajax: '{{ route('admin.dashboard.week-arrivals') }}',
                dataSrc: 'data',
                paging: false,
                searching: false,
                info: false,
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: [
                    {data: (d) => d.customer_name ?? '-'},
                    {data: (d) => d.bl ?? '-'},
                    {data: (d) => d.containers_count ?? 0},
                    {data: (d) => d.etas || '-'},
                    {data: (d) => d.company_name ?? '-'},
                    {data: (d) => d.description ?? '-'},
                    {data: (d) => d.type_operation ? `<span class="badge badge-secondary">${d.type_operation}</span>` : '-'},
                    {data: (d) => d.has_exchange ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>'},
                    {data: (d) => d.bad_date ?? '—'},
                    {data: (d) => d.valid_date ?? '—'},
                    {data: (d) => d.observation ?? '-'},
                    {data: (d) => `<a href="/admin/bls/${d.id}/edit" class="btn btn-sm btn-primary" title="Modifier"><i class="fa fa-edit"></i></a>`},
                ],
            });
        });
    </script>
@endpush
