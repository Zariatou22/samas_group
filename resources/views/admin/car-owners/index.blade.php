@extends('layouts.admin')

@section('title', 'Transporteurs')

@section('content')
    <p>
        <a href="{{ route('admin.cars.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#ownerModal" onclick="openOwnerModal()">
            <i class="fa fa-plus"></i> Nouveau transporteur
        </button>
    </p>

    <div class="card">
        <div class="card-header"><h5>Liste des transporteurs</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="ownerTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">NOM</th>
                            <th class="text-center align-middle">CONTACT</th>
                            <th class="text-center align-middle">ADRESSE</th>
                            <th class="text-center align-middle">V&Eacute;HICULES</th>
                            <th class="text-center align-middle">CHAUFFEURS</th>
                            <th class="text-center align-middle">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ownerModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" id="ownerForm" action="{{ route('admin.car-owners.store') }}">
                @csrf
                <div id="ownerMethodField"></div>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Transporteur</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nom *</label>
                            <input type="text" name="name" id="ownerName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Contact</label>
                            <input type="text" name="contact" id="ownerContact" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Adresse</label>
                            <input type="text" name="address" id="ownerAddress" class="form-control">
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
        function openOwnerModal(owner) {
            const form = document.getElementById('ownerForm');
            const methodField = document.getElementById('ownerMethodField');
            methodField.innerHTML = '';

            if (owner) {
                form.action = `/admin/car-owners/${owner.id}`;
                methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            } else {
                form.action = '{{ route('admin.car-owners.store') }}';
            }

            document.getElementById('ownerName').value = owner?.name ?? '';
            document.getElementById('ownerContact').value = owner?.contact ?? '';
            document.getElementById('ownerAddress').value = owner?.address ?? '';
        }

        $(function () {
            $('#ownerTable').DataTable({
                ajax: '{{ route('admin.car-owners.data') }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: [
                    {data: (d) => d.name},
                    {data: (d) => d.contact ?? '-'},
                    {data: (d) => d.address ?? '-'},
                    {data: (d) => `<span class="badge badge-secondary">${d.cars_count}</span>`},
                    {data: (d) => `<span class="badge badge-secondary">${d.drivers_count}</span>`},
                    {data: (d) => `
                        <button type="button" class="btn btn-sm btn-primary" title="Modifier"
                            data-toggle="modal" data-target="#ownerModal" data-owner-id="${d.id}">
                            <i class="fa fa-edit"></i>
                        </button>
                        <form method="POST" action="/admin/car-owners/${d.id}" class="d-inline" onsubmit="return confirm('Archiver ce transporteur ?')">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button class="btn btn-sm btn-danger" title="Archiver"><i class="fa fa-trash"></i></button>
                        </form>
                    `},
                ],
                rowCallback: function (row, data) {
                    $(row).find('button[data-owner-id]').data('owner', data);
                },
            });

            $('#ownerTable').on('click', 'button[data-owner-id]', function () {
                openOwnerModal($(this).data('owner'));
            });
        });
    </script>
@endpush
