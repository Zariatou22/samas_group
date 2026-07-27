@extends('layouts.admin')

@section('title', 'Modifier la facture '.$invoice->reference)

@section('content')
    <p>
        <a href="{{ route('admin.invoices.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <form method="POST" action="{{ route('admin.invoices.update', $invoice) }}">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-header"><h5>Facture</h5></div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Mandataire</label>
                        <input type="text" class="form-control" value="{{ $invoice->mandataire?->customer_name }}" disabled>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Client</label>
                        <select name="customer_company" class="form-control">
                            <option value="">Choisir...</option>
                            @foreach ($companies as $c)
                                <option value="{{ $c->id }}" @selected(old('customer_company', $invoice->customer_company) == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>BL</label>
                        <input type="text" class="form-control" value="{{ $invoice->parentBl?->bl }}" disabled>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Désignation *</label>
                        <select name="label" class="form-control" required>
                            <option value="">Choisir...</option>
                            @foreach ($labels as $label)
                                <option value="{{ $label->id }}" @selected(old('label', $invoice->label) == $label->id)>{{ $label->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Référence *</label>
                        <input type="text" name="reference" class="form-control" value="{{ old('reference', $invoice->reference) }}" required maxlength="100">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Montant *</label>
                        <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount', $invoice->amount) }}" required>
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
                            <input type="checkbox" name="paid" id="paidCheck" value="1" onchange="togglePaid()" @checked(old('paid', $invoice->paid))>
                            <label for="paidCheck">Payée</label>
                        </div>
                    </div>
                    <div class="col-md-6 form-group" id="paidAmountGroup">
                        <label>Montant payé</label>
                        <input type="number" step="0.01" name="paid_amount" class="form-control" value="{{ old('paid_amount', $invoice->payment?->amount) }}">
                    </div>
                    <div class="col-md-6 form-group" id="paymentReferenceGroup">
                        <label>Référence du paiement</label>
                        <input type="text" name="payment_reference" class="form-control" value="{{ old('payment_reference', $invoice->payment?->reference) }}">
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
        function togglePaid() {
            const checked = document.getElementById('paidCheck').checked;
            document.getElementById('paidAmountGroup').style.display = checked ? '' : 'none';
            document.getElementById('paymentReferenceGroup').style.display = checked ? '' : 'none';
        }

        $(function () { togglePaid(); });
    </script>
@endpush
