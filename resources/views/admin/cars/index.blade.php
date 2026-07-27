@extends('layouts.admin')

@section('title', 'Véhicules')

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <a href="{{ route('admin.cars.create') }}" class="btn btn-outline-primary"><i class="fa fa-plus"></i> Nouveau véhicule</a>
    </p>

    <div class="card">
        <div class="card-header"><h5>Liste des véhicules</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="carTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">IMMATRICULATION</th>
                            <th class="text-center align-middle">TRANSPORTEUR</th>
                            <th class="text-center align-middle">CHAUFFEUR</th>
                            <th class="text-center align-middle">CONTACT CHAUFFEUR</th>
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
            $('#carTable').DataTable({
                ajax: '{{ route('admin.cars.data') }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: [
                    {data: (d) => `<a href="/admin/cars/${d.id}/edit">${d.full_registration}</a>`},
                    {data: (d) => d.owner_name ?? '-'},
                    {data: (d) => d.driver_name ?? '-'},
                    {data: (d) => d.driver_contact ?? '-'},
                    {data: (d) => `
                        <a href="/admin/cars/${d.id}/edit" class="btn btn-sm btn-primary" title="Modifier"><i class="fa fa-edit"></i></a>
                        <form method="POST" action="/admin/cars/${d.id}" class="d-inline" onsubmit="return confirm('Archiver ce véhicule ?')">
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
