<div class="card">
    <div class="card-header"><h5>Véhicule</h5></div>
    <div class="card-block">
        <div class="row">
            <div class="col-md-6 form-group">
                <label>Immatriculation tracteur *</label>
                <input type="text" name="front_registration" class="form-control" value="{{ old('front_registration', $car->front_registration ?? '') }}" required maxlength="50">
            </div>
            <div class="col-md-6 form-group">
                <label>Immatriculation remorque *</label>
                <input type="text" name="back_registration" class="form-control" value="{{ old('back_registration', $car->back_registration ?? '') }}" required maxlength="50">
            </div>
            <div class="col-md-6 form-group">
                <label>Transporteur *</label>
                <div class="input-group">
                    <select name="owner" id="carOwnerSelect" class="form-control" required onchange="filterDrivers()">
                        <option value="">Choisir...</option>
                        @foreach ($owners as $owner)
                            <option value="{{ $owner->id }}" @selected(old('owner', session('newOwnerId', $car->owner ?? null)) == $owner->id)>{{ $owner->name }}</option>
                        @endforeach
                    </select>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#ownerModal"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 form-group">
                <label>Chauffeur *</label>
                <div class="input-group">
                    <select name="driver" id="carDriverSelect" class="form-control" required>
                        <option value="">Choisir...</option>
                    </select>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#driverModal"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ownerModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.car-owners.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nouveau transporteur</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nom *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Contact</label>
                        <input type="text" name="contact" class="form-control">
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

<div class="modal fade" id="driverModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.car-drivers.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nouveau chauffeur</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Transporteur *</label>
                        <select name="owner" class="form-control" required>
                            <option value="">Choisir...</option>
                            @foreach ($owners as $owner)
                                <option value="{{ $owner->id }}" @selected(session('newOwnerId') == $owner->id || old('owner', $car->owner ?? null) == $owner->id)>{{ $owner->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nom *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Contact</label>
                        <input type="text" name="contact" class="form-control">
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

@push('scripts')
    <script>
        const allDrivers = @json($drivers);
        const selectedDriverId = @json(old('driver', session('newDriverId', $car->driver ?? null)));

        function filterDrivers() {
            const ownerId = document.getElementById('carOwnerSelect').value;
            const select = document.getElementById('carDriverSelect');
            select.innerHTML = '<option value="">Choisir...</option>';

            allDrivers
                .filter(d => String(d.owner) === String(ownerId))
                .forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.id;
                    opt.textContent = d.name;
                    if (selectedDriverId && String(d.id) === String(selectedDriverId)) {
                        opt.selected = true;
                    }
                    select.appendChild(opt);
                });
        }

        $(function () {
            if (document.getElementById('carOwnerSelect').value) {
                filterDrivers();
            }
        });
    </script>
@endpush
