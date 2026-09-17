@extends('layouts.admin')

@section('title', 'Modifier l\'opération')

@section('content')
    <p>
        <a href="{{ route('admin.accounting-invoices.operations.unbilled') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <form method="POST" action="{{ route('admin.accounting-invoices.operations.update', $operation) }}">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-header"><h5>Modifier l'opération</h5></div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label>BL</label>
                        <input type="text" class="form-control" value="{{ $operation->parentBl?->bl }}" disabled>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Désignation *</label>
                        <input type="text" name="designation" class="form-control" value="{{ old('designation', $operation->display_designation) }}" required>
                    </div>
                    <div class="col-md-2 form-group">
                        <label>Quantité *</label>
                        <input type="number" step="0.01" min="0" name="quantity" id="opQuantity" class="form-control" value="{{ old('quantity', $operation->quantity) }}" required>
                    </div>
                    <div class="col-md-2 form-group">
                        <label>Prix unitaire *</label>
                        <input type="number" step="0.01" min="0" name="unit_price" id="opUnitPrice" class="form-control" value="{{ old('unit_price', $operation->unit_price) }}" required>
                    </div>
                    <div class="col-md-2 form-group">
                        <label>Montant</label>
                        <input type="text" id="opAmount" class="form-control" value="{{ number_format($operation->amount, 2) }}" disabled>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mb-4 mt-3">
            <button type="submit" class="btn btn-primary btn-lg">Enregistrer</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        function updateOpAmount() {
            const qty = parseFloat($('#opQuantity').val()) || 0;
            const price = parseFloat($('#opUnitPrice').val()) || 0;
            $('#opAmount').val((qty * price).toFixed(2));
        }
        $('#opQuantity, #opUnitPrice').on('input', updateOpAmount);
    </script>
@endpush
