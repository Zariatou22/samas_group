var origin = window.origin, ajaxUrl = origin + '/ajax/';
moment.locale('fr');
const unpaidBox = {};
const ivcFields = {};
const ivcFieldLabels = [];
const shipFields = {};
const shipFieldLabels = [];
let lastFieldId = 1;
const shipProducts = [];
jQuery(document).ready(function($) {
	if (jQuery.select2) {
		$.fn.select2.defaults.set("ajax--cache", false);
		$.fn.select2.defaults.reset();
	}
	//Effacer les alerts
	window.setTimeout(function() {
    $(".inner-notif").fadeTo(500, 0).slideUp(500, function(){
      $(this).remove(); 
    });
	}, 7000);
	/**
	 * Confirmation avant suppression
	 */
	$("body").on('click', '.del', function(e) {
		var c = $(this), msg = c.data("msg"), link = c.attr("href");
		if (msg !== undefined) {
			e.preventDefault();
			$.confirm({
				title: 'A votre attention!',
				type: "red",
				typeAnimated: true,
				content: msg,
				buttons: {
					OK: {
						text: "Oui",
						btnClass: "btn-blue",
						action: function (a) {
							window.location.href = link;
						}
					},
					NO: {
						text: "Non",
						btnClass: "btn-red",
						action: function (a) {}
					}
				}
			})
		}
	});
	// Liste des produits
	const prod = $('#productList')
	if (prod.html() !== undefined && jQuery().dataTable !== undefined) {
		prod.DataTable({
			ajax: '/ajax/get-products',
			dataSrc: 'data',
			columns: [
				{'data': 'name'},
				{'data': 'container_type'},
				{'data': 'service'},
				{'data': (data) => {
					return `<a href="/dashboard/products.html?id=${data.id}">Modifier</a>`;
				}},
			],
			language: {
				url: '/assets/json/datatable/fr-FR.json',
			},
		});
	}
	/**
	 * Lorsqu'on clique sur click-to-run 
	 */
	$(".click-to-run").on('click', function(e) {
		const c = $(this);
		const link = c.data('link');
		const param = c.data('params');
		let lk = `/${link}.html`;
		if (param !== undefined && param.length > 0) {
			params = param.split(',')
			for (let i = 0; i < params.length; i++) {
				const p = encodeURI(params[i]);
				if (i == 0) {
					lk += `?${p}`;
				} else {
					lk += `&${p}`;
				}
			}
		}
		window.location.href=lk;
	});
	/**
	 * Select2
	 */
	const selCar = $('#selcarLoading');
	if (selCar.length > 0) {
		const ship = $("#ship").val();
		selCar.select2({
			placeholder: "Sélectionner",
			allowClear: true,
			language: 'fr_FR',
			ajax: {
				url: ajaxUrl + 'get-box-unpaid',
				dataType: 'json',
				data: function (params) {
					return {
						q: params.term, // search term
						id: ship // Ship ID
					};
				},
				processResults: function (data) {
					const d = [];
					if (data.length > 0) {
						data.forEach(function(item) {
							unpaidBox[item.kanis_loading_id] = item;
							d.push({
								id: item.kanis_loading_id,
								text: item.full_registration
							});
						});
					}
					return {'results': d};
				},
				cache: true,
			}
		});
		// A la sélction d'un élément dans la liste
		selCar.on('select2:select', function(e) {
			const data = e.params.data;
			const id = data.id;
			const load = unpaidBox[id];
			const up = $("#unit_price").val() ?? 41000;
			$("#net_weight").val(load.net_weight);
			$("#driver_name").val(load.driver_name);
			$("#owner_name").val(load.owner_name);
			$("#car").val(load.car);
			$("#loading_date").val(moment(load.loading_date).format('DD/MM/YYYY'));
			$("#amount").attr('max', load.net_weight * up);
			$("#amount").focus();
		});
	}
	// Nouveau client
	$("#dynClientAdd").on('click', function(e) {
		$.confirm(
		{
			title: 'Nouveau client',
			type: 'blue',
			typeAnimated: true,
			content: `
			<form action="" class="formName">
			<div class="form-group">
			<label for="clientName">Nom du client</label>
			<input type="text" id="clientName" class="name form-control" required>
			</div>
			</form>`,
			buttons: {
				formSubmit: {
					text: 'Enregistrer',
					btnClass: 'btn-blue',
					action: function () {
						let name = this.$content.find('.name').val();
						if(!name){
							$.alert('Veuillez entrer un nom');
							return false;
						}
						$.post('/ajax/set-transit-client-name', {name}, function(data) {
							if (data.status === 'error') {
								$.alert('Une erreur est survenue: ');
								console.warn(data);
								return false;
							}
							if (data.error) {
								$.alert('Erreur: ' + data.ermsg);
								return false;
							}
							const names = data.data;
							if (names.length > 0) {
								const customer = $('#customer');
								customer.html('');
								let options = '<option value="">Sélectionner un client</option>';
								names.forEach(function(item) {
									let selected = '';
									if (item.name == name) {
										selected = ' selected';
									}
									options += `<option value="${item.transit_customers_id}"${selected}>${item.name}</option>`;
								});
								customer.html(options).trigger('change');
							}
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
	});
	// Nouvelle provenance
	$("#dynOriginAdd").on('click', function(e) {
		$.confirm(
		{
			title: 'Nouvelle provenance',
			type: 'blue',
			typeAnimated: true,
			content: `
			<form action="" class="formName">
			<div class="form-group">
			<label for="originName">Origine du coli</label>
			<input type="text" id="originName" class="name form-control" required>
			</div>
			</form>`,
			buttons: {
				formSubmit: {
					text: 'Enregistrer',
					btnClass: 'btn-blue',
					action: function () {
						let name = this.$content.find('.name').val();
						if(!name){
							$.alert('Veuillez entrer un nom');
							return false;
						}
						$.post('/ajax/set-transit-origin-name', {name}, function(data) {
							if (data.status === 'error') {
								$.alert('Une erreur est survenue: ');
								console.warn(data);
								return false;
							}
							if (data.error) {
								$.alert('Erreur: ' + data.ermsg);
								return false;
							}
							const names = data.data;
							if (names.length > 0) {
								const origin = $('#origin');
								origin.html('');
								let options = '<option value="">Sélectionner une provenance</option>';
								names.forEach(function(item) {
									let selected = '';
									if (item.name == name) {
										selected = ' selected';
									}
									options += `<option value="${item.transit_origins_id}"${selected}>${item.name}</option>`;
								});
								origin.html(options).trigger('change');
							}
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
	});
	// Nouveau libellé
	$("#dynLabelAdd").on('click', function(e) {
		$.confirm(
		{
			title: 'Nouveau libellé',
			type: 'blue',
			typeAnimated: true,
			content: `
			<form action="" class="formName">
			<div class="form-group">
			<label for="labelName">Libellé</label>
			<input type="text" id="labelName" class="name form-control" required>
			</div>
			<div class="form-group">
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
						$.post('/ajax/set-transit-label', {name, unit_price: unitPrice}, function(data) {
							if (data.status === 'error') {
								$.alert('Une erreur est survenue: ');
								console.warn(data);
								return false;
							}
							if (data.error) {
								$.alert('Erreur: ' + data.ermsg);
								return false;
							}
							const labels = data.data;
							if (labels.length > 0) {
								ivcFieldLabels.splice(0, ivcFieldLabels.length);
								labels.forEach(function(item) {
									ivcFieldLabels.push(item);
									ivcFields[item.transit_invoice_labels_id] = item;
								});
							}
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
	});
	// Nouveau libellé
	$("#dynShipLabelAdd").on('click', function(e) {
		$.confirm(
		{
			title: 'Nouveau libellé',
			type: 'blue',
			typeAnimated: true,
			content: `
			<form action="" class="formName">
			<div class="form-group">
			<label for="labelName">Libellé</label>
			<input type="text" id="labelName" class="name form-control" required>
			</div>
			<div class="form-group">
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
						$.post('/ajax/set-shipping-label', {name, unit_price: unitPrice}, function(data) {
							if (data.status === 'error') {
								$.alert('Une erreur est survenue: ');
								console.warn(data);
								return false;
							}
							if (data.error) {
								$.alert('Erreur: ' + data.ermsg);
								return false;
							}
							const labels = data.data;
							if (labels.length > 0) {
								shipFieldLabels.splice(0, shipFieldLabels.length);
								labels.forEach(function(item) {
									shipFieldLabels.push(item);
									shipFields[item.shipping_invoice_labels_id] = item;
								});
							}
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
	});
	// Récupération des champs dynamiques pour les factures de Transit
	const ivc = $("#addInvoice")
	if (ivc.length > 0) {
		lastFieldId = $(".fields").data('rows');
		$.get('/ajax/get-invoice-fields', function(data) {
			if (data.status === 'error') {
				console.warn(data);
				return false;
			}
			if (data.length > 0) {
				for (let i = 0; i < data.length; i++) {
					ivcFields[data[i].transit_invoice_labels_id] = data[i];
					ivcFieldLabels.push(data[i]);
				}
			}
		}, "JSON");
	}
	// Récupération des champs dynamiques pour les factures de Shipping
	const ship = $("#addShipInvoice")
	if (ship.length > 0) {
		lastFieldId = $(".fields").data('rows');
		$.get('/ajax/get-ship-invoice-fields', function(data) {
			if (data.status === 'error') {
				console.warn(data);
				return false;
			}
			if (data.length > 0) {
				for (let i = 0; i < data.length; i++) {
					shipFields[data[i].shipping_invoice_labels_id] = data[i];
					shipFieldLabels.push(data[i]);
				}
			}
		}, "JSON");
	}
	// Nouveau champ de facture du shipping
	$("#addShipNewInvoiceRow").click(function () {
		if (shipFieldLabels.length === 0) {
			return;
		}
		let options = ``;
		for (let i = 0; i < shipFieldLabels.length; i++) {
			const field = shipFieldLabels[i];
			options += `<option value="${field.shipping_invoice_labels_id}">${field.name}</option>`
		}
		let row = `<div class="row si_fields" id="field_${lastFieldId}">
			<div class="col-md-3">
				<div class="form-group">
					<label for="si_select_label_${lastFieldId}">Libellé</label>
					<select name="si_fields[${lastFieldId}]" id="si_select_label_${lastFieldId}" class="form-control custom-select" onchange="si_field_label_changed(this)">
						<option value="">Sélectionner un libellé</option>
						${options}>
					</select>
					<input type="hidden" name="si_fields_label[]" id="si_field_label_${lastFieldId}" value="">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label for="si_field_unit_price_${lastFieldId}">Prix unitaire</label>
					<input type="number" name="si_field_unit_price[]" id="si_field_unit_price_${lastFieldId}" class="form-control" value="" min="0" step="0.1" onchange="si_field_unity_changed(this)">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label for="si_field_quantity_${lastFieldId}">Quantité</label>
					<input type="number" name="si_field_quantity[]" id="si_field_quantity_${lastFieldId}" class="form-control" value="1" min="1" step="1" onchange="si_field_quantity_changed(this)">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<a href="javascript:void(0)" class="float-right" onclick="removeSiField(${lastFieldId})"><i class="fa fa-times-circle text-danger"></i></a>
					<label for="si_field_amount_${lastFieldId}">Montant</label>
					<input type="number" name="si_field_amount[]" id="si_field_amount_${lastFieldId}" class="form-control" value="" readonly>
					<input type="hidden" name="si_field_id[]" id="si_field_id_${lastFieldId}" value="">
				</div>
			</div>
		</div>`;
		$(".fields").append(row);
		lastFieldId += 1;
	});
	// Nouveau champ de facture
	$("#addNewInvoiceRow").click(function () {
		if (ivcFieldLabels.length === 0) {
			return;
		}
		let options = ``;
		for (let i = 0; i < ivcFieldLabels.length; i++) {
			const field = ivcFieldLabels[i];
			options += `<option value="${field.transit_invoice_labels_id}">${field.name}</option>`
		}
		let row = `<div class="row ivc_fields" id="field_${lastFieldId}">
			<div class="col-md-3">
				<div class="form-group">
					<label for="ivc_select_label_${lastFieldId}">Libellé</label>
					<select name="ivc_fields[${lastFieldId}]" id="ivc_select_label_${lastFieldId}" class="form-control custom-select" onchange="ivc_field_label_changed(this)">
						<option value="">Sélectionner un libellé</option>
						${options}>
					</select>
					<input type="hidden" name="ivc_fields_label[]" id="ivc_field_label_${lastFieldId}" value="">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label for="ivc_field_unit_price_${lastFieldId}">Prix unitaire</label>
					<input type="number" name="ivc_field_unit_price[]" id="ivc_field_unit_price_${lastFieldId}" class="form-control" value="" min="0" step="0.1" onchange="ivc_field_unity_changed(this)">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label for="ivc_field_quantity_${lastFieldId}">Quantité</label>
					<input type="number" name="ivc_field_quantity[]" id="ivc_field_quantity_${lastFieldId}" class="form-control" value="1" min="1" step="1" onchange="ivc_field_quantity_changed(this)">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
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
	// Lorsqu'on change une source dans l'autorisation
	const authSource = $("#addAuth");
	if (authSource.length > 0) {
		// Lorsqu'on sélectionne uns source
		$("#source").on('change', function (e) {
			const c = $(this);
			const id = parseInt(e.target.value, 10);
			const ship = parseInt($("#ship").val(), 10);
			const edit = $("#edit").val();
			if (isNaN(id) || isNaN(ship)) return;
			$.get(`/ajax/get-source-bl?ship=${ship}&source=${id}&edit=${edit}`, function (data) {
				if (data.length === 0) {
					$("#shipProduct").html('<option value="">Sélectionner un BL</option>');
					return;
				}
				let options = '<option value="">Sélectionner un BL</option>';
				for (let i of data) {
					shipProducts[i.ship_products_id] = i;
					options += `<option value="${i.ship_products_id}">${i.bl}</option>`;
				}
				$("#shipProduct").html(options);
			}, 'JSON');
		});
		// Lorsqu'on sélectionne un BL
		$("#shipProduct").on('change', function (e) {
			const id = parseInt(e.target.value, 10);
			if (isNaN(id)) return;
			const sp = shipProducts[id];
			let quantity = parseFloat(sp.quantity);
			if (sp.stock_quantity !== undefined) {
				quantity = parseFloat(sp.stock_quantity);
			}
			$("#authQuantity").attr('max', quantity);
		});
	}
});
// Lorsqu'on change un libellé de facture du shipping
function si_field_label_changed(field) {
	const id = field.value;
	if (id === undefined || id === '') {
		return;
	}
	const field_ids = field.id.split('_')
	const field_id = field_ids[field_ids.length - 1];
	const si = shipFields[id];
	let quantity = 1;
	const label = document.getElementById('si_field_label_' + field_id);
	label.value = id;
	const qte = document.getElementById('si_field_quantity_' + field_id);
	if (qte.value !== undefined && qte.value !== '') {
		quantity = parseInt(qte.value, 10);
	}
	let up = 0;
	const unit_price = document.getElementById('si_field_unit_price_' + field_id);
	if (unit_price !== null && unit_price !== undefined) {
		unit_price.value = si.unit_price;
	}
	const fid = 'si_field_amount_' + field_id;
	const amount = document.getElementById('si_field_amount_' + field_id);
	$(`#${fid}`).removeClass();
	$(`#${fid}`).addClass(`form-control si_field_amount_${si.tax}_${field_id}`);
	if (amount !== null && amount !== undefined) {
		amount.value = si.unit_price * quantity;
		compute_si_tax();
	}
}
function si_field_quantity_changed(field) {
	const field_ids = field.id.split('_')
	const field_id = field_ids[field_ids.length - 1];
	const qte = parseInt(field.value, 10);
	if (isNaN(qte) || qte <= 0) {
		field.value = 1;
		return;
	}
	const unit_price = document.getElementById('si_field_unit_price_' + field_id);
	if (unit_price !== null && unit_price !== undefined) {
		const amount = document.getElementById('si_field_amount_' + field_id);
		if (amount !== null && amount !== undefined) {
			amount.value = unit_price.value * qte;
			compute_si_tax();
		}
	}
}
function si_field_unity_changed(field) {
	const field_ids = field.id.split('_')
	const field_id = field_ids[field_ids.length - 1];
	const unit_price = parseInt(field.value, 10);
	const amount = document.getElementById('si_field_amount_' + field_id);
	if (isNaN(unit_price) || unit_price <= 0) {
		amount.value = 0;
		compute_si_tax();
		return;
	}
	const quantity = document.getElementById('si_field_quantity_' + field_id);
	if (quantity !== null && quantity !== undefined) {
		if (amount !== null && amount !== undefined) {
			amount.value = quantity.value * unit_price;
			compute_si_tax();
		}
	}
}
function compute_si_total() {
	const tax = $("#totalBaseTaxe").val();
	const taxFree = $("#baseHorsTaxe").val();
	const tva = $("#tva").val();
	if (tax === undefined || tax === '' || taxFree === undefined || taxFree === '' || tva === undefined || tva === '') {
		return;
	}
	const total = parseFloat(tax) + parseFloat(taxFree) + parseFloat(tva);
	$("#totalNet").val(total);
}
function compute_si_tax() {
	const fields = $('[class^="form-control si_field_amount_1"]');
	if (fields.length == 0) {
		return;
	}
	let total = 0;
	for (let i = 0; i < fields.length; i++) {
		const field = fields[i];
		if (field.value === '') {
			continue;
		}
		total += parseFloat(field.value);
	}
	const customer = $("#customerId").val();
	if (customer === undefined || customer === '' || customer === '2') {
		$("#totalBaseTaxe").val(total);
		compute_si_tax_free();
		compute_si_total();
		return;
	}
	$("#totalBaseTaxe").val(total);
	$("#tva").val(total * 0.18);
	compute_si_tax_free();
	compute_si_total();
}
function compute_si_tax_free() {
	let total = 0;
	const fields = $('[class^="form-control si_field_amount_0"]');
	if (fields.length == 0) {
		$("#baseHorsTaxe").val(total);
		return;
	}
	for (let i = 0; i < fields.length; i++) {
		const field = fields[i];
		if (field.value === '') {
			continue;
		}
		total += parseFloat(field.value);
	}
	$("#baseHorsTaxe").val(total);
}
function removeSiField(index) {
	const id = $("#si_field_id_" + index).val()
	const res = confirm('Êtes-vous sûr de vouloir supprimer cette ligne ?');
	if (!res) {
		return;
	}
	const field = $("#field_" + index)
	field.remove();
	compute_si_tax();
}
// Lorsqu'on change un libellé de facture du transit
function ivc_field_label_changed(field) {
	const id = field.value;
	if (id === undefined || id === '') {
		return;
	}
	const field_ids = field.id.split('_')
	const field_id = field_ids[field_ids.length - 1];
	const ivc = ivcFields[id];
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
		unit_price.value = ivc.unit_price;
	}
	const amount = document.getElementById('ivc_field_amount_' + field_id);
	if (amount !== null && amount !== undefined) {
		amount.value = ivc.unit_price * quantity;
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