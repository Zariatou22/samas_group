const listBlFilterCustomerCompanies = {};
const listBlFilterCustomers = {};
$(document).ready(function () {
    // Liste des BL
	const waiting = $('#waitingBl');
	if (waiting.length > 0) {
		if (jQuery().DataTable !== undefined) {
            const userId = waiting.data('user');
			const dt = waiting.DataTable({
				colReorder: true,
				ajax: {
					url: `/api/get-waiting`,
					dataSrc: function (json) {
						const data = json.data || [];
						const today = moment().startOf('day');
						// bl_name contient les groupes "TYPE X QUANTITE" par conteneur (voir Bls.php) :
						// on additionne les quantités pour obtenir le nombre de conteneurs, pas de BL.
						const containerCount = function (d) {
							if (!d.bl_name || !Array.isArray(d.bl_name)) {
								return 0;
							}
							return d.bl_name.reduce(function (sum, part) {
								const seg = String(part).split('X');
								const qty = seg.length > 1 ? parseInt(seg[seg.length - 1].trim(), 10) : NaN;
								return sum + (isNaN(qty) ? 0 : qty);
							}, 0);
						};
						const totalContainers = data.reduce(function (sum, d) { return sum + containerCount(d); }, 0);
						$('#waitingTotalCount').text(totalContainers);
						const warningContainers = data.filter(function (d) {
							if (!d.eta_date || d.exchange_date) {
								return false;
							}
							const days = moment(d.eta_date).startOf('day').diff(today, 'days');
							return days >= 0 && days <= 3;
						}).reduce(function (sum, d) { return sum + containerCount(d); }, 0);
						$('#waitingWarningCount').text(warningContainers);
						return data;
					}
				},
				order: [],
                columnDefs: [
                    { 'visible': false, 'targets': [0], orderData: [0] }
                ],
                "createdRow": function(row, data, dataIndex) {
                    if (data.eta_date === undefined || data.eta_date === null) {
                        return;
                    }
                    if (data.exchange_date !== undefined && data.exchange_date !== null) {
                        return;
                    }
                    const eta = moment(data.eta_date).startOf('day');
                    const today = moment().startOf('day');
                    const days = eta.diff(today, 'days');
                    if (days < 0) {
                        $(row).addClass('status-failure');
                    } else if (days <= 3) {
                        $(row).addClass('status-pending');
                    } else {
                        $(row).addClass('status-success');
                    }
                },
				columns: [
                    {data: (data) => {
                        return moment(data.eta_date).format('YYYYMMDD');
                    }},
					{data: (data) => {
						if (data.customer_company_name === null || data.customer_company_name === undefined) {
                            return '-';
                        }
                        const customerData = {
                            customerName: data.customer_name,
                            customerContact: data.customer_contact,
                            customerCompanyName: data.customer_company_name,
                            customerCompanyContact: data.customer_company_contact,
                            agentName: data.agent_name,
                            agentContact: data.agent_contact,
                        };
                        return `<a href="javascript:void(0)" class="text-navy" onclick="showCustomerDetails('${encodeURIComponent(JSON.stringify(customerData))}')">${data.customer_company_name}</a>`;
					}},
					{data: (data) => {
						return `<a href="/bl/containers.html?id=${data.id}&rt=${encodeURIComponent('bl/index/waiting')}" class="text-primary">${data.bl}</a>`;
					}},
					{data: (data) => {
						if (data.bl_name === undefined || data.bl_name === null) {
							return '-';
						}
						return data.bl_name.join('<br>');
					}},
					{data: (data) => {
                        if (data.created === undefined || data.created === null) {
                            return '-';
                        }
                        return moment(data.created).format('DD/MM/YYYY');
                    }},
					{data: (data) => {
                        if (data.eta_date === undefined || data.eta_date === null) {
                            return '-';
                        }
                        return moment(data.eta_date).format('DD/MM/YYYY');
                    }},
					{data: 'company_name'},
					{data: 'description'},
					{data: 'type_operation'},
					{data: (data) => {
                        if (data.exchange_date === undefined || data.exchange_date === null) {
                            return '-';
                        }
                        return moment(data.exchange_date).format('DD/MM/YYYY');
                    }},
                    {data: (data) => {
                        if (data.bad_date === undefined || data.bad_date === null) {
                            return '-';
                        }
                        return moment(data.bad_date).format('DD/MM/YYYY');
                    }},
                    {data: (data) => {
                        if (data.valid_date === undefined || data.valid_date === null) {
                            return '-';
                        }
                        return moment(data.valid_date).format('DD/MM/YYYY');
                    }},
                    {data: (data) => {
                        if (data.observation === undefined || data.observation === null) {
                            return '-';
                        }
                        return splitText(data.observation, 40).replace(/\n/g, '<br>');
                    }},
                    {data: (data) => {
                        const date_bad = data.bad_date ? moment(data.bad_date).format('YYYY-MM-DD') : '';
                        const date_exchange = data.exchange_date ? moment(data.exchange_date).format('YYYY-MM-DD') : '';
                        return `
                        <div class="dropdown">
                            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-cog"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="/bl/edit.html?id=${data.id}" class="dropdown-item">Modifier</a>
                                <a href="/bl/infos.html?id=${data.id}" class="dropdown-item">Informations</a>
                                <a href="/bl/print-info.html?id=${data.id}" class="dropdown-item" target="_blank"><i class="fas fa-print"></i>&nbsp;Imprimer</a>
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0);" class="dropdown-item" onclick="showExchangeBLDialog(${data.id}, ${userId}, '${date_exchange}'); return false;">ECHANGE BL</a>
                                <a href="javascript:void(0);" class="dropdown-item" onclick="showBadBLDialog(${data.id}, ${userId}, '${date_bad}', '${data.valid_date ?? ''}'); return false;">BAD</a>
                                <a href="javascript:void(0);" class="dropdown-item" onclick="showObservationBLDialog(${data.id}, ${userId}, ${data.observation ? `'${data.observation.replace(/'/g, "\\'")}'` : ''}); return false;">OBSERVATION</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="/bl/delete-bl.html?id=${data.id}" onclick="return confirm('Voulez-vous vraiment supprimer ce BL?')"><i class="fas fa-times"></i>&nbsp;Supprimer</a>
                            </div>
                        </div>
                    `;
                    }},
				],
                processing: true,
                pageLength: 25,
				language: {
					url: '/assets/json/datatable/fr-FR.json',
				},
				"footerCallback": function () {
					const api = this.api();
					const total = api
						.column(2, {search: 'applied'})
						.data()
						.reduce( function (a) {
							return parseFloat(a) + 1;
						}, 0 );
					let totalContainers = api.column(3, {search: 'applied'})
						.data()
						.reduce( function (a, b) {
                            let n = 0;
                            if (b.split('X')[1] !== undefined) {
                                n = parseInt(b.split('X')[1].trim(), 10);
                            }
							return parseFloat(a) + n;
						}, 0 );
                    $(api.column(2).footer()).html(total);
					$(api.column(3).footer()).html(totalContainers);
				}
            });
            // Cartes cliquables : filtrent le tableau sur le même critère que leur compteur
            // (tous / ETA <= 3 jours non dépassée). Le fond du tableau devient orange quand
            // le filtre "ETA <= 3 jours" est actif, pour bien voir quel filtre est appliqué.
            let waitingCardFilter = 'all';
            $.fn.dataTable.ext.search.push(function (settings, searchData, index, rowData) {
                if (settings.nTable.id !== 'waitingBl' || waitingCardFilter === 'all') {
                    return true;
                }
                if (!rowData || !rowData.eta_date || rowData.exchange_date) {
                    return false;
                }
                const days = moment(rowData.eta_date).startOf('day').diff(moment().startOf('day'), 'days');
                return days >= 0 && days <= 3;
            });
            function setWaitingCardFilter(filter) {
                waitingCardFilter = filter;
                $('#waitingCardTotal, #waitingCardWarning').removeClass('card-filter-active');
                $('#waitingBl').toggleClass('table-filter-orange', filter === 'warning');
                $('#waitingBl').toggleClass('table-filter-blue', filter === 'all');
                if (filter === 'warning') {
                    $('#waitingCardWarning').addClass('card-filter-active');
                } else {
                    $('#waitingCardTotal').addClass('card-filter-active');
                }
                dt.draw();
            }
            $('#waitingCardTotal').on('click', function () { setWaitingCardFilter('all'); });
            $('#waitingCardWarning').on('click', function () { setWaitingCardFilter('warning'); });
            setWaitingCardFilter('all');
			// En changer les options d'affichage des containers
			$("#filterBls").on('change', function () {
				const customer = $("#blCustomer").val();
				const company = $("#company").val();
                const startDate = $("#startDate").val();
                const endDate = $("#endDate").val();
                const customerCompany = $("#blFilterCustomerCompanies").val();
                setWaitingCardFilter('all');
				dt.ajax.url(`/api/get-waiting?customer=${customer}&company=${company}&start_date=${startDate}&end_date=${endDate}&customer_company=${customerCompany}`)
				dt.ajax.reload();
			});
			$("#filterBls").on('reset', function () {
                setWaitingCardFilter('all');
				dt.ajax.url(`/api/get-waiting`)
				dt.ajax.reload();
			});
            // exporter les données
            $("#exportWaiting").on('click', function () {
                const customer = $("#blCustomer").val();
                const company = $("#company").val();
                const startDate = $("#startDate").val();
                const endDate = $("#endDate").val();
                const customerCompany = $("#blFilterCustomerCompanies").val();
                window.location.href = `/bl/export-waiting?customer=${customer}&company=${company}&start_date=${startDate}&end_date=${endDate}&customer_company=${customerCompany}`;
            });
		}
	}
    // Liste des BL arrivés sans opération
	const arrived = $('#arrivedBl');
	if (arrived.length > 0) {
		if (jQuery().DataTable !== undefined) {
            const userId = arrived.data('user');
			const dt = arrived.DataTable({
				colReorder: true,
				ajax: `/api/get-arrived`,
				dataSrc: 'data',
				order: [],
                columnDefs: [
                    { 'visible': false, 'targets': [0], orderData: [0] }
                ],
                "createdRow": function(row, data, dataIndex) {
                    if (data.eta_date === undefined || data.eta_date === null) {
                        return;
                    }
                    if (data.exchange_date !== undefined && data.exchange_date !== null) {
                        return;
                    }   
                    const eta = moment(data.eta_date);
                    const today = moment().startOf('day');
                    if (eta.isBefore(today)) {
                        $(row).addClass('status-failure');
                    } else if (eta.isSame(today, 'day')) {
                        $(row).addClass('status-pending');
                    } else {
                        $(row).addClass('status-success');
                    }
                },
				columns: [
                    {data: (data) => {
                        return moment(data.eta_date).format('YYYYMMDD');
                    }},
					{data: (data) => {
						if (data.customer_company_name === null || data.customer_company_name === undefined) {
                            return '-';
                        }
                        const customerData = {
                            customerName: data.customer_name,
                            customerContact: data.customer_contact,
                            customerCompanyName: data.customer_company_name,
                            customerCompanyContact: data.customer_company_contact,
                            agentName: data.agent_name,
                            agentContact: data.agent_contact,
                        };
                        return `<a href="javascript:void(0)" class="text-navy" onclick="showCustomerDetails('${encodeURIComponent(JSON.stringify(customerData))}')">${data.customer_company_name}</a>`;
					}},
					{data: (data) => {
						return `<a href="/bl/containers.html?id=${data.id}&rt=${encodeURIComponent('admin/index/waiting')}" class="text-primary">${data.bl}</a>`;
					}},
					{data: (data) => {
						if (data.bl_name === undefined || data.bl_name === null) {
							return '-';
						}
						return data.bl_name.join('<br>');
					}},
					{data: (data) => {
                        if (data.eta_date === undefined || data.eta_date === null) {
                            return '-';
                        }
                        return moment(data.eta_date).format('DD/MM/YYYY');
                    }},
					{data: 'company_name'},
					{data: 'description'},
					{data: 'type_operation'},
					{data: (data) => {
                        if (data.exchange_date === undefined || data.exchange_date === null) {
                            return '-';
                        }
                        return moment(data.exchange_date).format('DD/MM/YYYY');
                    }},
                    {data: (data) => {
                        if (data.bad_date === undefined || data.bad_date === null) {
                            return '-';
                        }
                        return moment(data.bad_date).format('DD/MM/YYYY');
                    }},
                    {data: (data) => {
                        if (data.transfert_date === undefined || data.transfert_date === null) {
                            return '-';
                        }
                        return moment(data.transfert_date).format('DD/MM/YYYY');
                    }},
                    {data: (data) => {
                        if (data.observation === undefined || data.observation === null) {
                            return '-';
                        }
                        return splitText(data.observation, 40).replace(/\n/g, '<br>');
                    }},
                    {data: (data) => {
                        const date_bad = data.bad_date ? moment(data.bad_date).format('YYYY-MM-DD') : '';
                        const date_exchange = data.exchange_date ? moment(data.exchange_date).format('YYYY-MM-DD') : '';
                        return `
                        <div class="dropdown">
                            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-cog"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="/bl/edit.html?id=${data.id}" class="dropdown-item">Modifier</a>
                                <a href="/bl/infos.html?id=${data.id}" class="dropdown-item">Informations</a>
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0);" class="dropdown-item" onclick="showExchangeBLDialog(${data.id}, ${userId}, '${date_exchange}'); return false;">ECHANGE BL</a>
                                <a href="javascript:void(0);" class="dropdown-item" onclick="showBadBLDialog(${data.id}, ${userId}, '${date_bad}', '${data.valid_date ?? ''}'); return false;">BAD</a>
                                <a href="/transfert/terminal.html?bl_id=${data.id}" class="dropdown-item">Date de transfert</a>
                                <a href="javascript:void(0);" class="dropdown-item" onclick="showObservationBLDialog(${data.id}, ${userId}, ${data.observation ? `'${data.observation.replace(/'/g, "\\'")}'` : ''}); return false;">OBSERVATION</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="/bl/delete-bl.html?id=${data.id}" onclick="return confirm('Voulez-vous vraiment supprimer ce BL?')"><i class="fas fa-times"></i>&nbsp;Supprimer</a>
                            </div>
                        </div>
                    `;
                    }},
				],
                processing: true,
                pageLength: 25,
				language: {
					url: '/assets/json/datatable/fr-FR.json',
				},
				"footerCallback": function () {
					const api = this.api();
					const total = api
						.column(2, {search: 'applied'})
						.data()
						.reduce( function (a) {
							return parseFloat(a) + 1;
						}, 0 );
					let totalContainers = api.column(3, {search: 'applied'})
						.data()
						.reduce( function (a, b) {
                            let n = 0;
                            if (b.split('X')[1] !== undefined) {
                                n = parseInt(b.split('X')[1].trim(), 10);
                            }
							return parseFloat(a) + n;
						}, 0 );
                    $(api.column(2).footer()).html(total);
					$(api.column(3).footer()).html(totalContainers);
				}
            });
			// En changer les options d'affichage des containers
			$("#filterBls").on('change', function () {
				const customer = $("#blCustomer").val();
				const company = $("#company").val();
                const startDate = $("#startDate").val();
                const endDate = $("#endDate").val();
                const customerCompany = $("#blFilterCustomerCompanies").val();
				dt.ajax.url(`/api/get-arrived?customer=${customer}&company=${company}&start_date=${startDate}&end_date=${endDate}&customer_company=${customerCompany}`)
				dt.ajax.reload();
			});
			$("#filterBls").on('reset', function () {
				dt.ajax.url(`/api/get-arrived`)
				dt.ajax.reload();
			});
            // exporter les données
            $("#exportArrived").on('click', function () {
                const customer = $("#blCustomer").val();
                const company = $("#company").val();
                const startDate = $("#startDate").val();
                const endDate = $("#endDate").val();
                const customerCompany = $("#blFilterCustomerCompanies").val();
                window.location.href = `/bl/export-arrived?customer=${customer}&company=${company}&start_date=${startDate}&end_date=${endDate}&customer_company=${customerCompany}`;
            });
		}
		if (jQuery().select2 !== undefined) {
			$(".select2").select2();
		}
	}
    // Liste des traitement en cours
    const ongoing = $("#ongoingBl");
    if (ongoing.html() !== undefined) {
        const containerTypes = {};
        const userId = arrived.data('user');
        const dt = ongoing.DataTable({
            colReorder: true,
            ajax: `/api/get-ongoing`,
            dataSrc: 'data',
            order: [],
            columnDefs: [
                { 'visible': false, 'targets': [0], orderData: [0] }
            ],
            columns: [
                {data: (data) => {
                    return moment(data.eta_date).format('YYYYMMDD');
                }},
                {data: (data) => {
                    if (data.customer_company_name === null || data.customer_company_name === undefined) {
                            return '-';
                    }
                    const customerData = {
                        customerName: data.customer_name,
                        customerContact: data.customer_contact,
                        customerCompanyName: data.customer_company_name,
                        customerCompanyContact: data.customer_company_contact,
                        agentName: data.agent_name,
                        agentContact: data.agent_contact,
                    };
                    return `<a href="javascript:void(0)" class="text-navy" onclick="showCustomerDetails('${encodeURIComponent(JSON.stringify(customerData))}')">${data.customer_company_name}</a>`;
                }},
                {data: (data) => {
                    return `<a href="/bl/containers.html?id=${data.id}&rt=${encodeURIComponent('bl/index/ongoing')}" class="text-primary">${data.bl}</a>`;
                }},
                {data: (data) => {
                    if (data.exchange_date === undefined || data.exchange_date === null) {
                        return '-';
                    }
                    return moment(data.exchange_date).format('DD/MM/YYYY');
                }},
                {data: (data) => {
                    if (data.bad_date === undefined || data.bad_date === null) {
                        return '-';
                    }
                    return moment(data.bad_date).format('DD/MM/YYYY');
                }},
                {data: (data) => {
                    if (data.valid_date === undefined || data.valid_date === null) {
                        return '-';
                    }
                    return moment(data.valid_date).format('DD/MM/YYYY');
                }},
                {data: (data) => {
                    if (data.transfert_date === undefined || data.transfert_date === null) {
                        return '-';
                    }
                    return moment(data.transfert_date).format('DD/MM/YYYY');
                }},
                {data: (data) => {
                    if (data.invoices === undefined || data.invoices === null) {
                        return '-';
                    }
                    if (!data.invoices.hasOwnProperty('FACTURE TERMINALE')) {
                        return '-';
                    }
                    const invoice = data.invoices['FACTURE TERMINALE'];
                    return `<a href="javascript:void(0);" class="text-primary" onclick="invoiceDetails('${encodeURIComponent(JSON.stringify(invoice))}'); return false;">${moment(data.invoices['FACTURE TERMINALE'].created).format('DD/MM/YYYY')}</a>`;
                }},
                {data: (data) => {
                    if (data.invoices === undefined || data.invoices === null) {
                        return '-';
                    }
                    if (!data.invoices.hasOwnProperty('DFU')) {
                        return '-';
                    }
                    const invoice = data.invoices['DFU'];
                    return `<a href="javascript:void(0);" class="text-primary" onclick="invoiceDetails('${encodeURIComponent(JSON.stringify(invoice))}'); return false;">${moment(data.invoices['DFU'].created).format('DD/MM/YYYY')}</a>`;
                }},
                {data: (data) => {
                    if (data.eta_date === undefined || data.eta_date === null) {
                        return '-';
                    }
                    const eta = moment(data.eta_date);
                    const today = moment().startOf('day');
                    if (today.isBefore(eta)) {
                        return '-';
                    }
                    if (data.type_operation === 'DEPOTAGE') {
                        if (data.unpot_state === undefined || data.unpot_state === null || data.unpoted === 0) {
                            return 'En attente de dépotage';
                        }
                        if ((data.unpotable - data.unpoted) == 0) {
                            return 'Dépotage terminé';
                        }
                        return `Dépotage en cours (${data.unpoted}/${data.unpotable})`;
                    }
                    if (data.load_rest === undefined || data.load_rest === null || data.loaded === 0) {
                        return 'En attente de chargement';
                    }
                    if ((data.loadable - data.loaded) == 0) {
                        return 'Chargement terminé';
                    }
                    return `Chargement en cours (${data.loaded}/${data.loadable})`;
                }},
                {data: (data) => {
                    if (data.observation === undefined || data.observation === null) {
                        return '-';
                    }
                    return splitText(data.observation, 40).replace(/\n/g, '<br>');
                }},
                {data: (data) => {
                    const date_bad = data.bad_date ? moment(data.bad_date).format('YYYY-MM-DD') : '';
                    const date_exchange = data.exchange_date ? moment(data.exchange_date).format('YYYY-MM-DD') : '';
                    return `
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-cog"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="/bl/edit.html?id=${data.id}" class="dropdown-item">Modifier</a>
                            <a href="/bl/restore-waiting?id=${data.id}" class="dropdown-item">Restaurer en attente</a>
                            <a href="/bl/infos.html?id=${data.id}" class="dropdown-item">Informations</a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item" onclick="showExchangeBLDialog(${data.id}, ${userId}, '${date_exchange}'); return false;">ECHANGE BL</a>
                            <a href="javascript:void(0);" class="dropdown-item" onclick="showBadBLDialog(${data.id}, ${userId}, '${date_bad}', '${data.valid_date ?? ''}'); return false;">BAD</a>
                            <a href="/transfert/terminal.html?bl_id=${data.id}" class="dropdown-item">Transfert vers terminal</a>
                            ${data.type_operation === 'DEPOTAGE' ? `<a href="/bl/unpot.html?bl=${data.id}" class="dropdown-item">DÉPOTAGE</a>` : ''}
                            <a href="javascript:void(0);" class="dropdown-item" onclick="showObservationBLDialog(${data.id}, ${userId}, ${data.observation ? `'${data.observation.replace(/'/g, "\\'")}'` : ''}); return false;">OBSERVATION</a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item text-danger" onclick="showCompleteOperationDialog(${data.id}); return false;"><i class="fas fa-times"></i>&nbsp;Clôturer</a>
                        </div>
                    </div>
                `;
                }},
            ],
            processing: true,
            pageLength: 25,
            language: {
                url: '/assets/json/datatable/fr-FR.json',
            },
            "footerCallback": function () {
                const api = this.api();
                const total = api
                    .column(2, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        return parseFloat(a) + 1;
                    }, 0 );
                api.column(3, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        const nb = b.split('X').length;
                        if (nb > 1) {
                            n = parseInt(b.split('X')[1].trim(), 10);
                            const type = b.split('X')[0].trim();
                            if (containerTypes[type] === undefined) {
                                containerTypes[type] = 0;
                            } else {
                                containerTypes[type] += n;
                            }
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                $(api.column(2).footer()).html(total);
            }
        });
        // En changer les options d'affichage des containers
        $("#filterBls").on('change', function () {
            const customer = $("#blCustomer").val();
            const company = $("#company").val();
            const startDate = $("#startDate").val();
            const endDate = $("#endDate").val();
            const customerCompany = $("#blFilterCustomerCompanies").val();
            dt.ajax.url(`/api/get-ongoing?customer=${customer}&company=${company}&start_date=${startDate}&end_date=${endDate}&customer_company=${customerCompany}`)
            dt.ajax.reload();
        });
        $("#filterBls").on('reset', function () {
            dt.ajax.url(`/api/get-ongoing`)
            dt.ajax.reload();
        });
        // exporter les données
        $("#exportOngoing").on('click', function () {
            const customer = $("#blCustomer").val();
            const company = $("#company").val();
            const startDate = $("#startDate").val();
            const endDate = $("#endDate").val();
            const customerCompany = $("#blFilterCustomerCompanies").val();
            window.location.href = `/bl/export-ongoing?customer=${customer}&company=${company}&start_date=${startDate}&end_date=${endDate}&customer_company=${customerCompany}`;
        });
    }
    // Liste des traitement en cours
    const complete = $("#completeBl");
    if (complete.html() !== undefined) {
        const containerTypes = {};
        const userId = complete.data('user');
        const dt = complete.DataTable({
            colReorder: true,
            ajax: `/api/get-complete`,
            dataSrc: 'data',
            order: [],
            columnDefs: [
                { 'visible': false, 'targets': [0], orderData: [0] }
            ],
            columns: [
                {data: (data) => {
                    return moment(data.eta_date).format('YYYYMMDD');
                }},
                {data: (data) => {
                    if (data.customer_company_name === null || data.customer_company_name === undefined) {
                        return '-';
                    }
                    const customerData = {
                        customerName: data.customer_name,
                        customerContact: data.customer_contact,
                        customerCompanyName: data.customer_company_name,
                        customerCompanyContact: data.customer_company_contact,
                        agentName: data.agent_name,
                        agentContact: data.agent_contact,
                    };
                    return `<a href="javascript:void(0)" class="text-navy" onclick="showCustomerDetails('${encodeURIComponent(JSON.stringify(customerData))}')">${data.customer_company_name}</a>`;
                }},
                {data: (data) => {
                    return `<a href="/bl/containers.html?id=${data.id}&rt=${encodeURIComponent('bl/index/completed')}" class="text-primary">${data.bl}</a>`;
                }},
                {data: (data) => {
                    if (data.exchange_date === undefined || data.exchange_date === null) {
                        return '-';
                    }
                    return moment(data.exchange_date).format('DD/MM/YYYY');
                }},
                {data: (data) => {
                    if (data.bad_date === undefined || data.bad_date === null) {
                        return '-';
                    }
                    return moment(data.bad_date).format('DD/MM/YYYY');
                }},
                {data: (data) => {
                    if (data.bad_date === undefined || data.bad_date === null) {
                        return '-';
                    }
                    return moment(data.bad_date).format('DD/MM/YYYY');
                }},
                {data: (data) => {
                    if (data.transfert_date === undefined || data.transfert_date === null) {
                        return '-';
                    }
                    return moment(data.transfert_date).format('DD/MM/YYYY');
                }},
                {data: (data) => {
                    if (data.invoices === undefined || data.invoices === null) {
                        return '-';
                    }
                    if (!data.invoices.hasOwnProperty('FACTURE TERMINALE')) {
                        return '-';
                    }
                    const invoice = data.invoices['FACTURE TERMINALE'];
                    return `<a href="javascript:void(0);" class="text-primary" onclick="invoiceDetails('${encodeURIComponent(JSON.stringify(invoice))}'); return false;">${moment(data.invoices['FACTURE TERMINALE'].created).format('DD/MM/YYYY')}</a>`;
                }},
                {data: (data) => {
                    if (data.invoices === undefined || data.invoices === null) {
                        return '-';
                    }
                    if (!data.invoices.hasOwnProperty('DFU')) {
                        return '-';
                    }
                    const invoice = data.invoices['DFU'];
                    return `<a href="javascript:void(0);" class="text-primary" onclick="invoiceDetails('${encodeURIComponent(JSON.stringify(invoice))}'); return false;">${moment(data.invoices['DFU'].created).format('DD/MM/YYYY')}</a>`;
                }},
                {data: (data) => {
                    if (data.eta_date === undefined || data.eta_date === null) {
                        return '-';
                    }
                    const eta = moment(data.eta_date);
                    const today = moment().startOf('day');
                    if (today.isBefore(eta)) {
                        return '-';
                    }
                    if (data.type_operation === 'DEPOTAGE') {
                        if (data.unpot_state === undefined || data.unpot_state === null || data.unpoted === 0) {
                            return 'En attente de dépotage';
                        }
                        if ((data.unpotable - data.unpoted) == 0) {
                            return 'Dépotage terminé';
                        }
                        return `Dépotage en cours (${data.unpoted}/${data.unpotable})`;
                    }
                    if (data.load_rest === undefined || data.load_rest === null || data.loaded === 0) {
                        return 'En attente de chargement';
                    }
                    if ((data.loadable - data.loaded) == 0) {
                        return 'Chargement terminé';
                    }
                    return `Chargement en cours (${data.loaded}/${data.loadable})`;
                }},
                {data: (data) => {
                    if (data.discharge_date === undefined || data.discharge_date === null) {
                        return '-';
                    }
                    return moment(data.discharge_date).format('DD/MM/YYYY');
                }},
                {data: (data) => {
                    if (data.observation === undefined || data.observation === null || data.observation.length === 0) {
                        return '-';
                    }
                    return splitText(data.observation, 40).replace(/\n/g, '<br>');
                }},
                {data: (data) => {
                    return `
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-cog"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="/bl/restore-started?id=${data.id}" class="dropdown-item">Restaurer l'opération</a>
                            <a href="/bl/infos.html?id=${data.id}" class="dropdown-item">Informations</a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item" onclick="showObservationBLDialog(${data.id}, ${userId}, ${data.observation ? `'${data.observation.replace(/'/g, "\\'")}'` : ''}); return false;">OBSERVATION</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="/bl/complete-operation?id=${data.id}" onclick="return confirm('Voulez-vous vraiment supprimer ce BL?')"><i class="fas fa-times"></i>&nbsp;Clôturer</a>
                        </div>
                    </div>
                `;
                }},
            ],
            processing: true,
            pageLength: 25,
            language: {
                url: '/assets/json/datatable/fr-FR.json',
            },
            "footerCallback": function () {
                const api = this.api();
                const total = api
                    .column(2, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        return parseFloat(a) + 1;
                    }, 0 );
                api.column(3, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        const nb = b.split('X').length;
                        if (nb > 1) {
                            n = parseInt(b.split('X')[1].trim(), 10);
                            const type = b.split('X')[0].trim();
                            if (containerTypes[type] === undefined) {
                                containerTypes[type] = 0;
                            } else {
                                containerTypes[type] += n;
                            }
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                $(api.column(2).footer()).html(total);
            }
        });
        // En changer les options d'affichage des containers
        $("#filterBls").on('change', function () {
            const customer = $("#blCustomer").val();
            const company = $("#company").val();
            const startDate = $("#startDate").val();
            const endDate = $("#endDate").val();
            const customerCompany = $("#blFilterCustomerCompanies").val();
            dt.ajax.url(`/api/get-complete?customer=${customer}&company=${company}&start_date=${startDate}&end_date=${endDate}&customer_company=${customerCompany}`)
            dt.ajax.reload();
        });
        $("#filterBls").on('reset', function () {
            dt.ajax.url(`/api/get-complete`)
            dt.ajax.reload();
        });
        // exporter les données
        $("#exportCompleted").on('click', function () {
            const customer = $("#blCustomer").val();
            const company = $("#company").val();
            const startDate = $("#startDate").val();
            const endDate = $("#endDate").val();
            const customerCompany = $("#blFilterCustomerCompanies").val();
            window.location.href = `/bl/export-completed?customer=${customer}&company=${company}&start_date=${startDate}&end_date=${endDate}&customer_company=${customerCompany}`;
        });
    }
    // Liste des traitement en cours
    const all = $("#allBl");
    if (all.html() !== undefined) {
        const containerTypes = {};
        const userId = arrived.data('user');
        const dt = all.DataTable({
            colReorder: true,
            ajax: `/api/v1/bl/list-bls`,
            dataSrc: 'data',
            order: [],
            columnDefs: [
                { 'visible': false, 'targets': [0] },
                { 'visible': false, 'targets': [7] }
            ],
            "createdRow": function(row, data, dataIndex) {
                if (data.eta_date === undefined || data.eta_date === null) {
                    return;
                }
                if (data.exchange_date !== undefined && data.exchange_date !== null) {
                    return;
                }   
                const eta = moment(data.eta_date);
                const today = moment().startOf('day');
                if (eta.isBefore(today)) {
                    $(row).addClass('status-failure');
                } else if (eta.isSame(today, 'day')) {
                    $(row).addClass('status-pending');
                } else {
                    $(row).addClass('status-success');
                }
            },
            columns: [
                {data: (data) => {
                    return moment(data.eta_date).format('YYYYMMDD');
                }},
                {data: (data) => {
                    if (data.customer_company_name === null || data.customer_company_name === undefined) {
                        return '-';
                    }
                    const customerData = {
                        customerName: data.customer_name,
                        customerContact: data.customer_contact,
                        customerCompanyName: data.customer_company_name,
                        customerCompanyContact: data.customer_company_contact,
                        agentName: data.agent_name,
                        agentContact: data.agent_contact,
                    };
                    return `<a href="javascript:void(0)" class="text-navy" onclick="showCustomerDetails('${encodeURIComponent(JSON.stringify(customerData))}')">${data.customer_company_name}</a>`;
                }},
                {data: (data) => {
                    return `<a href="/bl/containers.html?id=${data.id}&rt=${encodeURIComponent('bl/index/ongoing')}" class="text-primary">${data.bl}</a>`;
                }},
                {data: (data) => {
                    if (data.bl_name === undefined || data.bl_name === null) {
                        return '-';
                    }
                    return data.bl_name.join('<br>');
                }},
                {data: (data) => {
                    if (data.description === undefined || data.description === null || data.description === '') {
                        return '-';
                    }
                    return data.description;
                }},
                {data: (data) => {
                    if (data.eta_date === undefined || data.eta_date === null) {
                        return '-';
                    }
                    return moment(data.eta_date).format('DD/MM/YYYY');
                }},
                {data: (data) => {
                    if (data.exchange_date === undefined || data.exchange_date === null) {
                        return '-';
                    }
                    return moment(data.exchange_date).format('DD/MM/YYYY');
                }},
                {data: (data) => {
                    if (data.bad_date === undefined || data.bad_date === null) {
                        return '-';
                    }
                    return moment(data.bad_date).format('DD/MM/YYYY');
                }},
                {data: (data) => {
                    if (data.valid_date === undefined || data.valid_date === null) {
                        return '-';
                    }
                    return moment(data.valid_date).format('DD/MM/YYYY');
                }},
                {data: (data) => {
                    if (data.transfert_date === undefined || data.transfert_date === null) {
                        return '-';
                    }
                    return moment(data.transfert_date).format('DD/MM/YYYY');
                }},
                {data: (data) => {
                    if (data.invoices === undefined || data.invoices === null) {
                        return '-';
                    }
                    if (!data.invoices.hasOwnProperty('FACTURE TERMINALE')) {
                        return '-';
                    }
                    const invoice = data.invoices['FACTURE TERMINALE'];
                    return `<a href="javascript:void(0);" class="text-primary" onclick="invoiceDetails('${encodeURIComponent(JSON.stringify(invoice))}'); return false;">${moment(data.invoices['FACTURE TERMINALE'].paid_date).format('DD/MM/YYYY')}</a>`;
                }},
                {data: (data) => {
                    if (data.invoices === undefined || data.invoices === null) {
                        return '-';
                    }
                    if (!data.invoices.hasOwnProperty('DFU')) {
                        return '-';
                    }
                    const invoice = data.invoices['DFU'];
                    return `<a href="javascript:void(0);" class="text-primary" onclick="invoiceDetails('${encodeURIComponent(JSON.stringify(invoice))}'); return false;">${moment(data.invoices['DFU'].paid_date).format('DD/MM/YYYY')}</a>`;
                }},
                {data: (data) => {
                    if (data.eta_date === undefined || data.eta_date === null) {
                        return '-';
                    }
                    const eta = moment(data.eta_date);
                    const today = moment().startOf('day');
                    if (today.isBefore(eta)) {
                        return '-';
                    }
                    if (data.type_operation === 'DEPOTAGE') {
                        if (data.unpot_state === undefined || data.unpot_state === null || data.unpoted === 0) {
                            return 'En attente de dépotage';
                        }
                        if ((data.unpotable - data.unpoted) == 0) {
                            return 'Dépotage terminé';
                        }
                        return `Dépotage en cours (${data.unpoted}/${data.unpotable})`;
                    }
                    if (data.load_rest === undefined || data.load_rest === null || data.loaded === 0) {
                        return 'En attente de chargement';
                    }
                    if ((data.loadable - data.loaded) == 0) {
                        return 'Chargement terminé';
                    }
                    return `Chargement en cours (${data.loaded}/${data.loadable})`;
                }},
                {data: (data) => {
                    if (data.observation === undefined || data.observation === null) {
                        return '-';
                    }
                    return data.observation;
                }},
                {data: (data) => {
                    const date_bad = data.bad_date ? moment(data.bad_date).format('YYYY-MM-DD') : '';
                    const date_exchange = data.exchange_date ? moment(data.exchange_date).format('YYYY-MM-DD') : '';
                    return `
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-cog"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="/bl/edit.html?id=${data.id}" class="dropdown-item">Modifier</a>
                            <a href="/bl/restore-waiting?id=${data.id}" class="dropdown-item">Restaurer en attente</a>
                            <a href="/bl/infos.html?id=${data.id}" class="dropdown-item">Informations</a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item" onclick="showExchangeBLDialog(${data.id}, ${userId}, '${date_exchange}'); return false;">ECHANGE BL</a>
                            <a href="javascript:void(0);" class="dropdown-item" onclick="showBadBLDialog(${data.id}, ${userId}, '${date_bad}', '${data.valid_date ?? ''}'); return false;">BAD</a>
                            <a href="/transfert/terminal.html?bl_id=${data.id}" class="dropdown-item">Transfert vers terminal</a>
                            ${data.type_operation === 'DEPOTAGE' ? `<a href="/bl/unpot.html?bl=${data.id}" class="dropdown-item">DÉPOTAGE</a>` : ''}
                            <a href="javascript:void(0);" class="dropdown-item" onclick="showObservationBLDialog(${data.id}, ${userId}, ${data.observation ? `'${data.observation.replace(/'/g, "\\'")}'` : ''}); return false;">OBSERVATION</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="/bl/complete-operation?id=${data.id}"" onclick="return confirm('Voulez-vous vraiment clôturer ce BL?')"><i class="fas fa-times"></i>&nbsp;Clôturer</a>
                        </div>
                    </div>
                `;
                }},
            ],
            processing: true,
            pageLength: 25,
            language: {
                url: '/assets/json/datatable/fr-FR.json',
            },
            "footerCallback": function () {
                const api = this.api();
                const total = api
                    .column(2, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        return parseFloat(a) + 1;
                    }, 0 );
                const totalContainers = api.column(3, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        if (!b || b === '-') {
                            return a;
                        }
                        let n = 0;
                        b.split('<br>').forEach(function (part) {
                            const seg = part.split('X');
                            if (seg.length > 1) {
                                const qty = parseInt(seg[1].trim(), 10);
                                if (! isNaN(qty)) {
                                    const type = seg[0].trim();
                                    containerTypes[type] = (containerTypes[type] || 0) + qty;
                                    n += qty;
                                }
                            }
                        });
                        return parseFloat(a) + n;
                    }, 0 );
                $(api.column(2).footer()).html(total);
                $(api.column(3).footer()).html(totalContainers);
            }
        });
        // Récapitulatif des conteneurs enregistrés mois par mois (depuis le début de l'année, ou la
        // date de début choisie dans le filtre, jusqu'à aujourd'hui ou la date de fin choisie),
        // sous forme de graphique à barres (un mois par barre, y compris les mois à 0)
        function loadDailyContainers() {
            const customer = $("#blCustomer").val();
            const company = $("#company").val();
            const startDate = $("#startDate").val();
            const endDate = $("#endDate").val();
            const customerCompany = $("#blFilterCustomerCompanies").val();
            $.get(`/api/v1/bl/monthly-containers?customer=${customer}&company=${company}&start_date=${startDate}&end_date=${endDate}&customer_company=${customerCompany}`, function (res) {
                const counts = {};
                (res.data || []).forEach(function (d) {
                    counts[d.month] = parseInt(d.total, 10);
                });
                const start = startDate ? moment(startDate).startOf('month') : moment().startOf('year');
                const end = endDate ? moment(endDate).startOf('month') : moment().startOf('month');
                const barData = [];
                for (const d = start.clone(); d.isSameOrBefore(end, 'month'); d.add(1, 'month')) {
                    const n = counts[d.format('YYYY-MM')] || 0;
                    barData.push({label: d.format('MMM'), fullLabel: d.format('MMMM YYYY'), data: n});
                }
                const total = barData.reduce(function (s, d) { return s + d.data; }, 0);
                const $chartWrap = $("#dailyContainersChartWrap");
                const $chart = $("#dailyContainersChart");
                if (barData.length === 0) {
                    $chartWrap.hide();
                    $("#dailyContainersEmpty").show();
                    return;
                }
                $("#dailyContainersEmpty").hide();
                $chartWrap.show();
                $("#dailyContainersTotalNumber").text(total);
                $chart.empty();
                $chartWrap.find('.bar-value-label').remove();
                const plot = $.plot($chart, [{
                    data: barData.map(function (d, i) { return [i, d.data]; }),
                    color: '#4C6EF5',
                    bars: {
                        show: true,
                        barWidth: 0.6,
                        align: 'center',
                        lineWidth: 0,
                        fillColor: {colors: [{opacity: 0.85}, {opacity: 0.85}]}
                    }
                }], {
                    xaxis: {
                        ticks: barData.map(function (d, i) { return [i, d.label]; }),
                        tickLength: 0,
                        font: {size: 11, color: '#495057'}
                    },
                    yaxis: {
                        min: 0,
                        tickDecimals: 0,
                        font: {size: 11}
                    },
                    grid: {
                        hoverable: true,
                        clickable: true,
                        borderWidth: 1,
                        borderColor: '#eee',
                        margin: {top: 22}
                    },
                    legend: {
                        show: false
                    },
                    tooltip: true,
                    tooltipOpts: {
                        content: function (label, x, y, item) {
                            const d = item ? barData[item.dataIndex] : null;
                            return (d ? d.fullLabel : label) + ': ' + y + ' conteneur(s)';
                        },
                        defaultTheme: false
                    }
                });
                // Affiche le nombre au-dessus de chaque barre (y compris les mois à 0)
                barData.forEach(function (d, i) {
                    const o = plot.pointOffset({x: i, y: d.data});
                    $('<div class="bar-value-label"></div>')
                        .css({
                            position: 'absolute',
                            left: (o.left - 15) + 'px',
                            top: (o.top - 18) + 'px',
                            width: '30px',
                            textAlign: 'center',
                            fontSize: '11px',
                            fontWeight: 'bold',
                            color: '#495057',
                            pointerEvents: 'none'
                        })
                        .text(d.data)
                        .appendTo($chartWrap);
                });
            });
        }
        loadDailyContainers();
        // En changer les options d'affichage des containers
        $("#filterBls").on('change', function () {
            const customer = $("#blCustomer").val();
            const company = $("#company").val();
            const startDate = $("#startDate").val();
            const endDate = $("#endDate").val();
            const customerCompany = $("#blFilterCustomerCompanies").val();
            dt.ajax.url(`/api/v1/bl/list-bls?customer=${customer}&company=${company}&start_date=${startDate}&end_date=${endDate}&customer_company=${customerCompany}`)
            dt.ajax.reload();
            loadDailyContainers();
        });
        $("#filterBls").on('reset', function () {
            dt.ajax.url(`/api/v1/bl/list-bls`)
            dt.ajax.reload();
            setTimeout(loadDailyContainers);
        });
        // exporter les données
        $("#exportAll").on('click', function () {
            const customer = $("#blCustomer").val();
            const company = $("#company").val();
            const startDate = $("#startDate").val();
            const endDate = $("#endDate").val();
            const customerCompany = $("#blFilterCustomerCompanies").val();
            window.location.href = `/bl/export-all?customer=${customer}&company=${company}&start_date=${startDate}&end_date=${endDate}&customer_company=${customerCompany}`;
        });
    }
    // Recherche des compagnies de clients
    if (jQuery().select2 !== undefined) {
        $("#blFilterCustomerCompanies").select2({
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
                            listBlFilterCustomerCompanies[item.id] = item;
                            return {
                                id: item.id,
                                text: item.name
                            };
                        })
                    };
                },
            },
        });
        // Customer
        $("#blCustomer").select2({
            theme: 'bootstrap',
        });
    }
    // Dépotage
    const unpot = $("#unpotTable");
    if (unpot.html() !== undefined) {
        unpot.DataTable({
            ajax: `/api/get-unpot?bl=${unpot.data('id')}`,
            dataSrc: 'data',
            order: [],
            columnDefs: [
                { 'visible': false, 'targets': [0], orderData: [0] }
            ],
            columns: [
                {data: (data) => {
                    return moment(data.date_unpot).format('YYYYMMDD');
                }},
                {data: (data) => {
                    return data.customer_name;
                }},
                {data: 'ship'},
                {data: 'bl_name'},
                {data: (data) => {
                    return moment(data.eta).format('DD/MM/YYYY');
                }},
                {data: 'container_name'},
                {data: 'numero'},
                {data: (data) => {
                    return moment(data.date_unpot).format('DD/MM/YYYY');
                }},
                {data: (data) => {
                    return `<a href="/bl/unpot-add.html?bl=${data.bl}&id=${data.id}">Modifier</a><br><a href="/bl/unpot-delete.html?bl=${data.bl}&id=${data.id}" onclick="return confirm('Voulez-vous vraiment supprimer ce dépotage?')" class="text-danger">Supprimer</a>`;
                }},
            ],
            processing: true,
            language: {
                url: '/assets/json/datatable/fr-FR.json',
            },
            "footerCallback": function () {
                const api = this.api();
                const total = api
                    .column(3, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        return parseFloat(a) + 1;
                    }, 0 );
                $(api.column(2).footer()).html(total);
                }
        });
    }
    // Dépotage d'un BL
    const bc = $("#unpotCard");
    if (bc.html() !== undefined) {
        const blId = bc.data('bl');
        // const edit = $("#editId").val();
        $("#blContainer").select2({
			placeholder: 'Sélectionner un numéro',
			ajax: {
				url: '/api/search-container?bl=' + blId,
				dataType: 'json',
				delay: 250,
                data: function (params) {
                    const query = {
                        term: params.term,
                        page: params.page,
                        edit: $("#editId").val()
                    };
                    return query;
                },
				processResults: function (data) {
					const d = [];
					if (data.data.length > 0) {
						data.data.forEach(function(item) {
							d.push({
								id: item.id,
								text: item.type_tc + ' : ' + item.numero
							});
						});
					}
					return {
						results: d
					};
				},
				cache: true
			}
		});
    }
    // Liste des containers
    const containers = $("#containers");
    if (containers.html() !== undefined) {
        const id = containers.data('id');
        containers.DataTable({
            ajax: `/api/get-containers?id=${id}`,
            dataSrc: 'data',
            order: [],
            columnDefs: [
                { 'visible': false, 'targets': [0], orderData: [0] }
            ],
            columns: [
                {data: (data) => {
                    return moment(data.eta).format('YYYYMMDD');
                }},
                {data: 'customer_name'},
                {data: 'type_tc'},
                {data: 'numero'},
                {data: 'lead_number'},
                {data: 'type_name'},
                {data: (data) => display_number(data.nb_package)},
                {data: (data) => display_number(data.quantity)},
                {data: 'ship'},
                {data: (data) => {
                    if (data.eta === undefined || data.eta === null) {
                        return '';
                    }
                    return moment(data.eta).format('DD/MM/YYYY');
                }},
                {data: (data) => {
                    return `<a href="/bl/edit-container.html?bl=${data.bl}&id=${data.id}">Modifier</a><br><a href="/bl/container-delete.html?bl=${data.bl}&id=${data.id}" onclick="return confirm('Voulez-vous vraiment supprimer ce conteneur?')" class="text-danger">Supprimer</a>`;
                }},
            ],
            processing: true,
            language: {
                url: '/assets/json/datatable/fr-FR.json',
            },
            "footerCallback": function () {
                const api = this.api();
                const total = api
                    .column(3, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        return parseFloat(a) + 1;
                    }, 0 );
                const sizeTotals = {};
                api.column(2, {search: 'applied'}).data().each(function (typeTc) {
                    const match = String(typeTc || '').match(/^\s*(\d{2})/);
                    const label = match ? match[1] + ' pieds' : 'Autre';
                    sizeTotals[label] = (sizeTotals[label] || 0) + 1;
                });
                const sizeSummary = Object.keys(sizeTotals)
                    .map((label) => `${label} : ${sizeTotals[label]}`)
                    .join('<br>');
                const nbPackages = api
                    .column(6, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        const nb = parseFloat(b.replace(/\s/g, '').replace(',', '.'));
                        return parseFloat(a) + nb;
                    }, 0 );
                const quantity = api
                    .column(7, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        const nb = parseFloat(b.replace(/\s/g, '').replace(',', '.'));
                        return parseFloat(a) + nb;
                    }, 0 );
                $(api.column(2).footer()).html(`${total}<br><small>${sizeSummary}</small>`);
                $(api.column(6).footer()).html(display_number(nbPackages));
                $(api.column(7).footer()).html(display_number(quantity));
            }
        });
    }
    // Liste des BL en attente pour cette semaine
    const weekWaiting = $('#weekWaitingBl');
	if (weekWaiting.length > 0) {
		if (jQuery().DataTable !== undefined) {
            const userId = weekWaiting.data('user');
			const dt = weekWaiting.DataTable({
				ajax: `/api/v1/bl/get-waiting-week`,
				dataSrc: 'data',
				order: [],
                columnDefs: [
                    { 'visible': false, 'targets': [0], orderData: [0] }
                ],
                "createdRow": function(row, data, dataIndex) {
                    if (data.eta_date === undefined || data.eta_date === null) {
                        return;
                    }
                    if (data.exchange_date !== undefined && data.exchange_date !== null) {
                        return;
                    }   
                    const eta = moment(data.eta_date);
                    const today = moment().startOf('day');
                    if (eta.isBefore(today)) {
                        $(row).addClass('status-failure');
                    } else if (eta.isSame(today, 'day')) {
                        $(row).addClass('status-pending');
                    } else {
                        $(row).addClass('status-success');
                    }
                },
				columns: [
                    {data: (data) => {
                        return moment(data.eta_date).format('YYYYMMDD');
                    }},
					{data: (data) => {
						if (data.customer_name === null || data.customer_name === undefined) {
							return '';
						}
						if (data.nb_docs == 0) {
							return `<a href="/customer/documents.html?customer=${data.customer}" class="text-danger">${data.customer_name}</a>`;
						} else {
							return data.customer_name;
						}
					}},
					{data: (data) => {
						return `<a href="/bl/containers.html?id=${data.id}&rt=${encodeURIComponent('bl/index/waiting')}" class="text-primary">${data.bl}</a>`;
					}},
					{data: (data) => {
						if (data.bl_name === undefined || data.bl_name === null) {
							return '-';
						}
						return data.bl_name.join('<br>');
					}},
					{data: (data) => {
                        if (data.eta_date === undefined || data.eta_date === null) {
                            return '-';
                        }
                        return moment(data.eta_date).format('DD/MM/YYYY');
                    }},
					{data: 'company_name'},
					{data: 'description'},
					{data: 'type_operation'},
					{data: (data) => {
                        if (data.exchange_date === undefined || data.exchange_date === null) {
                            return '-';
                        }
                        return moment(data.exchange_date).format('DD/MM/YYYY');
                    }},
                    {data: (data) => {
                        if (data.bad_date === undefined || data.bad_date === null) {
                            return '-';
                        }
                        return moment(data.bad_date).format('DD/MM/YYYY');
                    }},
                    {data: (data) => {
                        if (data.valid_date === undefined || data.valid_date === null) {
                            return '-';
                        }
                        return moment(data.valid_date).format('DD/MM/YYYY');
                    }},
                    {data: (data) => {
                        if (data.observation === undefined || data.observation === null) {
                            return '-';
                        }
                        return splitText(data.observation, 40).replace(/\n/g, '<br>');
                    }},
                    {data: (data) => {
                        const date_bad = data.bad_date ? moment(data.bad_date).format('YYYY-MM-DD') : '';
                        const date_exchange = data.exchange_date ? moment(data.exchange_date).format('YYYY-MM-DD') : '';
                        return `
                        <div class="dropdown">
                            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-cog"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="/bl/edit.html?id=${data.id}" class="dropdown-item">Modifier</a>
                                <a href="/bl/infos.html?id=${data.id}" class="dropdown-item">Informations</a>
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0);" class="dropdown-item" onclick="showExchangeBLDialog(${data.id}, ${userId}, '${date_exchange}'); return false;">ECHANGE BL</a>
                                <a href="javascript:void(0);" class="dropdown-item" onclick="showBadBLDialog(${data.id}, ${userId}, '${date_bad}', '${data.valid_date ?? ''}'); return false;">BAD</a>
                                <a href="javascript:void(0);" class="dropdown-item" onclick="showObservationBLDialog(${data.id}, ${userId}, ${data.observation ? `'${data.observation.replace(/'/g, "\\'")}'` : ''}); return false;">OBSERVATION</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="/bl/delete-bl.html?id=${data.id}" onclick="return confirm('Voulez-vous vraiment supprimer ce BL?')"><i class="fas fa-times"></i>&nbsp;Supprimer</a>
                            </div>
                        </div>
                    `;
                    }},
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
						.reduce( function (a) {
							return parseFloat(a) + 1;
						}, 0 );
					let totalContainers = api.column(3, {search: 'applied'})
						.data()
						.reduce( function (a, b) {
                            let n = 0;
                            if (b.split('X')[1] !== undefined) {
                                n = parseInt(b.split('X')[1].trim(), 10);
                            }
							return parseFloat(a) + n;
						}, 0 );
					$(api.column(3).footer()).html(totalContainers);
					$(api.column(2).footer()).html(total);
				}
            });
			// En changer les options d'affichage des containers
			$("#filterContainer").on('change', function () {
				const customer = $("#customer").val();
				const company = $("#company").val();
                const startDate = $("#startDate").val();
                const endDate = $("#endDate").val();
				dt.ajax.url(`/api/get-waiting?customer=${customer}&company=${company}&start_date=${startDate}&end_date=${endDate}`)
				dt.ajax.reload();
			});
			$("#filterContainer").on('reset', function () {
				dt.ajax.url(`/api/get-waiting`)
				dt.ajax.reload();
			});
            // exporter les données
            $("#exportWaiting").on('click', function () {
                const customer = $("#customer").val();
                const company = $("#company").val();
                const startDate = $("#startDate").val();
                const endDate = $("#endDate").val();
                window.location.href = `/bl/export-waiting?customer=${customer}&company=${company}&start_date=${startDate}&end_date=${endDate}`;
            });
		}
		if (jQuery().select2 !== undefined) {
			$(".select2").select2();
		}
	}
});