@extends('layouts.admin')

@section('title', $title)

@section('content')
    <p>
        <a href="{{ route('admin.accounting-invoices.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <a href="{{ route('admin.accounting-invoices.operations.create') }}" class="btn btn-outline-primary"><i class="fa fa-plus"></i> Nouvelle opération</a>
    </p>

    <div class="card mb-4">
        <div class="card-header"><h5>Choisir un client</h5></div>
        <div class="card-block">
            <div class="row">
                <div class="col-md-4 form-group">
                    <label>Client</label>
                    <select id="filterCustomer" class="form-control">
                        <option value="">Choisissez un client</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="table-responsive mt-3" id="unbilledBlsWrapper" style="display:none;">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width:3em;"><input type="checkbox" id="selectAllUnbilledBls" title="Tout sélectionner"></th>
                            <th>BL</th>
                            <th>DESCRIPTION</th>
                        </tr>
                    </thead>
                    <tbody id="unbilledBlsBody"></tbody>
                </table>
                <button type="button" id="createOperationBtn" class="btn btn-primary" disabled>
                    <i class="fa fa-plus"></i> Créer une opération pour la sélection
                </button>
            </div>
            <p id="unbilledBlsEmpty" class="text-muted mb-0 mt-3" style="display:none;">Ce client n'a aucun BL non facturé.</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5>{{ $title }}</h5></div>
        <div class="card-block">
            @if ($operations->isNotEmpty())
                <div class="mb-3">
                    <button type="button" id="billSelectionBtn" class="btn btn-success" disabled>
                        <i class="fa fa-check-circle"></i> Facturer la sélection
                    </button>
                    <span id="billSelectionWarning" class="text-danger ml-2" style="display:none;">Veuillez sélectionner des BL d'un même client.</span>
                </div>
            @endif
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width:3em;"><input type="checkbox" id="selectAllUnbilledGroups" title="Tout sélectionner"></th>
                            <th>CLIENT</th>
                            <th>BL</th>
                            <th>DESIGNATION</th>
                            <th class="text-right">MONTANT</th>
                            <th style="width:3em;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($operations as $group)
                            @php
                                $first = $group->fields->first();
                                $items = $group->fields->map(fn ($f) => ['id' => $f->id, 'designation' => $f->display_designation, 'amount' => (float) $f->amount])->values();
                            @endphp
                            <tr>
                                <td class="align-middle">
                                    <input type="checkbox" class="unbilled-group-checkbox"
                                        data-customer="{{ $group->customer }}"
                                        data-bl-name="{{ $group->bl_name }}"
                                        data-items='{{ $items->toJson() }}'>
                                </td>
                                <td class="align-middle">{{ $group->customer_name }}</td>
                                <td class="align-middle">{{ $group->bl_name }}</td>
                                <td class="align-middle">{!! $group->fields->map(fn ($f) => e($f->display_designation))->implode('<br>') !!}</td>
                                <td class="align-middle text-right">{{ number_format($group->amount, 2, ',', ' ') }}</td>
                                <td class="align-middle">
                                    <div class="dropdown">
                                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                                            <i class="fa fa-cog"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            @if ($group->fields->count() === 1)
                                                <a class="dropdown-item" href="{{ route('admin.accounting-invoices.operations.edit', $first->id) }}">Modifier</a>
                                            @endif
                                            <a class="dropdown-item text-success" href="javascript:void(0)" onclick="showBillOperationDialog({{ $group->bl }}); return false;"><i class="fa fa-check-circle"></i> Facturé</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item text-danger" href="{{ route('admin.accounting-invoices.operations.unbilled', ['rm' => implode(',', $group->ids)]) }}"
                                                onclick="return confirm('Voulez-vous vraiment supprimer {{ count($group->ids) > 1 ? 'ces '.count($group->ids).' opérations' : 'cette opération' }} ?')">
                                                <i class="fa fa-times"></i> Supprimer
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Aucune opération non facturée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('admin.accounting-invoices._bill-operations-modal')
@endsection

@push('scripts')
    <script>
        function eligibleBlsUrl(customerId) {
            return '{{ url('admin/accounting-invoices/operations/customer') }}/' + customerId + '/eligible-bls';
        }

        $(function () {
            const $createBtn = $('#createOperationBtn');
            const $selectAll = $('#selectAllUnbilledBls');

            function updateSelectionState() {
                const $checkboxes = $('.unbilled-bl-checkbox');
                const checkedCount = $checkboxes.filter(':checked').length;
                $createBtn.prop('disabled', checkedCount === 0);
                $selectAll.prop('checked', $checkboxes.length > 0 && checkedCount === $checkboxes.length);
            }

            $('#filterCustomer').on('change', function () {
                const customerId = $(this).val();
                const $wrapper = $('#unbilledBlsWrapper');
                const $body = $('#unbilledBlsBody');
                const $empty = $('#unbilledBlsEmpty');
                $body.empty();
                $wrapper.hide();
                $empty.hide();
                $selectAll.prop('checked', false);
                $createBtn.prop('disabled', true);
                if (!customerId) {
                    return;
                }
                $.getJSON(eligibleBlsUrl(customerId), function (res) {
                    const items = res.data || [];
                    if (items.length === 0) {
                        $empty.show();
                        return;
                    }
                    items.forEach(function (item) {
                        const $row = $('<tr>');
                        $row.append($('<td>').append($('<input type="checkbox" class="unbilled-bl-checkbox">').val(item.id)));
                        $row.append($('<td>').text(item.bl));
                        $row.append($('<td>').text(item.description ?? ''));
                        $body.append($row);
                    });
                    $wrapper.show();
                });
            });

            $selectAll.on('change', function () {
                $('.unbilled-bl-checkbox').prop('checked', $(this).is(':checked'));
                updateSelectionState();
            });
            $(document).on('change', '.unbilled-bl-checkbox', updateSelectionState);

            $createBtn.on('click', function () {
                const customerId = $('#filterCustomer').val();
                const ids = $('.unbilled-bl-checkbox:checked').map(function () { return $(this).val(); }).get();
                if (!customerId || ids.length === 0) {
                    return;
                }
                const params = ids.map(id => 'bl[]=' + encodeURIComponent(id)).join('&');
                window.location.href = '{{ route('admin.accounting-invoices.operations.create') }}' + '?customer=' + encodeURIComponent(customerId) + '&' + params;
            });

            // Facturation groupée : sélectionner plusieurs BL (d'un même client) et les facturer ensemble.
            const $billBtn = $('#billSelectionBtn');
            const $billWarning = $('#billSelectionWarning');
            const $selectAllGroups = $('#selectAllUnbilledGroups');

            function updateBillSelectionState() {
                const $checked = $('.unbilled-group-checkbox:checked');
                const $all = $('.unbilled-group-checkbox');
                $selectAllGroups.prop('checked', $all.length > 0 && $checked.length === $all.length);
                if ($checked.length === 0) {
                    $billBtn.prop('disabled', true);
                    $billWarning.hide();
                    return;
                }
                const customerIds = $checked.map(function () { return $(this).data('customer'); }).get();
                const distinct = [...new Set(customerIds)];
                if (distinct.length > 1) {
                    $billBtn.prop('disabled', true);
                    $billWarning.show();
                    return;
                }
                $billWarning.hide();
                $billBtn.prop('disabled', false);
            }

            $selectAllGroups.on('change', function () {
                $('.unbilled-group-checkbox').prop('checked', $(this).is(':checked'));
                updateBillSelectionState();
            });
            $(document).on('change', '.unbilled-group-checkbox', updateBillSelectionState);

            $billBtn.on('click', function () {
                const items = [];
                $('.unbilled-group-checkbox:checked').each(function () {
                    const blName = $(this).data('bl-name');
                    const groupItems = $(this).data('items') || [];
                    groupItems.forEach(it => items.push({ id: it.id, designation: it.designation, amount: it.amount, bl_name: blName }));
                });
                showBillOperationsDialog(items);
            });
        });
    </script>
@endpush
