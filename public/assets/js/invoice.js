const invoiceBls = {};
const invoiceFields = {};
const invoiceFieldLabels = [];
$(document).ready(function() {
    // Liste des mandataires : cartes cliquables (une par mandataire) plutôt qu'un tableau,
    // chacune menant à la même page "mandataire" que l'ancien bouton "Voir". Filtres
    // Mandataire / Client identiques à ceux de "Tous les B/L" (bl-filter.php).
	const ivcCustomers = $("#invoiceCustomers");
	if (ivcCustomers.length > 0) {
		function renderInvoiceCustomers(list) {
			ivcCustomers.empty();
			$("#invoiceCustomersEmpty").toggle(list.length === 0);
			let totalAmount = 0;
			let totalPaid = 0;
			list.forEach(function (data) {
				const total = parseFloat(data.total) || 0;
				const paid = parseFloat(data.paid) || 0;
				const reste = total - paid;
				totalAmount += total;
				totalPaid += paid;
				ivcCustomers.append(`
					<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-2">
						<a href="/invoice/mandataire.html?id=${data.id}" class="card h-100 no-select text-decoration-none mandataire-card" style="cursor: pointer;">
							<div class="card-body p-2">
								<h6 class="mb-1 text-truncate text-dark" style="font-size: 0.8rem;" title="${data.customer_name}">${data.customer_name}</h6>
								<div class="d-flex justify-content-between text-muted" style="font-size: 0.7rem;">
									<span>Total</span><span>${display_number(total)}</span>
								</div>
								<div class="d-flex justify-content-between text-muted" style="font-size: 0.7rem;">
									<span>Payé</span><span>${display_number(paid)}</span>
								</div>
								<div class="d-flex justify-content-between font-weight-bold ${reste > 0 ? 'text-danger' : 'text-success'}" style="font-size: 0.7rem;">
									<span>Reste</span><span>${reste == 0 ? '-' : display_number(reste)}</span>
								</div>
							</div>
						</a>
					</div>
				`);
			});
			$("#invoiceCustomersTotals").text(`${list.length} mandataire(s) — Total : ${display_number(totalAmount)} · Payé : ${display_number(totalPaid)} · Reste : ${display_number(totalAmount - totalPaid)}`);
		}
		function loadInvoiceCustomers() {
			const customer = $("#invoiceFilterCustomer").val();
			const customerCompany = $("#invoiceFilterCustomerCompany").val();
			$.ajax({
				url: `/api/v1/invoice/get-invoice-customers?customer=${customer}&customer_company=${customerCompany}`,
				dataType: 'json',
				success: function (response) {
					renderInvoiceCustomers(response.data || []);
				}
			});
		}
		loadInvoiceCustomers();
		if (jQuery().select2 !== undefined) {
			$("#invoiceFilterCustomerCompany").select2({
				theme: 'bootstrap',
				placeholder: '--Sélectionner un client--',
				allowClear: true,
				ajax: {
					url: `/api/v1/customer/search-companies-with-commands`,
					dataType: 'json',
					delay: 250,
					data: function (params) {
						return {
							q: params.term
						};
					},
					processResults: function (data, _) {
						return {
							results: data.items.map(function (item) {
								return {
									id: item.id,
									text: item.name
								};
							})
						};
					},
				},
			});
			$("#invoiceFilterCustomer").select2({
				theme: 'bootstrap',
			});
		}
		$("#filterInvoiceCustomers").on('change', function () {
			loadInvoiceCustomers();
		});
		$("#filterInvoiceCustomers").on('reset', function () {
			setTimeout(loadInvoiceCustomers);
		});
	}
	// Liste des factures d'un mandataire (par client)
	const customerIvc = $("#customerInvoices");
	if (customerIvc.html() !== undefined) {
		const customerId = customerIvc.data('customer');
		customerIvc.DataTable({
			ajax: `/api/v1/invoice/get-customer-invoices?customer=${customerId}`,
			dataSrc: 'data',
			order: [],
			columns: [
				{data: 'customer_company_name'},
				{data: (data) => {
					return `<a href="/invoice/bl-invoices.html?bl=${data.bl}">${data.bl_name}</a>`;
				}},
				{data: (data) => display_number(data.total)},
				{data: (data) => display_number(data.paid)},
				{data: (data) => {
					const reste = parseFloat(data.total) - parseFloat(data.paid);
					if (reste == 0) {
						return '-';
					}
					return display_number(reste);
				}}
			],
			"createdRow": function(row, _, _) {
                row.firstElementChild.classList.add("align-left");
            },
			processing: true,
			pageLength: 25,
			language: {
				url: '/assets/json/datatable/fr-FR.json',
			},
			"footerCallback": function () {
				const api = this.api();
				const totalAmount = api
					.column(2, {search: 'applied'})
					.data()
					.reduce( function (a, b) {
						let n = 0;
						if (b !== '-') {
							n = parseFloat(b.replace(/\s/g, '').replace(',', '.'), 10);
						}
						return parseFloat(a) + n;
					}, 0 );
				const totalPaid = api
					.column(3, {search: 'applied'})
					.data()
					.reduce( function (a, b) {
						let n = 0;
						if (b !== '-') {
							n = parseFloat(b.replace(/\s/g, '').replace(',', '.'), 10);
						}
						return parseFloat(a) + n;
					}, 0 );
				const rest = api
					.column(4, {search: 'applied'})
					.data()
					.reduce( function (a, b) {
						let n = 0;
						if (b !== '-') {
							n = parseFloat(b.replace(/\s/g, '').replace(',', '.'), 10);
						}
						return parseFloat(a) + n;
					}, 0 );
				$(api.column(2).footer()).html(totalAmount == 0 ? '-' : display_number(totalAmount));
				$(api.column(3).footer()).html(totalPaid == 0 ? '-' : display_number(totalPaid));
				$(api.column(4).footer()).html(rest == 0 ? '-' : display_number(rest));
			}
		});
	}
    // Liste des factures par BL
	const blIvc = $("#providerBlInvoices");
	if (blIvc.html() !== undefined) {
		const blId = blIvc.data('id');
		blIvc.DataTable({
			ajax: `/api/v1/invoice/get-bl-invoices?bl_id=${blId}`,
			dataSrc: 'data',
			order: [],
			columns: [
				{data: 'customer_company_name'},
				{data: 'bl_name'},
				{data: 'label_name'},
				{data: 'reference'},
				{data: (data) => display_number(data.amount)},
				{data: (data) => {
					if (data.created === null || data.created === undefined || data.created === '') {
						return '-';
					}
					return moment(data.created).format('DD/MM/YYYY');
				}},
				{data: (data) => {
					const ref = data.payment_reference;
					if (ref === undefined || ref === null || ref.length === '0') {
						return '-';
					}
					return ref;
				}},
				{data: (data) => {
					const paid_amount = data.paid_amount;
					if (paid_amount === undefined || paid_amount === null || paid_amount === 0) {
						return '-';
					}
					return display_number(paid_amount);
				}},
				{data: (data) => {
					const paid_amount = data.paid_amount;
					if (paid_amount === undefined || paid_amount === null || paid_amount === 0) {
						return display_number(data.amount);
					}
					const rest = parseFloat(data.amount) - parseFloat(data.paid_amount);
					if (rest === 0) {
						return '-';
					}
					return display_number(rest);
				}},
				{data: (data) => {
					const paid_date = data.paid_date;
					if (paid_date === undefined || paid_date === null || paid_date === '') {
						return '-';
					}
					return moment(paid_date).format('DD/MM/YYYY');
				}},
				{data: (data) => {
					return `
						<div class="dropdown">
							<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"
								aria-expanded="false">
								<i class="fas fa-cog"></i>
							</button>
							<div class="dropdown-menu">
								<a href="/invoice/edit.html?bl=${data.bl}&id=${data.id}&rt=${encodeURIComponent('invoice/bl-invoices?bl=' + data.bl)}" class="dropdown-item">Modifier</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item text-danger" href="/invoice/delete.html?id=${data.id}&rt=${encodeURIComponent('invoice/bl-invoices?bl=' + data.bl)}" onclick="return confirm('Voulez-vous vraiment supprimer ce paiement de facture?')"><i class="fas fa-times"></i>&nbsp;Supprimer</a>
							</div>
						</div>
					`;
				}}
			],
			processing: true,
			pageLength: 25,
			language: {
				url: '/assets/json/datatable/fr-FR.json',
			},
			"footerCallback": function () {
				const api = this.api();
				const total = api
					.column(3, {search: 'applied'})
					.data()
					.reduce( function (a, _) {
						return parseInt(a) + 1;
					}, 0 );
				const totalAmount = api
					.column(4, {search: 'applied'})
					.data()
					.reduce( function (a, b) {
						let n = 0;
						if (b !== '-') {
							n = parseFloat(b.replace(/\s/g, '').replace(',', '.'), 10);
						}
						return parseFloat(a) + n;
					}, 0 );
				const totalPaid = api
					.column(7, {search: 'applied'})
					.data()
					.reduce( function (a, b) {
						let n = 0;
						if (b !== '-') {
							n = parseFloat(b.replace(/\s/g, '').replace(',', '.'), 10);
						}
						return parseFloat(a) + n;
					}, 0 );
				const rest = api
					.column(8, {search: 'applied'})
					.data()
					.reduce( function (a, b) {
						let n = 0;
						if (b !== '-') {
							n = parseFloat(b.replace(/\s/g, '').replace(',', '.'), 10);
						}
						return parseFloat(a) + n;
					}, 0 );
				$(api.column(3).footer()).html(total == 0 ? '-' : display_number(total));
				$(api.column(4).footer()).html(totalAmount == 0 ? '-' : display_number(totalAmount));
				$(api.column(7).footer()).html(totalPaid == 0 ? '-' : display_number(totalPaid));
				$(api.column(8).footer()).html(rest == 0 ? '-' : display_number(rest));
			}
		});
	}
	// Charger la liste des Bl d'un client
	$(".load-customer-bl").on('change', function () {
		const id = $(this).val();
        searchBl(id);
	});
	// Lorsque le BL change dans le formulaire d'ajout ou de modification de facture, mettre à jour la liste des clients associés à ce BL
    $("#customerBl").on('select2:select', function () {
		const ivcId = $('#customerBl').val();
		if (!ivcId) {
			$("#customerCompany").val('');
			return;
		}
		const bl = invoiceBls[ivcId];
		$("#customerCompany").val(bl.customer_company);
	});
    $("#customerBl").on('select2:clear', function () {
		$("#customerCompany").val('');
	});
	// Formatter la liste des clients pour le formulaire d'ajout de facture
	if (jQuery().select2 !== undefined) {
		$("#invoiceCustomer").select2({
			theme: 'bootstrap',
			placeholder: '--Nom du client--',
			allowClear: true,
			clear: true,
		});
	}
	// Lorsqu'on change le mode de la facture en payé, mettre à jour le champ payé avec le même montant que le champ montant de la facture
	$("#invoicePaid").on('change', function() {
		const selected = !isNaN(parseInt($(this).val())) ? parseInt($(this).val()) : 0;
		if (selected === 1) {
			$("#invoiceAmountPaid").val($("#invoiceAmount").val());
		} else {
			$("#invoiceAmountPaid").val('');
		}
	});
	// Gestion des champs récurrents
	const ivcForm = $("#addInvoice");
	if (ivcForm.html() !== undefined) {
        lastFieldId = $(".fields").data('rows');
		$.get('/api/v1/invoice/get-labels', function(data) {
			if (data.items.length === 0) {
				return;
			}
			data.items.forEach((item, _) => {
				invoiceFields[item.id] = item;
				invoiceFieldLabels.push(item);
			});
		}, "JSON");
		ivcForm.on('submit', function () {
			wait('Traitement en cours...', 'Enregistrement de la facture');
		});
    }
	// Ajout dynamique de ligne des champs du bon
    $("#addNewVoucherRow").click(function () {
		if (invoiceFieldLabels.length === 0) {
			return;
		}
		let options = ``;
		for (let i = 0; i < invoiceFieldLabels.length; i++) {
			const field = invoiceFieldLabels[i];
			options += `<option value="${field.id}">${field.name}</option>`;
		}
		let row = `<div class="row ivc_fields" id="field_${lastFieldId}">
			<div class="col-md-3">
				<div class="form-group mb-3">
					<label for="ivc_select_label_${lastFieldId}">Libellé</label>
					<select name="ivc_fields[${lastFieldId}]" id="ivc_select_label_${lastFieldId}" class="form-control custom-select" onchange="ivc_field_label_changed(this)">
						<option value="">Sélectionner un libellé</option>
						${options}>
					</select>
					<input type="hidden" name="ivc_fields_label[]" id="ivc_field_label_${lastFieldId}" value="">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group mb-3">
					<label for="ivc_field_unit_price_${lastFieldId}">Prix unitaire</label>
					<input type="number" name="ivc_field_unit_price[]" id="ivc_field_unit_price_${lastFieldId}" class="form-control" value="" min="0" step="0.1" onchange="ivc_field_unity_changed(this)">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group mb-3">
					<label for="ivc_field_quantity_${lastFieldId}">Quantité</label>
					<input type="number" name="ivc_field_quantity[]" id="ivc_field_quantity_${lastFieldId}" class="form-control" value="1" min="1" step="1" onchange="ivc_field_quantity_changed(this)">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group mb-3">
					<a href="javascript:void(0)" class="float-right" onclick="removeIvcField(${lastFieldId})"><i class="fa fa-times-circle text-danger"></i></a>
					<label for="ivc_field_amount_${lastFieldId}">Montant</label>
					<input type="number" name="ivc_field_amount[]" id="ivc_field_amount_${lastFieldId}" class="form-control" value="" readonly>
					<input type="hidden" name="ivc_field_id[]" id="ivc_field_id_${lastFieldId}" value="">
				</div>
			</div>
		</div>`;
		$(".fields").append(row);
		lastFieldId += 1;
	});
	// Créer un nouveau libellé dynamiquement
	$("#dynInvoiceLabelAdd").on('click', function (e) {
		e.preventDefault();
		const userId = $(this).data('user');
		$.confirm({
			title: 'Nouveau libellé',
			type: 'blue',
			typeAnimated: true,
			content: `
			<form action="" class="formName">
			<div class="form-group mb-3">
			<label for="labelName">Libellé</label>
			<input type="text" id="labelName" class="name form-control" required>
			</div>
			<div class="form-group mb-3">
			<label for="unitPrice">Prix unitaire</label>
			<input type="text" id="unitPrice" class="unitPrice form-control" min="0" step="0.1" value="0" required>
			</div>
			</form>`,
			buttons: {
				formSubmit: {
					text: 'Enregistrer',
					btnClass: 'btn-blue',
					action: function () {
						let name = this.$content.find('.name').val();
						let unitPrice = this.$content.find('.unitPrice').val();
						if(!name){
							$.alert('Veuillez saisir un libellé');
							return false;
						}
						$.post('/api/v1/invoice/set-invoice-label', {name, unit_price: unitPrice, user: userId}, function(data) {
							if (data.error) {
								$.alert('Erreur: ' + data.ermsg);
								return false;
							}
							const label = data.data;
							invoiceFieldLabels.push(label);
							invoiceFields[label.id] = label;
						}, "JSON");
					}
				},
				cancel: {
					text: 'Annuler',
					btnClass: 'btn-red',
					action: function () {}
				},
			},
			onContentReady: function () {
				var jc = this;
				this.$content.find('form').on('submit', function (e) {
					e.preventDefault();
					jc.$$formSubmit.trigger('click');
				});
			}
		});
	})
});
function searchBl(id) {
    // Select2 for BL selection
    const customerBl = $("#customerBl");
    if (customerBl.html() !== undefined && jQuery().select2 !== undefined) {
        if (id === null || id === undefined) {
            return;
        }
        customerBl.select2({
            theme: 'bootstrap',
			placeholder: "Sélectionner",
			allowClear: true,
			language: 'fr_FR',
			ajax: {
				url: `/ajax/list-customer-bls?id=${id}`,
				dataType: 'json',
				data: function (params) {
					return {
						q: params.term, // search term
						customer: id // Ship ID
					};
				},
				processResults: function (data) {
					const d = [];
					if (data.data.length > 0) {
						data.data.forEach(function(item) {
							invoiceBls[item.id] = item;
							d.push({
								id: item.id,
								text: item.bl
							});
						});
					}
					return {'results': d};
				},
				cache: true,
			}
		});
    }
}
// Lorsqu'on change un libellé de facture du transit
function ivc_field_label_changed(field) {
	const id = field.value;
	if (id === undefined || id === '') {
		return;
	}
	const field_ids = field.id.split('_')
	const field_id = field_ids[field_ids.length - 1];
	const vc = invoiceFields[id];
	let quantity = 1;
	const label = document.getElementById('ivc_field_label_' + field_id);
	label.value = id;
	const qte = document.getElementById('ivc_field_quantity_' + field_id);
	if (qte.value !== undefined && qte.value !== '') {
		quantity = parseInt(qte.value, 10);
	}
	let up = 0;
	const unit_price = document.getElementById('ivc_field_unit_price_' + field_id);
	if (unit_price !== null && unit_price !== undefined) {
		unit_price.value = vc.unit_price;
	}
	const amount = document.getElementById('ivc_field_amount_' + field_id);
	if (amount !== null && amount !== undefined) {
		amount.value = vc.unit_price * quantity;
		compute_ivc_total();
	}
}
function ivc_field_quantity_changed(field) {
	const field_ids = field.id.split('_')
	const field_id = field_ids[field_ids.length - 1];
	const qte = parseInt(field.value, 10);
	if (isNaN(qte) || qte <= 0) {
		field.value = 1;
		return;
	}
	const unit_price = document.getElementById('ivc_field_unit_price_' + field_id);
	if (unit_price !== null && unit_price !== undefined) {
		const amount = document.getElementById('ivc_field_amount_' + field_id);
		if (amount !== null && amount !== undefined) {
			amount.value = unit_price.value * qte;
			compute_ivc_total();
		}
	}
}
function ivc_field_unity_changed(field) {
	const field_ids = field.id.split('_')
	const field_id = field_ids[field_ids.length - 1];
	const unit_price = parseInt(field.value, 10);
	const amount = document.getElementById('ivc_field_amount_' + field_id);
	if (isNaN(unit_price) || unit_price <= 0) {
		amount.value = 0;
		compute_ivc_total();
		return;
	}
	const quantity = document.getElementById('ivc_field_quantity_' + field_id);
	if (quantity !== null && quantity !== undefined) {
		if (amount !== null && amount !== undefined) {
			amount.value = quantity.value * unit_price;
			compute_ivc_total();
		}
	}
}
function compute_ivc_total() {
	const fields = $('[id^=ivc_field_amount]');
	if (fields.length == 0) {
		return;
	}
	let total = 0;
	for (let i = 0; i < fields.length; i++) {
		const field = fields[i];
		if (field.value === '') {
			continue;
		}
		total += parseInt(field.value, 10);
	}
	$("#amount").val(total);
}
function removeIvcField(index) {
	const id = $("#ivc_field_id_" + index).val()
	const res = confirm('Êtes-vous sûr de vouloir supprimer cette ligne ?');
	if (!res) {
		return;
	}
	const field = $("#field_" + index)
	field.remove();
	compute_ivc_total();
}