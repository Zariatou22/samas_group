{{-- Boîte de dialogue "Facturer les opérations" partagée par operations-unbilled (BL unique via
     showBillOperationDialog, ou plusieurs BL d'un même client via showBillOperationsDialog). --}}
<div class="modal fade" id="billOperationsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Facturer les opérations</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Sélectionnez les désignations à inclure dans la facture :</p>
                <div id="billOperationsRows"></div>
                <div class="form-group mt-3">
                    <label for="billReference">Référence de la facture</label>
                    <input type="text" class="form-control" id="billReference" required>
                </div>
                <div id="billOperationsError" class="alert alert-danger mt-3" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="billOperationsConfirm">Confirmer</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function renderBillOperationsRows(items) {
            const $rows = $('#billOperationsRows');
            $rows.empty();
            let currentBl = null;
            items.forEach(function (item) {
                if (item.bl_name && item.bl_name !== currentBl) {
                    currentBl = item.bl_name;
                    $rows.append($('<p class="mb-1"><strong></strong></p>').find('strong').text('BL ' + currentBl).end());
                }
                const $row = $('<div class="form-check mb-2">');
                $row.append($(`<input class="form-check-input" type="checkbox" value="${item.id}" checked>`).attr('id', 'bill-op-' + item.id));
                $row.append($('<label class="form-check-label">').attr('for', 'bill-op-' + item.id)
                    .text(item.designation + ' — ' + Number(item.amount).toFixed(2)));
                $rows.append($row);
            });
        }

        function openBillOperationsModal(items, nextReference) {
            renderBillOperationsRows(items);
            $('#billReference').val(nextReference);
            $('#billOperationsError').hide();
            $('#billOperationsModal').modal('show');
            $('#billOperationsConfirm').off('click').on('click', function () {
                const ids = $('#billOperationsRows input:checked').map(function () { return this.value; }).get();
                const reference = $('#billReference').val().trim();
                const $error = $('#billOperationsError');
                if (ids.length === 0) {
                    $error.text('Veuillez sélectionner au moins une désignation.').show();
                    return;
                }
                if (!reference) {
                    $error.text('Veuillez renseigner une référence.').show();
                    return;
                }
                $.ajax({
                    url: '{{ route('admin.accounting-invoices.operations.mark-invoiced') }}',
                    method: 'POST',
                    data: { ids: ids, reference: reference, _token: '{{ csrf_token() }}' },
                    success: function (response) {
                        if (!response.success) {
                            $error.text(response.message || 'Une erreur est survenue.').show();
                            return;
                        }
                        window.location.reload();
                    },
                    error: function () {
                        $error.text('Une erreur est survenue.').show();
                    }
                });
            });
        }

        // Facturer une ou plusieurs désignations non facturées d'un même BL.
        function showBillOperationDialog(blId) {
            $.getJSON('{{ url('admin/accounting-invoices/operations/bl') }}/' + blId + '/unbilled', function (res) {
                const items = res.data || [];
                if (items.length === 0) {
                    alert('Aucune désignation non facturée trouvée pour ce BL.');
                    return;
                }
                openBillOperationsModal(items, res.next_reference);
            });
        }

        // Facturer des désignations provenant de plusieurs BL d'un même client, sous une seule référence.
        function showBillOperationsDialog(items) {
            if (!items || items.length === 0) {
                alert('Aucune désignation sélectionnée.');
                return;
            }
            $.getJSON('{{ route('admin.accounting-invoices.operations.next-reference') }}', function (res) {
                openBillOperationsModal(items, res.next_reference);
            });
        }
    </script>
@endpush
