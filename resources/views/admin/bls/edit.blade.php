@extends('layouts.admin')

@section('title', 'Modifier le BL '.$bl->bl)

@section('content')
    <p>
        <a href="{{ route('admin.bls.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <form method="POST" action="{{ route('admin.bls.update', $bl) }}">
        @csrf
        @method('PUT')
        @include('admin.bls._form')
    </form>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Conteneurs</h5>
            <div>
                @if ($bl->type_operation === 'DEPOTAGE')
                    <a href="{{ route('admin.bls.unpot.index', $bl) }}" class="btn btn-sm btn-outline-warning"><i class="fa fa-download"></i> Dépotage</a>
                @endif
                <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#containerModal" onclick="openContainerModal()">
                    <i class="fa fa-plus"></i> Ajouter un conteneur
                </button>
            </div>
        </div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>TYPE</th>
                            <th>NUM&Eacute;RO</th>
                            <th>NAVIRE</th>
                            <th>ETA</th>
                            <th>COLIS</th>
                            <th>QUANTIT&Eacute;</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($containers as $container)
                            <tr>
                                <td>{{ $container->type_tc }}</td>
                                <td>{{ $container->numero }}</td>
                                <td>{{ $container->ship }}</td>
                                <td>{{ optional($container->eta)->format('d/m/Y') }}</td>
                                <td>{{ $container->nb_package }}</td>
                                <td>{{ $container->quantity }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary" title="Modifier"
                                        onclick='openContainerModal(@json($container->only(["id", "type_tc", "numero", "ship", "lead_number", "product_type", "nb_package", "quantity"]) + ["eta" => optional($container->eta)->format("Y-m-d")]))'>
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.bls.containers.destroy', [$bl, $container]) }}" class="d-inline" onsubmit="return confirm('Supprimer ce conteneur ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" title="Supprimer"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">Aucun conteneur.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5>Suivi</h5></div>
        <div class="card-block">
            <div class="row">
                <div class="col-md-4">
                    <form method="POST" action="{{ route('admin.bls.exchange', $bl) }}">
                        @csrf
                        <label>Échange BL</label>
                        <input type="date" name="date_received" class="form-control" value="{{ optional($bl->exchange?->date_received)->format('Y-m-d') }}">
                        <button type="submit" class="btn btn-sm btn-info mt-2">Enregistrer</button>
                    </form>
                </div>
                <div class="col-md-4">
                    <form method="POST" action="{{ route('admin.bls.bad', $bl) }}">
                        @csrf
                        <label>BAD - Date de réception</label>
                        <input type="date" name="date_received" class="form-control mb-2" value="{{ optional($bl->deliveryNote?->date_received)->format('Y-m-d') }}">
                        <label>BAD - Date de validité</label>
                        <input type="date" name="date_valid" class="form-control" value="{{ optional($bl->deliveryNote?->date_valid)->format('Y-m-d') }}">
                        <button type="submit" class="btn btn-sm btn-info mt-2">Enregistrer</button>
                    </form>
                </div>
                <div class="col-md-4">
                    <label>Date de transfert</label>
                    <form method="POST" action="{{ route('admin.bls.transfert', $bl) }}">
                        @csrf
                        <div class="mb-2" style="max-height: 150px; overflow-y: auto;">
                            @forelse ($containers as $container)
                                <div class="form-check">
                                    <input type="checkbox" name="containers[]" value="{{ $container->id }}" class="form-check-input" id="transfertContainer{{ $container->id }}">
                                    <label class="form-check-label" for="transfertContainer{{ $container->id }}">{{ $container->type_tc }} — {{ $container->numero }}</label>
                                </div>
                            @empty
                                <p class="text-muted mb-0">Aucun conteneur.</p>
                            @endforelse
                        </div>
                        <input type="date" name="date_received" class="form-control mb-2" value="{{ now()->format('Y-m-d') }}" required>
                        <button type="submit" class="btn btn-sm btn-info">Enregistrer</button>
                    </form>
                    @if ($transferts->isNotEmpty())
                        <table class="table table-sm table-bordered mt-2">
                            <thead><tr><th>Conteneur</th><th>Date</th><th></th></tr></thead>
                            <tbody>
                                @foreach ($transferts as $transfert)
                                    <tr>
                                        <td>{{ $transfert->parentContainer?->numero }}</td>
                                        <td>{{ $transfert->date_received?->format('d/m/Y') }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.bls.transfert.destroy', [$bl, $transfert]) }}" onsubmit="return confirm('Annuler ce transfert ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="containerModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" id="containerForm" action="{{ route('admin.bls.containers.store', $bl) }}">
                @csrf
                <div id="containerMethodField"></div>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Conteneur</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Type *</label>
                                <input type="text" name="type_tc" id="containerTypeTc" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Num&eacute;ro *</label>
                                <input type="text" name="numero" id="containerNumero" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Navire *</label>
                                <input type="text" name="ship" id="containerShip" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>ETA *</label>
                                <input type="date" name="eta" id="containerEta" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>N&deg; suivi (lead)</label>
                                <input type="text" name="lead_number" id="containerLeadNumber" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Type d'emballage</label>
                                <select name="product_type" id="containerProductType" class="form-control">
                                    <option value="">Choisir...</option>
                                    @foreach ($productTypes as $productType)
                                        <option value="{{ $productType->id }}">{{ $productType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Nb colis</label>
                                <input type="number" name="nb_package" id="containerNbPackage" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Quantit&eacute;</label>
                                <input type="number" step="0.01" name="quantity" id="containerQuantity" class="form-control">
                            </div>
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
        function openContainerModal(container) {
            const form = document.getElementById('containerForm');
            const methodField = document.getElementById('containerMethodField');
            methodField.innerHTML = '';

            if (container) {
                form.action = `/admin/bls/{{ $bl->id }}/containers/${container.id}`;
                methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            } else {
                form.action = '{{ route('admin.bls.containers.store', $bl) }}';
            }

            document.getElementById('containerTypeTc').value = container?.type_tc ?? '';
            document.getElementById('containerNumero').value = container?.numero ?? '';
            document.getElementById('containerShip').value = container?.ship ?? '';
            document.getElementById('containerEta').value = container?.eta ?? '';
            document.getElementById('containerLeadNumber').value = container?.lead_number ?? '';
            document.getElementById('containerProductType').value = container?.product_type ?? '';
            document.getElementById('containerNbPackage').value = container?.nb_package ?? '';
            document.getElementById('containerQuantity').value = container?.quantity ?? '';
        }
    </script>
@endpush
