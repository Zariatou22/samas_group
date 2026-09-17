$(function () {
    // Copier du text dans le presse-papier
    $('.copy-text').on('click', function (e) {
        e.preventDefault();
        const c = $(this);
        if (c.find('.fas').hasClass('fa-copy')) {
            const target = c.data('target');
            const targetClass = `.copy-${target}`;
            clip = new ClipboardJS(targetClass);
            clip.on('success', (e) => e.clearSelection()).on('error', (e) => {
                console.error(e);
                e.clearSelection();
            });
            c.find('.fas').removeClass('fa-copy').addClass('fa-check');
            setTimeout(() => c.find('.fas').removeClass('fa-check').addClass('fa-copy'), 1000);
        }
    });
    // Liste des déclarations non soldées
    const authOngoing = $("#authOngoing");
    if (authOngoing) {
        authOngoing.DataTable({
            ajax: `/api/v1/authorization/get-auth?completed=0`,
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
                {data: (data) => {
                    return `<a href="/loading.html?bl=${data.bl}&auth=${data.id}" class="text-primary">${data.auth_number}</a>`;
                }},
                {data: (data) => display_number(data.nb_container)},
                {data: (data) => {
                    if (data.loaded_containers === undefined || data.loaded_containers == null || data.loaded_containers == 0) {
                        return '-';
                    }
                    return display_number(data.loaded_containers);
                }},
                {data: (data) => {
                    const diff = parseInt(data.nb_container) - parseInt(data.loaded_containers);
                    if (diff === 0) {
                        return '-';
                    }
                    return display_number(diff);
                }},
                {data: (data) => display_number(data.nb_package)},
                {data: (data) => {
                    if (data.loaded_packages === undefined || data.loaded_packages == null || data.loaded_packages == 0) {
                        return '-';
                    }
                    return display_number(data.loaded_packages);
                }},
                {data: (data) => {
                    const diff = parseInt(data.nb_package) - parseInt(data.loaded_packages);
                    if (diff === 0) {
                        return '-';
                    }
                    return display_number(diff);
                }},
                {data: (data) => display_number(data.quantity)},
                {data: (data) => {
                    if (data.loaded_quantity === undefined || data.loaded_quantity == null || data.loaded_quantity == 0) {
                        return '-';
                    }
                    return display_number(data.loaded_quantity);
                }},
                {data: (data) => {
                    const diff = parseFloat(data.quantity) - parseFloat(data.loaded_quantity);
                    if (diff === 0) {
                        return '-';
                    }
                    return display_number(diff);
                }},
                {data: (data) => {
                    return `
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-cog"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="/authorization/add-auth.html?bl=${data.bl}&id=${data.id}" class="dropdown-item">Modifier</a>
                            <a href="/loading.html?bl=${data.bl}&auth=${data.id}" class="dropdown-item">Chargements</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="/admin/delete-container.html?id=${data.id}" onclick="return confirm('Voulez-vous vraiment supprimer ce BL?')"><i class="fas fa-times"></i>&nbsp;Supprimer</a>
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
                    .column(1, {search: 'applied'})
                    .data()
                    .reduce( function (a) {
                        return parseFloat(a) + 1;
                    }, 0 );
                const totalAuth = api
                    .column(4, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseInt(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                const totalAuthLoaded = api
                    .column(5, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseFloat(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                const totalAuthRest = api
                    .column(6, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseFloat(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                const totalPackage = api
                    .column(7, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseInt(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                const totalPackageLoaded = api
                    .column(8, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseFloat(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                const totalPackageRest = api
                    .column(9, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseFloat(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                const totalQuantity = api
                    .column(10, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseInt(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                const totalQuantityLoaded = api
                    .column(11, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseFloat(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                const totalQuantityRest = api
                    .column(12, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseFloat(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                $(api.column(3).footer()).html(total == 0 ? '-' : display_number(total));
                $(api.column(4).footer()).html(totalAuth == 0 ? '-' : display_number(totalAuth));
                $(api.column(5).footer()).html(totalAuthLoaded === 0 ? '-' : display_number(totalAuthLoaded));
                $(api.column(6).footer()).html(totalAuthRest == 0 ? '-' : display_number(totalAuthRest));
                $(api.column(7).footer()).html(totalPackage == 0 ? '-' : display_number(totalPackage));
                $(api.column(8).footer()).html(totalPackageLoaded == 0 ? '-' : display_number(totalPackageLoaded));
                $(api.column(9).footer()).html(totalPackageRest == 0 ? '-' : display_number(totalPackageRest));
                $(api.column(10).footer()).html(totalQuantity == 0 ? '-' : display_number(totalQuantity));
                $(api.column(11).footer()).html(totalQuantityLoaded == 0 ? '-' : display_number(totalQuantityLoaded));
                $(api.column(12).footer()).html(totalQuantityRest == 0 ? '-' : display_number(totalQuantityRest));
            },
        });
    }
    // Liste des déclarations non soldées
    const authCompleted = $("#authCompleted");
    if (authCompleted) {
        const dt = authCompleted.DataTable({
            ajax: `/api/v1/authorization/get-auth?completed=1`,
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
                        return '';
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
                {data: (data) => {
                    return `<a href="/loading.html?bl=${data.bl}&auth=${data.id}" class="text-primary">${data.auth_number}</a>`;
                }},
                {data: (data) => display_number(data.nb_container)},
                {data: (data) => {
                    if (data.loaded_containers === undefined || data.loaded_containers == null || data.loaded_containers == 0) {
                        return '-';
                    }
                    return display_number(data.loaded_containers);
                }},
                {data: (data) => {
                    const diff = parseInt(data.nb_container) - parseInt(data.loaded_containers);
                    return display_number(diff);
                }},
                {data: (data) => display_number(data.nb_package)},
                {data: (data) => {
                    if (data.loaded_packages === undefined || data.loaded_packages == null || data.loaded_packages == 0) {
                        return '-';
                    }
                    return display_number(data.loaded_packages);
                }},
                {data: (data) => {
                    const diff = parseInt(data.nb_package) - parseInt(data.loaded_packages);
                    return display_number(diff);
                }},
                {data: (data) => display_number(data.quantity)},
                {data: (data) => {
                    if (data.loaded_quantity === undefined || data.loaded_quantity == null || data.loaded_quantity == 0) {
                        return '-';
                    }
                    return display_number(data.loaded_quantity);
                }},
                {data: (data) => {
                    const diff = parseFloat(data.quantity) - parseFloat(data.loaded_quantity);
                    return display_number(diff);
                }},
                {data: (data) => {
                    return `
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-cog"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="/authorization/add-auth.html?bl=${data.bl}&id=${data.id}" class="dropdown-item">Modifier</a>
                            <a href="/loading.html?bl=${data.bl}&auth=${data.id}" class="dropdown-item">Chargements</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="/admin/delete-container.html?id=${data.id}" onclick="return confirm('Voulez-vous vraiment supprimer ce BL?')"><i class="fas fa-times"></i>&nbsp;Supprimer</a>
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
                    .column(1, {search: 'applied'})
                    .data()
                    .reduce( function (a) {
                        return parseFloat(a) + 1;
                    }, 0 );
                const totalAuth = api
                    .column(4, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseInt(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                const totalAuthLoaded = api
                    .column(5, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseFloat(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                const totalAuthRest = api
                    .column(6, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseFloat(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                const totalPackage = api
                    .column(7, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseInt(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                const totalPackageLoaded = api
                    .column(8, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseFloat(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                const totalPackageRest = api
                    .column(9, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseFloat(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                const totalQuantity = api
                    .column(10, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseInt(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                const totalQuantityLoaded = api
                    .column(11, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseFloat(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                const totalQuantityRest = api
                    .column(12, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseFloat(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                $(api.column(3).footer()).html(total == 0 ? '-' : display_number(total));
                $(api.column(4).footer()).html(totalAuth == 0 ? '-' : display_number(totalAuth));
                $(api.column(5).footer()).html(totalAuthLoaded === 0 ? '-' : display_number(totalAuthLoaded));
                $(api.column(6).footer()).html(totalAuthRest == 0 ? '-' : display_number(totalAuthRest));
                $(api.column(7).footer()).html(totalPackage == 0 ? '-' : display_number(totalPackage));
                $(api.column(8).footer()).html(totalPackageLoaded == 0 ? '-' : display_number(totalPackageLoaded));
                $(api.column(9).footer()).html(totalPackageRest == 0 ? '-' : display_number(totalPackageRest));
                $(api.column(10).footer()).html(totalQuantity == 0 ? '-' : display_number(totalQuantity));
                $(api.column(11).footer()).html(totalQuantityLoaded == 0 ? '-' : display_number(totalQuantityLoaded));
                $(api.column(12).footer()).html(totalQuantityRest == 0 ? '-' : display_number(totalQuantityRest));
            }
        });
    }
    // Liste des déclarations disponibles pour chargement
    const listAvailableAuth = $("#listAvailableAuth");
    if (listAvailableAuth) {
        const dest = listAvailableAuth.data('dest');
        listAvailableAuth.DataTable({
            ajax: `/api/v1/authorization/get-available-auth?bl=${listAvailableAuth.data('bl')}`,
            dataSrc: 'data',
            order: [],
            columnDefs: [
                { 'visible': false, 'targets': [0], orderData: [0] }
            ],
            columns: [
                {data: (data) => {
                    return moment(data.eta_date).format('YYYYMMDD');
                }},
                {data: 'bl_name'},
                {data: 'auth_number'},
                {data: (data) => {
                    const diff = parseInt(data.nb_container) - parseInt(data.loaded_containers);
                    return display_number(diff);
                }},
                {data: (data) => {
                    const diff = parseInt(data.nb_package) - parseInt(data.loaded_packages);
                    return display_number(diff);
                }},
                {data: (data) => {
                    const diff = parseFloat(data.quantity) - parseFloat(data.loaded_quantity);
                    return display_number(diff);
                }},
                {data: (data) => {
                    return `<a href="${dest}?bl=${data.id}&auth=${data.id}">Aller&nbsp;&rarr;</a>`;
                }}
            ],
            processing: true,
            language: {
                url: '/assets/json/datatable/fr-FR.json',
            },
            "footerCallback": function () {
                const api = this.api();
                const total = api
                    .column(1, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        return parseFloat(a) + 1;
                    }, 0 );
                const totalAuth = api
                    .column(2, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        return parseFloat(a) + 1;
                    }, 0 );
                const totalContainers = api
                    .column(3, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        const nb = parseFloat(b.replace(/\s/g, '').replace(',', '.'));
                        return parseFloat(a) + nb;
                    }, 0 );
                const totalPackages = api
                    .column(4, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        const nb = parseFloat(b.replace(/\s/g, '').replace(',', '.'));
                        return parseFloat(a) + nb;
                    }, 0 );
                const totalQuantity = api
                    .column(5, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        const nb = parseFloat(b.replace(/\s/g, '').replace(',', '.'));
                        return parseFloat(a) + nb;
                    }, 0 );
                $(api.column(1).footer()).html(total == 0 ? '-' : display_number(total));
                $(api.column(2).footer()).html(totalAuth == 0 ? '-' : display_number(totalAuth));
                $(api.column(3).footer()).html(totalContainers == 0 ? '-' : display_number(totalContainers));
                $(api.column(4).footer()).html(totalPackages == 0 ? '-' : display_number(totalPackages));
                $(api.column(5).footer()).html(totalQuantity == 0 ? '-' : display_number(totalQuantity));
            }
        });
    }
});