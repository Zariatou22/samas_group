@extends('layouts.admin')

@section('title', $title)

@section('content')
    <p>
        <a href="{{ route('admin.accounting-invoices.operations.unbilled') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <form method="POST" action="{{ route('admin.accounting-invoices.operations.store') }}" id="operationForm">
        @csrf
        <div class="card">
            <div class="card-header"><h5>{{ $title }}</h5></div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Client *</label>
                        <select name="customer" id="operationCustomer" class="form-control" required>
                            <option value="">Choisissez un client</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" @selected($prefillCustomer == $customer->id)>{{ $customer->customer_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8 form-group">
                        <label>BL(s) non facturé(s) *</label>
                        <div id="operationBlList" class="border rounded p-2" style="max-height:180px; overflow-y:auto;"></div>
                    </div>
                </div>
                <hr>
                <label>Désignations</label>
                @if ($labels->isEmpty())
                    <p class="text-muted">Aucune désignation enregistrée. <a href="{{ route('admin.invoice-labels.index') }}">Ajouter une désignation</a>.</p>
                @endif
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="operationDesignationsTable">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width:2em;"></th>
                                <th>DESIGNATION</th>
                                <th style="width:130px">QUANTITE</th>
                                <th style="width:150px">PRIX UNITAIRE</th>
                                <th style="width:150px">MONTANT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($labels as $label)
                                <tr>
                                    <td class="align-middle text-center">
                                        <input type="checkbox" class="designation-checkbox" data-label-id="{{ $label->id }}">
                                    </td>
                                    <td class="align-middle">{{ $label->name }}</td>
                                    <td>
                                        <input type="number" step="0.01" min="0" class="form-control designation-qty" value="1" disabled>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" class="form-control designation-price" value="0" disabled>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control designation-amount" value="0.00" disabled>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div id="operationHiddenFields"></div>
            </div>
        </div>
        <div class="text-center mb-4 mt-3">
            <button type="submit" class="btn btn-primary btn-lg">Enregistrer</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        const allBls = @json($bls);
        const prefillBls = @json($prefillBls);

        function renderBlList() {
            const customerId = $('#operationCustomer').val();
            const $list = $('#operationBlList');
            $list.empty();
            allBls
                .filter(b => !customerId || String(b.customer) === String(customerId))
                .forEach(b => {
                    const checked = prefillBls.includes(b.id) ? 'checked' : '';
                    const $row = $(`
                        <div class="form-check">
                            <input class="form-check-input operation-bl-checkbox" type="checkbox" name="bl[]" value="${b.id}" id="opBl${b.id}" ${checked}>
                            <label class="form-check-label" for="opBl${b.id}">${b.bl}${b.description ? ' — ' + b.description : ''}</label>
                        </div>
                    `);
                    $list.append($row);
                });
        }

        function toggleDesignationRow(checkbox) {
            const $row = $(checkbox).closest('tr');
            const enabled = checkbox.checked;
            $row.find('.designation-qty, .designation-price').prop('disabled', !enabled);
            if (!enabled) {
                $row.find('.designation-amount').val('0.00');
            }
            updateRowAmount($row);
        }

        function updateRowAmount($row) {
            const qty = parseFloat($row.find('.designation-qty').val()) || 0;
            const price = parseFloat($row.find('.designation-price').val()) || 0;
            $row.find('.designation-amount').val((qty * price).toFixed(2));
        }

        function syncHiddenFields() {
            const $hidden = $('#operationHiddenFields');
            $hidden.empty();
            let i = 0;
            $('.designation-checkbox:checked').each(function () {
                const $row = $(this).closest('tr');
                const labelId = $(this).data('label-id');
                const qty = $row.find('.designation-qty').val();
                const price = $row.find('.designation-price').val();
                $hidden.append(`<input type="hidden" name="designation_id[${i}]" value="${labelId}">`);
                $hidden.append(`<input type="hidden" name="quantity[${i}]" value="${qty}">`);
                $hidden.append(`<input type="hidden" name="unit_price[${i}]" value="${price}">`);
                i++;
            });
        }

        $(function () {
            renderBlList();
            $('#operationCustomer').on('change', renderBlList);

            $('#operationDesignationsTable').on('change', '.designation-checkbox', function () {
                toggleDesignationRow(this);
            });
            $('#operationDesignationsTable').on('input', '.designation-qty, .designation-price', function () {
                updateRowAmount($(this).closest('tr'));
            });

            $('#operationForm').on('submit', function () {
                syncHiddenFields();
            });
        });
    </script>
@endpush
