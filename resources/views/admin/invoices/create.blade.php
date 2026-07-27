@extends('layouts.admin')

@section('title', 'Nouvelle facture')

@section('content')
    <p>
        <a href="{{ route('admin.invoices.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <form method="POST" action="{{ route('admin.invoices.store') }}">
        @csrf
        <div class="card">
            <div class="card-header"><h5>Facture</h5></div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Mandataire *</label>
                        <select name="customer" id="customerSelect" class="form-control" required onchange="onCustomerChange()">
                            <option value="">Choisir...</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}" @selected(old('customer') == $c->id)>{{ $c->customer_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Client</label>
                        <select name="customer_company" id="companySelect" class="form-control">
                            <option value="">Choisir un mandataire d'abord...</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>BL *</label>
                        <select name="bl" class="form-control" required>
                            <option value="">Choisir...</option>
                            @foreach ($bls as $bl)
                                <option value="{{ $bl->id }}" @selected(old('bl') == $bl->id)>{{ $bl->bl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Désignation *</label>
                        <select name="label" class="form-control" required>
                            <option value="">Choisir...</option>
                            @foreach ($labels as $label)
                                <option value="{{ $label->id }}" @selected(old('label') == $label->id)>{{ $label->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Référence *</label>
                        <input type="text" name="reference" class="form-control" value="{{ old('reference') }}" required maxlength="100">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Montant *</label>
                        <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount', 0) }}" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h5>Paiement</h5></div>
            <div class="card-block">
                <div class="row">
                    <div class="col-12 form-group">
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="paid" id="paidCheck" value="1" onchange="togglePaid()" @checked(old('paid'))>
                            <label for="paidCheck">Payée</label>
                        </div>
                    </div>
                    <div class="col-md-6 form-group" id="paidAmountGroup" style="display:none">
                        <label>Montant payé</label>
                        <input type="number" step="0.01" name="paid_amount" class="form-control" value="{{ old('paid_amount') }}">
                    </div>
                    <div class="col-md-6 form-group" id="paymentReferenceGroup" style="display:none">
                        <label>Référence du paiement</label>
                        <input type="text" name="payment_reference" class="form-control" value="{{ old('payment_reference') }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mb-4">
            <button type="submit" class="btn btn-primary btn-lg">Enregistrer</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        const companiesByCustomer = @json($companiesByCustomer);

        function onCustomerChange() {
            const customerId = document.getElementById('customerSelect').value;
            const select = document.getElementById('companySelect');
            select.innerHTML = '<option value="">Choisir...</option>';
            (companiesByCustomer[customerId] ?? []).forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.name;
                select.appendChild(opt);
            });
        }

        function togglePaid() {
            const checked = document.getElementById('paidCheck').checked;
            document.getElementById('paidAmountGroup').style.display = checked ? '' : 'none';
            document.getElementById('paymentReferenceGroup').style.display = checked ? '' : 'none';
        }

        $(function () {
            if (document.getElementById('customerSelect').value) onCustomerChange();
            togglePaid();
        });
    </script>
@endpush
