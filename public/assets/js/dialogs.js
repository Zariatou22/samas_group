// Facturer une ou plusieurs désignations non facturées d'un même BL, sous une référence commune
function showBillOperationDialog(blId) {
    $.getJSON('/ajax/list-bl-unbilled-operations.html', { bl: blId }, function (res) {
        var items = res.data || [];
        if (items.length === 0) {
            $.alert('Aucune désignation non facturée trouvée pour ce BL.');
            return;
        }
        var rowsHtml = items.map(function (item) {
            return `
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="ids[]" value="${item.id}" id="bill-op-${item.id}" checked>
                    <label class="form-check-label" for="bill-op-${item.id}">
                        ${item.designation} &mdash; ${display_number(item.amount)}
                    </label>
                </div>
            `;
        }).join('');
        $.confirm({
            title: 'Facturer les opérations',
            type: 'blue',
            typeAnimated: true,
            content: `
                <form id="bill-operations-form">
                    <p>Sélectionnez les désignations à inclure dans la facture :</p>
                    ${rowsHtml}
                    <div class="form-group mt-3">
                        <label for="billReference">Référence de la facture</label>
                        <input type="text" class="form-control" id="billReference" name="reference" value="${res.next_reference}" required>
                    </div>
                </form>
            `,
            buttons: {
                cancel: {
                    text: 'Annuler',
                    btnClass: 'btn-default',
                    action: function () {
                        // Do nothing, just close the dialog
                    }
                },
                confirm: {
                    text: 'Confirmer',
                    btnClass: 'btn-primary',
                    action: function () {
                        var form = this.$content.find('#bill-operations-form');
                        var ids = form.find('input[name="ids[]"]:checked').map(function () { return $(this).val(); }).get();
                        var reference = form.find('#billReference').val().trim();
                        if (ids.length === 0) {
                            $.alert('Veuillez sélectionner au moins une désignation.');
                            return false;
                        }
                        if (!reference) {
                            $.alert('Veuillez renseigner une référence.');
                            return false;
                        }
                        var wait = $.dialog("Veuillez patienter...");
                        $.ajax({
                            url: '/api/mark-operations-invoiced.html',
                            method: 'POST',
                            data: { 'ids[]': ids, reference: reference },
                            success: function (response) {
                                wait.close();
                                if (!response.success) {
                                    $.alert(response.message || 'Une erreur est survenue.');
                                    return;
                                }
                                window.location.reload();
                            },
                            error: function () {
                                wait.close();
                            }
                        });
                    }
                }
            }
        });
    });
}
// Facturer des désignations non facturées provenant de plusieurs BL d'un même client, regroupées
// sous une seule référence de facture (ex : facture couvrant les BL I, II, III d'un client).
function showBillOperationsDialog(items) {
    if (!items || items.length === 0) {
        $.alert('Aucune désignation sélectionnée.');
        return;
    }
    $.getJSON('/ajax/next-invoice-reference.html', {}, function (res) {
        var byBl = {};
        var order = [];
        items.forEach(function (item) {
            if (!byBl[item.bl_name]) {
                byBl[item.bl_name] = [];
                order.push(item.bl_name);
            }
            byBl[item.bl_name].push(item);
        });
        var rowsHtml = order.map(function (blName) {
            var rows = byBl[blName].map(function (item) {
                return `
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="ids[]" value="${item.id}" id="bill-op-${item.id}" checked>
                        <label class="form-check-label" for="bill-op-${item.id}">
                            ${item.designation} &mdash; ${display_number(item.amount)}
                        </label>
                    </div>
                `;
            }).join('');
            return `<p class="mb-1"><strong>BL ${blName}</strong></p>${rows}`;
        }).join('');
        $.confirm({
            title: 'Facturer les opérations',
            type: 'blue',
            typeAnimated: true,
            content: `
                <form id="bill-operations-form">
                    <p>Sélectionnez les désignations à inclure dans la facture :</p>
                    ${rowsHtml}
                    <div class="form-group mt-3">
                        <label for="billReference">Référence de la facture</label>
                        <input type="text" class="form-control" id="billReference" name="reference" value="${res.next_reference}" required>
                    </div>
                </form>
            `,
            buttons: {
                cancel: {
                    text: 'Annuler',
                    btnClass: 'btn-default',
                    action: function () {
                        // Do nothing, just close the dialog
                    }
                },
                confirm: {
                    text: 'Confirmer',
                    btnClass: 'btn-primary',
                    action: function () {
                        var form = this.$content.find('#bill-operations-form');
                        var ids = form.find('input[name="ids[]"]:checked').map(function () { return $(this).val(); }).get();
                        var reference = form.find('#billReference').val().trim();
                        if (ids.length === 0) {
                            $.alert('Veuillez sélectionner au moins une désignation.');
                            return false;
                        }
                        if (!reference) {
                            $.alert('Veuillez renseigner une référence.');
                            return false;
                        }
                        var wait = $.dialog("Veuillez patienter...");
                        $.ajax({
                            url: '/api/mark-operations-invoiced.html',
                            method: 'POST',
                            data: { 'ids[]': ids, reference: reference },
                            success: function (response) {
                                wait.close();
                                if (!response.success) {
                                    $.alert(response.message || 'Une erreur est survenue.');
                                    return;
                                }
                                window.location.reload();
                            },
                            error: function () {
                                wait.close();
                            }
                        });
                    }
                }
            }
        });
    });
}
// Add exchange BL dialog
function showExchangeBLDialog(blId, userId, date='') {
    $.confirm({
        title: 'Échanger le BL',
        type: 'blue',
        typeAnimated: true,
        content: `
            <form id="exchange-bl-form" action="/api/set-exchange-date.html" method="POST">
                <input type="hidden" name="bl_id" value="${blId}">
                <input type="hidden" name="user_id" value="${userId}">
                <div class="form-group">
                    <label for="dateReceived">Date de réception</label>
                    <input type="date" class="form-control" id="dateReceived" name="date_received" value="${date !== '' ? date : new Date().toISOString().split('T')[0]}" required>
                </div>
            </form>
        `,
        buttons: {
            cancel: {
                text: 'Annuler',
                btnClass: 'btn-default',
                action: function () {
                    // Do nothing, just close the dialog
                }
            },
            confirm: {
                text: 'Confirmer',
                btnClass: 'btn-primary',
                action: function () {
                    const form = this.$content.find('#exchange-bl-form');
                    const dateReceived = form.find('input[name="date_received"]').val();
                    if (!dateReceived) {
                        $.alert('Veuillez sélectionner une date de réception.');
                        return false; // Prevent closing the dialog
                    }
                    const wait = $.dialog("Veuillez patienter...");
                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: form.serialize(),
                        success: function (response) {
                            wait.close();
                            if (!response.success) {
                                $.alert(response.message || 'Une erreur est survenue lors de l\'échange du BL.');
                                return;
                            }
                            window.location.reload();
                        },
                        error: function () {
                            wait.close();
                        }
                    });
                }
            }
        }
    });
}
// Add bad BL dialog
function showBadBLDialog(blId, userId, date='', validDate='') {
    $.confirm({
        title: 'BAD du BL',
        type: 'blue',
        typeAnimated: true,
        content: `
            <form id="bad-bl-form" action="/api/set-bad-date.html" method="POST">
                <input type="hidden" name="bl_id" value="${blId}">
                <input type="hidden" name="user_id" value="${userId}">
                <div class="form-group">
                    <label for="validDate">Date de validité</label>
                    <input type="date" class="form-control" id="validDate" name="date_valid" value="${validDate !== '' ? validDate : new Date().toISOString().split('T')[0]}" required>
                </div>
                <div class="form-group">
                    <label for="dateReceived">Date de réception</label>
                    <input type="date" class="form-control" id="dateReceived" name="date_received" value="${date !== '' ? date : new Date().toISOString().split('T')[0]}" required>
                </div>
            </form>
        `,
        buttons: {
            cancel: {
                text: 'Annuler',
                btnClass: 'btn-default',
                action: function () {
                    // Do nothing, just close the dialog
                }
            },
            confirm: {
                text: 'Confirmer',
                btnClass: 'btn-primary',
                action: function () {
                    const form = this.$content.find('#bad-bl-form');
                    const dateReceived = form.find('input[name="date_received"]').val();
                    if (!dateReceived) {
                        $.alert('Veuillez sélectionner une date de réception.');
                        return false; // Prevent closing the dialog
                    }
                    const wait = $.dialog("Veuillez patienter...");
                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: form.serialize(),
                        success: function (response) {
                            wait.close();
                            if (!response.success) {
                                $.alert(response.message || 'Une erreur est survenue lors de l\'enregistrement avec le BL.');
                                return;
                            }
                            window.location.reload();
                        },
                        error: function () {
                            wait.close();
                        }
                    });
                }
            }
        }
    });
}
// Add observation BL dialog
function showObservationBLDialog(blId, userId, data='') {
    $.confirm({
        title: 'Observations',
        type: 'blue',
        typeAnimated: true,
        columnClass: 'col-md-6 col-md-offset-3',
        content: `
            <form id="observation-bl-form" action="/api/v1/bl/set-observation.html" method="POST">
                <input type="hidden" name="bl_id" value="${blId}">
                <input type="hidden" name="user_id" value="${userId}">
                <div class="form-group">
                    <label for="observation">Observations</label>
                    <textarea class="form-control" id="observation" name="observation" rows="4" placeholder="Veuillez saisir une observation ...">${data ?? ''}</textarea>
                </div>
            </form>
        `,
        buttons: {
            cancel: {
                text: 'Annuler',
                btnClass: 'btn-default',
                action: function () {
                    // Do nothing, just close the dialog
                }
            },
            confirm: {
                text: 'Enregistrer',
                btnClass: 'btn-primary',
                action: function () {
                    const form = this.$content.find('#observation-bl-form');
                    const observations = form.find('textarea[name="observation"]').val();
                    if (!observations) {
                        $.alert('Veuillez saisir des observations.');
                        return false; // Prevent closing the dialog
                    }
                    const wait = $.dialog("Veuillez patienter...");
                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: form.serialize(),
                        success: function (response) {
                            wait.close();
                            if (!response.success) {
                                $.alert(response.message || 'Une erreur est survenue lors de l\'enregistrement de l\'observation du BL.');
                                return;
                            }
                            window.location.reload();
                        },
                        error: function () {
                            wait.close();
                        }
                    });
                }
            }
        }
    });
}
// Clôturer un BL (Opérations en cours) : demande la date de déchargement avant de clôturer
function showCompleteOperationDialog(blId) {
    $.confirm({
        title: 'Clôturer le BL',
        type: 'red',
        typeAnimated: true,
        content: `
            <form id="complete-operation-form" action="/api/set-complete-date.html" method="POST">
                <input type="hidden" name="bl_id" value="${blId}">
                <div class="form-group">
                    <label for="dischargeDate">Date de déchargement</label>
                    <input type="date" class="form-control" id="dischargeDate" name="discharge_date" value="${new Date().toISOString().split('T')[0]}" required>
                </div>
            </form>
        `,
        buttons: {
            cancel: {
                text: 'Annuler',
                btnClass: 'btn-default',
                action: function () {
                    // Do nothing, just close the dialog
                }
            },
            confirm: {
                text: 'Clôturer',
                btnClass: 'btn-danger',
                action: function () {
                    const form = this.$content.find('#complete-operation-form');
                    const dischargeDate = form.find('input[name="discharge_date"]').val();
                    if (!dischargeDate) {
                        $.alert('Veuillez sélectionner une date de déchargement.');
                        return false; // Prevent closing the dialog
                    }
                    const wait = $.dialog("Veuillez patienter...");
                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: form.serialize(),
                        success: function (response) {
                            wait.close();
                            if (!response.success) {
                                $.alert(response.message || 'Une erreur est survenue lors de la clôture du BL.');
                                return;
                            }
                            window.location.href = '/bl/index/completed';
                        },
                        error: function () {
                            wait.close();
                        }
                    });
                }
            }
        }
    });
}
// Invoice details dialog
function invoiceDetails(ivc) {
    const invoice = JSON.parse(decodeURIComponent(ivc));
    $.dialog({
		title: invoice.label_name,
		animated: true,
		typeAnimation: 'zoom',
		theme: 'modern',
		backgroundDismiss: true,
		content: `<table class="table table-bordered">
			<tr>
				<th class="align-middle text-left">BL</th>
				<td class="align-middle text-left">${invoice.bl_name}</td>
			</tr>
			<tr>
				<th class="align-middle text-left">Client</th>
				<td class="align-middle text-left">${invoice.customer_name}</td>
			</tr>
			<tr>
				<th class="align-middle text-left">Référence la facture</th>
				<td class="align-middle text-left">${invoice.reference}</td>
			</tr>
            <tr>
				<th class="align-middle text-left">Date de réception</th>
				<td class="align-middle text-left">${moment(invoice.created).format('DD/MM/YYYY')}</td>
			</tr>
			<tr>
				<th class="align-middle text-left">Montant</th>
				<td class="align-middle text-left">${display_number(invoice.amount)}F</td>
			</tr>
            <tr>
				<th class="align-middle text-left">Etat de paiement</th>
				<td class="align-middle text-left ${invoice.paid == 1 ? 'text-success' : 'text-danger'}">
					${invoice.paid == 1 ? 'Payée' : 'Impayée'}
				</td>
			</tr>
            <tr>
				<th class="align-middle text-left">Date du paiement</th>
				<td class="align-middle text-left">${invoice.paid == 1 ? moment(invoice.payment_date).format('DD/MM/YYYY') : '-'}</td>
			</tr>
            <tr>
				<th class="align-middle text-left">Référence du paiement</th>
				<td class="align-middle text-left">${invoice.payment_reference ? invoice.payment_reference : '-'}</td>
			</tr>
		</table>`,
		columnClass: 'medium',
	});
}
// Informations d'un camion
function showCarDetails(car) {
    if (!car || car === '' || car === 'null' || car === 'undefined' || car.length === 0) {
        $.alert('Aucune information disponible pour ce camion.');
        return;
    }
    const carData = JSON.parse(decodeURIComponent(car));
    $.dialog({
		title: carData.full_registration,
		animated: true,
		typeAnimation: 'zoom',
		theme: 'modern',
		backgroundDismiss: true,
		content: `<table class="table table-bordered">
			<tr>
				<th class="align-middle text-left">Chauffeur</th>
				<td class="align-middle text-left">${carData.driver_name}</td>
			</tr>
			<tr>
				<th class="align-middle text-left">Contact du chauffeur</th>
				<td class="align-middle text-left">${carData.driver_contact}</td>
			</tr>
			<tr>
				<th class="align-middle text-left">Transporteur</th>
				<td class="align-middle text-left">${carData.owner_name}</td>
			</tr>
            <tr>
				<th class="align-middle text-left">Contact du transporteur</th>
				<td class="align-middle text-left">${carData.owner_contact}</td>
			</tr>
		</table>`,
		columnClass: 'medium',
	});
}
// Informations d'un camion
function showCustomerDetails(data) {
    if (!data || data === '' || data === 'null' || data === 'undefined' || data.length === 0) {
        $.alert('Aucune information disponible pour ce camion.');
        return;
    }
    const customerData = JSON.parse(decodeURIComponent(data));
    $.dialog({
		title: 'Informations du clients',
		animated: true,
		typeAnimation: 'zoom',
		theme: 'modern',
		backgroundDismiss: true,
		content: `<table class="table table-bordered">
			<tr>
				<th class="align-middle text-left">Client</th>
				<td class="align-middle text-left">${customerData.customerCompanyName}</td>
			</tr>
			<tr>
				<th class="align-middle text-left">Contact du client</th>
				<td class="align-middle text-left">${customerData.customerCompanyContact}</td>
			</tr>
			<tr>
				<th class="align-middle text-left">Mandataire</th>
				<td class="align-middle text-left">${customerData.customerName}</td>
			</tr>
            <tr>
				<th class="align-middle text-left">Contact du mandataire</th>
				<td class="align-middle text-left">${customerData.customerContact}</td>
			</tr>
			<tr>
				<th class="align-middle text-left">Transitaire</th>
				<td class="align-middle text-left">${customerData.agentName}</td>
			</tr>
            <tr>
				<th class="align-middle text-left">Contact du transitaire</th>
				<td class="align-middle text-left">${customerData.agentContact}</td>
			</tr>
		</table>`,
		columnClass: 'medium',
	});
}