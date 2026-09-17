const t1LoadedCars = {};
$(function () {
    // Liste des T1 en cours de validité
    $("#t1OngoingTable").DataTable({
        processing: true,
        ajax: '/api/v1/t1/get-ongoing',
        dataSrc: 'data',
        order: [],
        columnDefs: [
            { 'visible': false, 'targets': [0], orderData: [0] }
        ],
        "createdRow": function(row, _, _) {
            row.firstElementChild.classList.add("align-left");
        },
        columns: [
            {data: (data) => {
                return moment(data.created).format('YYYYMMDD');
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
            {data: 'bl_name'},
            {data: 'auth_number'},
            {data: (data) => {
                if (!data.container_names || data.container_names.length === 0) {
                    return '';
                }
                return data.container_names.join('<br>');
            }},
            {data: (data) => {
                const car = {
                    'full_registration': data.full_registration,
                    'driver_name': data.driver_name,
                    'driver_contact': data.driver_contact,
                    'owner_name': data.owner_name,
                    'owner_contact': data.owner_contact
                };
                return `<a href="javascript:void(0)" onclick="showCarDetails('${encodeURIComponent(JSON.stringify(car))}')">${data.full_registration ? data.full_registration.replace(/\//g, '<br>') : ''}</a>`;
            }},
            {data: 'bl_route'},
            {data: 't1_number'},
            {data: (data) => {
                if (data.validate === undefined || data.validate === null || data.validate === '') {
                    return 'En attente <br>de validation';
                }
                return `<span class="badge badge-success">Validé</span>`;
            }},
            {data: (data) => {
                return moment(data.valid_until).format('DD/MM/YYYY');
            }},
            {data: (data) => {
                return `
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-cog"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="/t1/edit.html?id=${data.id}" class="dropdown-item">Modifier</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="/t1/delete?id=${data.id}" onclick="return confirm('Voulez-vous vraiment supprimer ce T1?')"><i class="fas fa-times"></i>&nbsp;Supprimer</a>
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
                .column(7, {search: 'applied'})
                .data()
                .reduce( function (a, _) {
                    return parseInt(a) + 1;
                }, 0 );
            $(api.column(7).footer()).html(total == 0 ? '-' : display_number(total));
        }
    });
    // Liste des T1 expiré
    $("#t1ExpiredTable").DataTable({
        processing: true,
        ajax: '/api/v1/t1/get-expired',
        dataSrc: 'data',
        order: [],
        columnDefs: [
            { 'visible': false, 'targets': [0], orderData: [0] }
        ],
        "createdRow": function(row, _, _) {
            row.firstElementChild.classList.add("align-left");
        },
        columns: [
            {data: (data) => {
                return moment(data.created).format('YYYYMMDD');
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
            {data: 'bl_name'},
            {data: 'auth_number'},
            {data: (data) => {
                if (!data.container_names || data.container_names.length === 0) {
                    return '';
                }
                return data.container_names.join('<br>');
            }},
            {data: (data) => {
                const car = {
                    'full_registration': data.full_registration,
                    'driver_name': data.driver_name,
                    'driver_contact': data.driver_contact,
                    'owner_name': data.owner_name,
                    'owner_contact': data.owner_contact
                };
                return `<a href="javascript:void(0)" onclick="showCarDetails('${encodeURIComponent(JSON.stringify(car))}')">${data.full_registration ? data.full_registration.replace(/\//g, '<br>') : ''}</a>`;
            }},
            {data: 'bl_route'},
            {data: 't1_number'},
            {data: (data) => {
                if (data.validate === undefined || data.validate === null || data.validate === '') {
                    return 'En attente <br>de validation';
                }
                return `<span class="badge badge-success">Validé</span>`;
            }},
            {data: (data) => {
                return moment(data.valid_until).format('DD/MM/YYYY');
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
                .column(7, {search: 'applied'})
                .data()
                .reduce( function (a, _) {
                    return parseInt(a) + 1;
                }, 0 );
            $(api.column(7).footer()).html(total == 0 ? '-' : display_number(total));
        }
    });
    // Liste des T1 en attente
    $("#t1WaitingTable").DataTable({
        processing: true,
        ajax: '/api/v1/t1/get-waiting',
        dataSrc: 'data',
        order: [],
        columnDefs: [
            { 'visible': false, 'targets': [0], orderData: [0] }
        ],
        columns: [
            {data: (data) => {
                return moment(data.created).format('YYYYMMDD');
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
            {data: 'bl_name'},
            {data: 'auth_number'},
            {data: (data) => {
                if (!data.container_names || data.container_names.length === 0) {
                    return '';
                }
                return data.container_names.join('<br>');
            }},
            {data: (data) => {
                const car = {
                    'full_registration': data.full_registration,
                    'driver_name': data.driver_name,
                    'driver_contact': data.driver_contact,
                    'owner_name': data.owner_name,
                    'owner_contact': data.owner_contact
                };
                return `<a href="javascript:void(0)" onclick="showCarDetails('${encodeURIComponent(JSON.stringify(car))}')">${data.full_registration ? data.full_registration.replace(/\//g, '<br>') : ''}</a>`;
            }},
            {data: (data) => display_number(data.quantity)},
            {data: (data) => {
                return `
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-cog"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="/t1/edit.html?loading=${data.id}&rt=${encodeURIComponent('t1/index/waiting')}" class="dropdown-item">Faire le T1</a>
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
                .column(4, {search: 'applied'})
                .data()
                .reduce( function (a, _) {
                    return parseInt(a) + 1;
                }, 0 );
            $(api.column(4).footer()).html(total == 0 ? '-' : display_number(total));
        }
    });
    // Edition de T1
    const t1Edit = $("#t1EditForm");
    if (t1Edit.html() !== undefined) {
        // Recherche de chargement
        const editId = $("#editId").val();
        $("#t1Car").select2({
            theme: 'bootstrap',
            placeholder: '--Sélectionner un chargement--',
            allowClear: true,
            ajax: {
            url: `/api/v1/t1/waiting`,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // terme de recherche pour filtrer les résultats côté serveur
                    edit_id: editId // Vous pouvez ajuster cette valeur selon vos besoins
                };
            },
            processResults: function (data, _) {
                return {
                    results: data.items.map(function (item) {
                        t1LoadedCars[item.id] = item; // Stocker les données de la déclaration pour une utilisation ultérieure
                        return {
                            id: item.id,
                            text: item.full_registration
                        };
                    })
                };
            },
        },
        });
        // Lorsque le camion change
        $("#t1Car").on('select2:select', function () {
            const carId = $("#t1Car").val();
            if (!t1LoadedCars[carId]) {
                clearForm();
                return;
            }
            const car = t1LoadedCars[carId];
            $("#t1CarRegistration").val(car.full_registration);
            $("#t1CarRegistration").parent().find('.copy-text').attr('data-clipboard-text', car.full_registration);
            $("#authorization").val(car.auth_number);
            $("#authorization").parent().find('.copy-text').attr('data-clipboard-text', car.auth_number);
            $("#nbPackage").val(car.nb_package);
            $("#nbPackage").parent().find('.copy-text').attr('data-clipboard-text', car.nb_package);
            $("#route").val(car.bl_route);
            $("#route").parent().find('.copy-text').attr('data-clipboard-text', car.bl_route);
            $("#t1Containers").val(car.container_names.join(','));
            $("#t1Containers").parent().find('.copy-text').attr('data-clipboard-text', car.container_names.join(','));
            $("#quantity").val(car.quantity);
            $("#quantity").parent().find('.copy-text').attr('data-clipboard-text', car.quantity);
        });
        // Lorqu'on nettoie le chargement
        $("#t1Car").on('select2:clear', function () {
            clearForm();
        });
    }
});
function clearForm() {
    $("#t1CarRegistration").val('');
    $("#t1CarRegistration").parent().find('.copy-text').attr('data-clipboard-text', '');
    $("#authorization").val('');
    $("#authorization").parent().find('.copy-text').attr('data-clipboard-text', '');
    $("#nbPackage").val('');
    $("#nbPackage").parent().find('.copy-text').attr('data-clipboard-text', '');
    $("#t1Containers").val('');
    $("#t1Containers").parent().find('.copy-text').attr('data-clipboard-text', '');
    $("#quantity").val('');
    $("#quantity").parent().find('.copy-text').attr('data-clipboard-text', '');
    $("#route").val('');
    $("#route").parent().find('.copy-text').attr('data-clipboard-text', '');
}