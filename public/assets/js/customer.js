const addBlCustomers = {};
$(function () {
    // Liste des clients
    const listCustomers = $("#listCustomers");
    if (listCustomers) {
        listCustomers.DataTable({
            ajax: `/api/v1/customer/users`,
            dataSrc: 'data',
            order: [],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, row, meta) {
                        // meta.row is the 0-based index of the row
                        // meta.settings._iDisplayStart handles pagination
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }
            ],
            columns: [
                {data: (data) => {
                    return moment(data.created).format('YYYYMMDD');
                }},
                {data: (data) => {
                    if (data.customer_name === null || data.customer_name === undefined) {
                        return '';
                    }
                    return data.customer_name;
                }},
                {data: (data) => {
                    if (data.customer_contact === undefined || data.customer_contact === null) {
                        return '-';
                    }
                    return data.customer_contact;
                }},
                {data: (data) => {
                    if (data.agent_name === undefined || data.agent_name === null) {
                        return '-';
                    }
                    return data.agent_name;
                }},
                {data: (data) => {
                    if (data.agent_contact === undefined || data.agent_contact === null) {
                        return '-';
                    }
                    return data.agent_contact;
                }},
                {data: (data) => {
                    if (data.email === undefined || data.email === null) {
                        return '-';
                    }
                    return data.email;
                }},
                {data: (data) => {
                    if (data.companies === undefined || data.companies === null || data.companies.length === 0) {
                        return '-';
                    }
                    return `<a href="/customer/companies?id=${data.id}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i>&nbsp;Voir</a>`;
                }},
                {data: (data) => {
                    return `
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-cog"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="/customer/edit?id=${data.id}" class="dropdown-item">Modifier</a>
                            <a href="/customer/transfert?id=${data.id}" class="dropdown-item">Transférer</a>
                            <div class="dropdown-divider"></div>
                            <a href="/customer/companies?id=${data.id}" class="dropdown-item">Clients</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="/customer/delete?id=${data.id}" onclick="return confirm('Voulez-vous vraiment supprimer ce client?')"><i class="fas fa-times"></i>&nbsp;Supprimer</a>
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
					.reduce( function (a, _) {
						return parseInt(a) + 1;
					}, 0 );
				$(api.column(2).footer()).html(total == 0 ? '-' : display_number(total));
				const totalCompanies = api
					.rows({search: 'applied'})
					.data()
					.toArray()
					.reduce( function (a, row) {
						const n = Array.isArray(row.companies) ? row.companies.length : 0;
						return parseInt(a) + n;
					}, 0 );
				$(api.column(2).footer()).html(total == 0 ? '-' : display_number(total));
				$(api.column(6).footer()).html(totalCompanies == 0 ? '-' : display_number(totalCompanies));
			}
        });
    }
    // Charger les sociétés du client lors de la sélection d'un client
    $('#blRegCustomer').change(function () {
        const customerId = $(this).val();
        if (!customerId) {
            $("#blCustomerCompany").prop('disabled', true); // Désactiver le select même en cas d'erreur
            $("#blCustomerCompany").html('<option value="">--Scoiété du client--</option>'); // Réinitialiser les options
            $("#customerCompanyGroup .input-loader").addClass('d-none');
            $('#blConsignee').val(''); 
            return; // Si aucun client n'est sélectionné, ne rien faire
        }
        $("#blCustomerCompany").html('<option value="">--Scoiété du client--</option>'); // Réinitialiser les options
        $("#blCustomerCompany").prop('disabled', true); // Désactiver le select pendant le chargement
        $("#customerCompanyGroup .input-loader").removeClass('d-none'); // Afficher le loader
        $.ajax({
            url: '/api/v1/customer/companies?id=' + customerId,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                $("#customerCompanyGroup .input-loader").addClass('d-none'); // Cacher le loader
                if (response.success) {
                    const companies = response.message; // Supposons que les sociétés sont dans le champ "message"
                    $("#blCustomerCompany").removeAttr('disabled'); // Désactiver le select
                    // Remplir le select avec les sociétés
                    companies.forEach(function (company) {
                        addBlCustomers[company.id] = company; // Stocker les sociétés dans un objet pour un accès facile plus tard
                        $("#blCustomerCompany").append('<option value="' + company.id + '">' + company.name + '</option>');
                    });
                    return;
                }
                $('#blConsignee').val(''); // Réinitialiser le champ de saisie du nom de la société
                $("#blCustomerCompany").prop('disabled', true); // Désactiver le select même en cas de succès sans sociétés
                $("#blCustomerCompany").html('<option value="">--Scoiété du client--</option>'); // Réinitialiser les options
            },
            error: function (xhr, status, error) {
                console.error('Erreur lors du chargement des sociétés : ' + error);
                $("#blCustomerCompany").prop('disabled', true); // Désactiver le select même en cas d'erreur
                $("#customerCompanyGroup .input-loader").addClass('d-none'); // Cacher le loader en cas d'erreur
            }
        });
    });
    // Lorsque la société du client change, mettre à jour le champ de saisie du nom de la société
    $('#blCustomerCompany').change(function () {
        const companyId = $(this).find('option:selected').val();
        const company = addBlCustomers[companyId];
        if (company) {
            let content = company.name;
            if (company.address) {
                content += '\n' + company.address; // Ajouter l'adresse sur une nouvelle ligne si elle existe
            }
            $('#blConsignee').val(content); // Mettre à jour le champ de saisie avec le nom et l'adresse de la société
        } else {
            $('#blConsignee').val('');
        }
    });
    if (jQuery().select2) {
        // Transfert de client ou mandataire
        $("#transfertCustomer #to, #transfertCompany #to").select2({
            theme: 'bootstrap',
        });
        // Lorsque le mandataire de destination change pour le transfert
        $("#transfertCustomer #to").on('select2:select', function(e) {
            const customerId = parseInt(e.currentTarget.value, 10);
            if (isNaN(customerId)) {
                return;
            }
            const companyName = $("#old_company").val();
            $.get('/api/v1/customer/get-customer-company?id=' + customerId + '&name=' + companyName, function (data) {
                if (!data.success) {
                    $("#new_company").val('');
                    $("#company_id").val('');
                    return;
                }
                const company = data.message;
                $("#new_company").val(company.name);
                $("#companyId").val(company.id);
            }, 'JSON');
        });
    }
});