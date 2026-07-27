@php
    $bl = $bl ?? null;
    $old = fn ($field, $default = null) => old($field, $bl?->{$field} ?? $default);
@endphp

<div class="card">
    <div class="card-header"><h5>Identification</h5></div>
    <div class="card-block">
        <div class="row">
            <div class="col-md-4 form-group">
                <label>N&deg; B/L *</label>
                <input type="text" name="bl" class="form-control" value="{{ $old('bl') }}" required>
            </div>
            <div class="col-md-4 form-group">
                <label>Mandataire *</label>
                <select name="customer" id="customerSelect" class="form-control" required>
                    <option value="">Choisir...</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" data-companies='{{ $customerCompanies->where('customer', $customer->id)->values()->toJson() }}' @selected($old('customer') == $customer->id)>
                            {{ $customer->customer_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label>Client</label>
                <select name="customer_company" id="customerCompanySelect" class="form-control">
                    <option value="">Choisir...</option>
                    @foreach ($customerCompanies as $cc)
                        <option value="{{ $cc->id }}" data-customer="{{ $cc->customer }}" @selected($old('customer_company') == $cc->id)>
                            {{ $cc->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label>Compagnie de transport *</label>
                <select name="company" class="form-control" required>
                    <option value="">Choisir...</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" @selected($old('company') == $company->id)>{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label>Type d'op&eacute;ration *</label>
                <select name="type_operation" class="form-control" required>
                    <option value="">Choisir...</option>
                    <option value="CHARGEMENT" @selected($old('type_operation') == 'CHARGEMENT')>Chargement</option>
                    <option value="DEPOTAGE" @selected($old('type_operation') == 'DEPOTAGE')>D&eacute;potage</option>
                    <option value="TRANSFERT MAD" @selected($old('type_operation') == 'TRANSFERT MAD')>Transfert MAD</option>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <div class="checkbox-fade fade-in-primary">
                    <label>
                        <input type="checkbox" name="telex" value="1" @checked($old('telex'))>
                        <span class="cr"><i class="cr-icon fa fa-check"></i></span> Telex release
                    </label>
                </div>
            </div>
            <div class="col-md-2 form-group">
                <div class="checkbox-fade fade-in-primary">
                    <label>
                        <input type="checkbox" name="is_urgent" value="1" @checked($old('is_urgent'))>
                        <span class="cr"><i class="cr-icon fa fa-check"></i></span> Urgent
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h5>Marchandise</h5></div>
    <div class="card-block">
        <div class="row">
            <div class="col-md-12 form-group">
                <label>Description marchandise *</label>
                <input type="text" name="description" class="form-control" value="{{ $old('description') }}" required>
            </div>
            <div class="col-md-3 form-group">
                <label>Type de produit *</label>
                <select name="product_type" class="form-control" required>
                    <option value="">Choisir...</option>
                    @foreach ($productTypes as $productType)
                        <option value="{{ $productType->id }}" @selected($old('product_type') == $productType->id)>{{ $productType->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label>Nb colis *</label>
                <input type="number" name="nb_package" class="form-control" value="{{ $old('nb_package', 0) }}" required>
            </div>
            <div class="col-md-3 form-group">
                <label>Quantit&eacute; *</label>
                <input type="number" step="0.01" name="quantity" class="form-control" value="{{ $old('quantity', 0) }}" required>
            </div>
            <div class="col-md-3 form-group">
                <label>Valeur marchandise</label>
                <input type="number" step="0.01" name="product_value" class="form-control" value="{{ $old('product_value', 0) }}">
            </div>
            <div class="col-md-3 form-group">
                <label>Tarif</label>
                <input type="number" step="0.01" name="tariff" class="form-control" value="{{ $old('tariff', 0) }}">
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h5>Transport</h5></div>
    <div class="card-block">
        <div class="row">
            <div class="col-md-4 form-group">
                <label>Chargeur (shipper) *</label>
                <input type="text" name="shipper" class="form-control" value="{{ $old('shipper') }}" required>
            </div>
            <div class="col-md-4 form-group">
                <label>Navire *</label>
                <input type="text" name="vessel" class="form-control" value="{{ $old('vessel') }}" required>
            </div>
            <div class="col-md-4 form-group">
                <label>Date de chargement *</label>
                <input type="date" name="loading_date" class="form-control" value="{{ $old('loading_date') }}" required>
            </div>
            <div class="col-md-4 form-group">
                <label>Port de chargement *</label>
                <input type="text" name="port_of_load" class="form-control" value="{{ $old('port_of_load') }}" required>
            </div>
            <div class="col-md-4 form-group">
                <label>Port de d&eacute;chargement *</label>
                <input type="text" name="port_of_discharge" class="form-control" value="{{ $old('port_of_discharge') }}" required>
            </div>
            <div class="col-md-4 form-group">
                <label>ETA</label>
                <input type="date" name="eta_date" class="form-control" value="{{ $old('eta_date') }}">
            </div>
            <div class="col-md-12 form-group">
                <label>Itin&eacute;raire</label>
                <textarea name="route" class="form-control">{{ $old('route') }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h5>Destinataire</h5></div>
    <div class="card-block">
        <div class="row">
            <div class="col-md-4 form-group">
                <label>Consignataire</label>
                <input type="text" name="consignee" class="form-control" value="{{ $old('consignee') }}">
            </div>
            <div class="col-md-4 form-group">
                <label>Notify</label>
                <input type="text" name="notify" class="form-control" value="{{ $old('notify') }}">
            </div>
            <div class="col-md-4 form-group">
                <label>Agent</label>
                <input type="text" name="agent" class="form-control" value="{{ $old('agent') }}">
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h5>Observations</h5></div>
    <div class="card-block">
        <textarea name="observation" class="form-control" rows="3">{{ $old('observation') }}</textarea>
    </div>
</div>

<div class="text-center mb-4">
    <button type="submit" class="btn btn-primary btn-lg">Enregistrer</button>
</div>

@push('scripts')
    <script>
        $(function () {
            function filterCompanies() {
                const customerId = $('#customerSelect').val();
                $('#customerCompanySelect option').each(function () {
                    const opt = $(this);
                    if (!opt.val()) { return; }
                    opt.toggle(opt.data('customer') == customerId);
                });
            }
            $('#customerSelect').on('change', filterCompanies);
            filterCompanies();
        });
    </script>
@endpush
