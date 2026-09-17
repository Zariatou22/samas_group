@extends('layouts.admin')

@section('title', 'Modifier la facture pro forma '.$invoice->reference)

@section('content')
    <p>
        <a href="{{ route('admin.proforma-invoices.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <form method="POST" action="{{ route('admin.proforma-invoices.update', $invoice) }}">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-header"><h5>Détails de la facture pro forma</h5></div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Référence *</label>
                        <input type="text" name="reference" class="form-control" value="{{ old('reference', $invoice->reference) }}" required maxlength="100">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Client *</label>
                        <select name="customer" class="form-control" required>
                            <option value="">Choisir...</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}" @selected(old('customer', $invoice->customer) == $c->id)>{{ $c->customer_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Date *</label>
                        <input type="datetime-local" name="date_issued" class="form-control" value="{{ old('date_issued', $invoice->date_issued?->format('Y-m-d\TH:i')) }}" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Maison consignataire</label>
                        <input type="text" name="consignee_house" class="form-control" value="{{ old('consignee_house', $invoice->consignee_house) }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Type de conteneurs</label>
                        <input type="text" name="container_type" class="form-control" value="{{ old('container_type', $invoice->container_type) }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Nombre de conteneurs</label>
                        <input type="number" step="1" min="0" name="container_count" class="form-control" value="{{ old('container_count', $invoice->container_count) }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Nature marchandise</label>
                        <input type="text" name="goods_nature" class="form-control" value="{{ old('goods_nature', $invoice->goods_nature) }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Poids et valeur</label>
                        <input type="text" name="weight_value" class="form-control" value="{{ old('weight_value', $invoice->weight_value) }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mb-4">
            <button type="submit" class="btn btn-primary btn-lg">Enregistrer</button>
        </div>
    </form>
@endsection
