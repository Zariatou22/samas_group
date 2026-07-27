@extends('layouts.admin')

@section('title', 'Chauffeurs')

@section('content')
    <p>
        <a href="{{ route('admin.cars.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#driverModal" onclick="openDriverModal()">
            <i class="fa fa-plus"></i> Nouveau chauffeur
        </button>
    </p>

    <div class="card">
        <div class="card-header"><h5>Liste des chauffeurs</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="driverTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">NOM</th>
                            <th class="text-center align-middle">CONTACT</th>
                            <th class="text-center align-middle">TRANSPORTEUR</th>
                            <th class="text-center align-middle">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="driverModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" id="driverForm" action="{{ route('admin.car-drivers.store') }}">
                @csrf
                <div id="driverMethodField"></div>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Chauffeur</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Transporteur *</label>
                            <select name="owner" id="driverOwner" class="form-control" required>
                                <option value="">Choisir...</option>
                                @foreach ($owners as $owner)
                                    <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nom *</label>
                            <input type="text" name="name" id="driverName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Contact</label>
                            <input type="text" name="contact" id="driverContact" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openDriverModal(driver) {
            const form = document.getElementById('driverForm');
            const methodField = document.getElementById('driverMethodField');
            methodField.innerHTML = '';

            if (driver) {
                form.action = `/admin/car-drivers/${driver.id}`;
                methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            } else {
                form.action = '{{ route('admin.car-drivers.store') }}';
            }

            document.getElementById('driverOwner').value = driver?.owner ?? '';
            document.getElementById('driverName').value = driver?.name ?? '';
            document.getElementById('driverContact').value = driver?.contact ?? '';
        }

        $(function () {
            $('#driverTable').DataTable({
                ajax: '{{ route('admin.car-drivers.data') }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: [
                    {data: (d) => d.name},
                    {data: (d) => d.contact ?? '-'},
                    {data: (d) => d.owner_name ?? '-'},
                    {data: (d) => `
                        <button type="button" class="btn btn-sm btn-primary" title="Modifier"
                            data-toggle="modal" data-target="#driverModal" data-driver-id="${d.id}">
                            <i class="fa fa-edit"></i>
                        </button>
                        <form method="POST" action="/admin/car-drivers/${d.id}" class="d-inline" onsubmit="return confirm('Archiver ce chauffeur ?')">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button class="btn btn-sm btn-danger" title="Archiver"><i class="fa fa-trash"></i></button>
                        </form>
                    `},
                ],
                rowCallback: function (row, data) {
                    $(row).find('button[data-driver-id]').data('driver', data);
                },
            });

            $('#driverTable').on('click', 'button[data-driver-id]', function () {
                openDriverModal($(this).data('driver'));
            });
        });
    </script>
@endpush
