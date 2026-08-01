@extends('layouts.admin')

@section('title', 'Modifier '.$customer->customer_name)

@section('content')
    <p>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <form method="POST" action="{{ route('admin.customers.update', $customer) }}">
        @csrf
        @method('PUT')
        @include('admin.customers._form')
        <div class="text-center mb-4">
            <button type="submit" class="btn btn-primary btn-lg">Enregistrer</button>
        </div>
    </form>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Clients</h5>
            <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#companyModal" onclick="openCompanyModal()">
                <i class="fa fa-plus"></i> Ajouter un client
            </button>
        </div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>NOM</th>
                            <th>CONTACT</th>
                            <th>PROPRIÉTAIRE</th>
                            <th>RCCM</th>
                            <th>NIF</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customer->companies as $company)
                            <tr>
                                <td>{{ $company->name }}</td>
                                <td>{{ $company->contact ?? '-' }}</td>
                                <td>{{ $company->owner_name ?? '-' }}</td>
                                <td>{{ $company->rccm ?? '-' }}</td>
                                <td>{{ $company->nif ?? '-' }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" title="Modifier"
                                        onclick='openCompanyModal(@json($company))' data-toggle="modal" data-target="#companyModal">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.customers.companies.destroy', [$customer, $company]) }}" class="d-inline" onsubmit="return confirm('Archiver ce client ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" title="Archiver"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">Aucun client rattaché.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card" id="documents">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Documents</h5>
            <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#documentModal">
                <i class="fa fa-plus"></i> Ajouter un document
            </button>
        </div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>LIBELLÉ</th>
                            <th>CLIENT</th>
                            <th>TYPE</th>
                            <th>FICHIER</th>
                            <th>AJOUTÉ LE</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customer->documents as $document)
                            <tr>
                                <td>{{ $document->name }}</td>
                                <td>{{ $document->clientCompany->name ?? '-' }}</td>
                                <td>{{ \App\Models\CustomerDocument::types()[$document->type] ?? '-' }}</td>
                                <td>
                                    @if ($document->filename)
                                        <a href="{{ asset('storage/customers/'.$document->filename) }}" target="_blank">Voir <i class="fa fa-external-link"></i></a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ optional($document->created)->format('d/m/Y') }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary" title="Modifier"
                                        onclick='openDocumentEditModal(@json(["id" => $document->id, "name" => $document->name, "type" => $document->type, "company" => $document->company, "created" => optional($document->created)->format("Y-m-d")]))'
                                        data-toggle="modal" data-target="#documentEditModal">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.customers.documents.destroy', [$customer, $document]) }}" class="d-inline" onsubmit="return confirm('Supprimer ce document ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" title="Supprimer"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">Aucun document.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="companyModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="companyForm" method="POST" action="{{ route('admin.customers.companies.store', $customer) }}">
                @csrf
                <div id="companyMethodField"></div>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Client</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nom de la société</label>
                            <input type="text" name="name" id="companyName" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Contact</label>
                            <input type="text" name="contact" id="companyContact" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Adresse</label>
                            <textarea name="address" id="companyAddress" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Nom du propriétaire</label>
                            <input type="text" name="owner_name" id="companyOwnerName" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Contact du propriétaire</label>
                            <input type="text" name="owner_contact" id="companyOwnerContact" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>RCCM</label>
                            <input type="text" name="rccm" id="companyRccm" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>NIF</label>
                            <input type="text" name="nif" id="companyNif" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>CNI/Passeport</label>
                            <input type="text" name="cni" id="companyCni" class="form-control">
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

    <div class="modal fade" id="documentModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.customers.documents.store', $customer) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Document</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Type de document</label>
                            <select name="type" class="form-control">
                                <option value="">--Choisir--</option>
                                @foreach (\App\Models\CustomerDocument::types() as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Client</label>
                            <select name="company" class="form-control">
                                <option value="">--Aucun client (document du mandataire)--</option>
                                @foreach ($customer->companies as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Fichier *</label>
                            <input type="file" name="file" class="form-control-file" required>
                            <small class="form-text text-muted">PDF, Word, Excel ou image — 2 Mo maximum.</small>
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

    <div class="modal fade" id="documentEditModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" id="documentEditForm" action="">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modifier le document</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libellé *</label>
                            <input type="text" name="name" id="documentEditName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Type de document</label>
                            <select name="type" id="documentEditType" class="form-control">
                                <option value="">--Choisir--</option>
                                @foreach (\App\Models\CustomerDocument::types() as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Client</label>
                            <select name="company" id="documentEditCompany" class="form-control">
                                <option value="">--Aucun client (document du mandataire)--</option>
                                @foreach ($customer->companies as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="created" id="documentEditDate" class="form-control">
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
        function openDocumentEditModal(doc) {
            const form = document.getElementById('documentEditForm');
            form.action = `/admin/customers/{{ $customer->id }}/documents/${doc.id}`;
            document.getElementById('documentEditName').value = doc.name ?? '';
            document.getElementById('documentEditType').value = doc.type ?? '';
            document.getElementById('documentEditCompany').value = doc.company ?? '';
            document.getElementById('documentEditDate').value = doc.created ?? '';
        }

        function openCompanyModal(company) {
            const form = document.getElementById('companyForm');
            const methodField = document.getElementById('companyMethodField');
            methodField.innerHTML = '';

            if (company) {
                form.action = `/admin/customers/{{ $customer->id }}/companies/${company.id}`;
                methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
                document.getElementById('companyName').value = company.name ?? '';
                document.getElementById('companyContact').value = company.contact ?? '';
                document.getElementById('companyAddress').value = company.address ?? '';
                document.getElementById('companyOwnerName').value = company.owner_name ?? '';
                document.getElementById('companyOwnerContact').value = company.owner_contact ?? '';
                document.getElementById('companyRccm').value = company.rccm ?? '';
                document.getElementById('companyNif').value = company.nif ?? '';
                document.getElementById('companyCni').value = company.cni ?? '';
            } else {
                form.action = '{{ route('admin.customers.companies.store', $customer) }}';
                form.reset();
            }
        }
    </script>
@endpush
