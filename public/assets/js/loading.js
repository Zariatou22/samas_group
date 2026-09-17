const carLoadingContainers = {};
const carLoadingAuthorizations = {};
$(function () {
    /**
     * Liste des chargements (type=0), des dépotages (type=1), ou de la Mise en entrepôt
     * (type=0 restreint aux B/L TRANSFERT MAD, sans lien avec Franchises) avec DataTables.
     * Les trois pages partagent le même gabarit de tableau, seuls les attributs data-type et
     * data-warehousing changent.
     */
    const loadingsTableIsUnloading = $('#loadingsTable').data('type') == 1;
    const loadingsTableIsWarehousing = $('#loadingsTable').data('warehousing') == 1;
    const loadingsEditRoute = loadingsTableIsWarehousing ? '/loading/warehousing-edit.html' : (loadingsTableIsUnloading ? '/loading/unloading-edit.html' : '/loading/edit.html');
    const loadingsAjaxUrl = loadingsTableIsWarehousing ? '/api/v1/loading/get-warehousing' : (loadingsTableIsUnloading ? '/api/v1/loading/get-unloadings' : '/api/v1/loading/get-loadings');
    const loadingsDt = $('#loadingsTable').DataTable({
        colReorder: true,
        processing: true,
        ajax: loadingsAjaxUrl,
        dataSrc: 'data',
        order: [],
        columnDefs: [
            { 'visible': false, 'targets': [0], orderData: [0] }
        ],
        columns: [
            {data: (data) => {
                return moment(data.loading_date).format('YYYYMMDD');
            }},
            {data: (data) => data.customer_name || '-'},
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
            {data: 'bl_number'},
            {data: 'auth_number'},
            {data: (data) => {
                if (!data.containers || data.containers.length === 0) {
                    return '';
                }
                return data.containers.map(c => c.numero).join('<br>');
            }},
            {data: (data) => {
                const car = {
                    'full_registration': data.car_full_registration,
                    'driver_name': data.driver_name,
                    'driver_contact': data.driver_contact,
                    'owner_name': data.owner_name,
                    'owner_contact': data.owner_contact
                };
                return `<a href="javascript:void(0)" onclick="showCarDetails('${encodeURIComponent(JSON.stringify(car))}')">${data.car_full_registration ? data.car_full_registration.replace(/\//g, '<br>') : ''}</a>`;
            }},
            {data: (data) => data.position || '-'},
            {data: 'source_name'},
            {data: (data) => display_number(data.quantity)},
            {data: (data) => moment(data.loading_date).format('DD/MM/YYYY')},
            {data: (data) => {
                if (!data.bad_valid_date) {
                    return '-';
                }
                return moment(data.bad_valid_date).format('DD/MM/YYYY');
            }},
            {data: (data) => {
                return `
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-cog"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="${loadingsEditRoute}?id=${data.id}" class="dropdown-item">Modifier</a>
                            <a href="/car/edit.html?id=${data.car}&rt=${encodeURIComponent(loadingsTableIsWarehousing ? 'loading/warehousing' : (loadingsTableIsUnloading ? 'loading/unloadings' : 'loading'))}" class="dropdown-item">Modifier le camion</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="/loading/delete-loading?id=${data.id}" onclick="return confirm('Voulez-vous vraiment supprimer cette opération?')"><i class="fas fa-times"></i>&nbsp;Supprimer</a>
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
            const totalWeight = api
                .column(9, {search: 'applied'})
                .data()
                .reduce( function (a, b) {
                    let n = 0;
                    if (b !== '-') {
                        n = parseFloat(b.replace(/\s/g, '').replace(',', '.'), 10);
                    }
                    return parseFloat(a) + n;
                }, 0 );
            const total = api
                    .column(6, {search: 'applied'})
                    .data()
                    .reduce( function (a, _) {
                        return parseFloat(a) + 1;
                    }, 0 );
            $(api.column(9).footer()).html(totalWeight == 0 ? '-' : display_number(totalWeight));
            $(api.column(6).footer()).html(total == 0 ? '-' : display_number(total));
        }
    });
    // Filtres Mandataire / Client au-dessus du tableau (comme sur "Tous les B/L") : rechargent
    // le tableau avec les paramètres customer/customer_company déjà supportés par l'API.
    const loadingFilterCustomerCompany = $('#loadingFilterCustomerCompany');
    if (loadingFilterCustomerCompany.length > 0 && jQuery().select2 !== undefined) {
        loadingFilterCustomerCompany.select2({
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
        $('#loadingFilterCustomer').select2({
            theme: 'bootstrap',
        });
    }
    $("#filterLoadings").on('change', function () {
        const customer = $("#loadingFilterCustomer").val();
        const customerCompany = $("#loadingFilterCustomerCompany").val();
        loadingsDt.ajax.url(`${loadingsAjaxUrl}?customer=${customer}&customer_company=${customerCompany}`);
        loadingsDt.ajax.reload();
    });
    $("#filterLoadings").on('reset', function () {
        setTimeout(function () {
            $('#loadingFilterCustomerCompany').val(null).trigger('change.select2');
            loadingsDt.ajax.url(loadingsAjaxUrl);
            loadingsDt.ajax.reload();
        });
    });
    // Exporter les données : si une recherche/filtre est appliqué sur le tableau, on
    // n'exporte que les lignes actuellement affichées (filtrées), pas tout le tableau.
    $("#exportAllLoadings").on('click', function () {
        const ids = loadingsDt.rows({search: 'applied'}).data().toArray().map(row => row.id);
        window.location.href = `/loading/export-all?ids=${ids.join(',')}`;
    });
    const blInput = $("#loadingBl");
    if (blInput.html() !== undefined && jQuery().select2 !== undefined) {
        blInput.select2({
            theme: 'bootstrap',
            placeholder: '--Sélectionner un B/L--',
            allowClear: true,
        });
    }
    // Lorsqu'on en cours de mise à jour de chargement
    const editLoadId = $("#editLoadId");
    if (editLoadId.html() !== undefined) {
        const blId = $('#loadingBl').val();
        if (blId) {
            const editAuthId = $('#editAuthId').val();
            if (!editAuthId || editAuthId === '') {
                return;
            }
            loadAuthorizations(blId, editAuthId);
            const containersIds = $('#editContainerIds').val();
            if (!containersIds || containersIds === '') {
                return;
            }
            // const ctIds = containersIds.split(',').map(id => id.trim());
            loadContainers(blId, containersIds);
        }
    }
    // Lorsque le B/L est sélectionné, charger les déclarations associées
    $('#loadingBl').on('change', function () {
        var blId = $(this).val();
        // Pré-remplir la validité BAD du BL sélectionné (peut ensuite être modifiée à la main)
        const badValid = $(this).find(':selected').data('bad-valid');
        $('#badValidDate').val(badValid || '');
        loadAuthorizations(blId);
        // Lorsque la déclaration est sélectionnée, charger les conteneurs associés
        loadContainers(blId);
        // Lorsque le conteneur est sélectionné, afficher les informations du conteneur
        containerChange();
    });
    // Page "Franchises" : chargements dont le B/L est entièrement chargé ("Chargement terminé"
    // dans Opérations en cours) et pas encore clôturé ; sans les colonnes Lieu et Date, avec le
    // nombre de jours restants avant la validité BAD à la place.
    const franchiseTable = $('#franchiseTable');
    if (franchiseTable.length > 0) {
        const franchiseDt = franchiseTable.DataTable({
            colReorder: true,
            processing: true,
            ajax: {
                url: '/api/v1/loading/get-franchise-loadings',
                dataSrc: function (json) {
                    renderFranchiseAlerts(json.data || []);
                    return json.data || [];
                }
            },
            order: [],
            columnDefs: [
                { 'visible': false, 'targets': [0], orderData: [0] }
            ],
            columns: [
                {data: (data) => {
                    return data.bad_valid_date ? moment(data.bad_valid_date).format('YYYYMMDD') : '99999999';
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
                {data: 'bl_number'},
                {data: 'auth_number'},
                {data: (data) => {
                    if (!data.containers || data.containers.length === 0) {
                        return '';
                    }
                    return data.containers.map(c => c.numero).join('<br>');
                }},
                {data: (data) => {
                    const car = {
                        'full_registration': data.car_full_registration,
                        'driver_name': data.driver_name,
                        'driver_contact': data.driver_contact,
                        'owner_name': data.owner_name,
                        'owner_contact': data.owner_contact
                    };
                    return `<a href="javascript:void(0)" onclick="showCarDetails('${encodeURIComponent(JSON.stringify(car))}')">${data.car_full_registration ? data.car_full_registration.replace(/\//g, '<br>') : ''}</a>`;
                }},
                {data: (data) => data.position || '-'},
                {data: (data) => display_number(data.quantity)},
                {data: (data) => {
                    if (!data.bad_valid_date) {
                        return '-';
                    }
                    return moment(data.bad_valid_date).format('DD/MM/YYYY');
                }},
                {data: (data) => {
                    if (!data.bad_valid_date) {
                        return '-';
                    }
                    const days = moment(data.bad_valid_date).startOf('day').diff(moment().startOf('day'), 'days');
                    let cssClass = 'text-success';
                    if (days < 0) {
                        cssClass = 'text-danger font-weight-bold';
                    } else if (days <= 5) {
                        cssClass = 'text-warning font-weight-bold';
                    }
                    return `<span class="${cssClass}">${days}</span>`;
                }},
            ],
            processing: true,
            pageLength: 25,
            language: {
                url: '/assets/json/datatable/fr-FR.json',
            },
            "footerCallback": function () {
                const api = this.api();
                const totalWeight = api
                    .column(7, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseFloat(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                const total = api
                        .column(4, {search: 'applied'})
                        .data()
                        .reduce( function (a, _) {
                            return parseFloat(a) + 1;
                        }, 0 );
                $(api.column(7).footer()).html(totalWeight == 0 ? '-' : display_number(totalWeight));
                $(api.column(4).footer()).html(total == 0 ? '-' : display_number(total));
            }
        });
        // Cartes au-dessus du tableau : total de dossiers (bleu), échéance à <= 5 jours mais
        // pas encore dépassée (orange), et validité BAD déjà dépassée (rouge).
        function renderFranchiseAlerts(data) {
            $('#franchiseTotalCount').text(data.length);
            const warningCount = data.filter(function (d) {
                if (!d.bad_valid_date) {
                    return false;
                }
                const days = moment(d.bad_valid_date).startOf('day').diff(moment().startOf('day'), 'days');
                return days >= 0 && days <= 5;
            }).length;
            const alertCount = data.filter(function (d) {
                if (!d.bad_valid_date) {
                    return false;
                }
                const days = moment(d.bad_valid_date).startOf('day').diff(moment().startOf('day'), 'days');
                return days < 0;
            }).length;
            $('#franchiseWarningCount').text(warningCount);
            $('#franchiseAlertCount').text(alertCount);
        }
        // Cartes cliquables : filtrent le tableau sur le même critère que leur compteur
        // (tous / échéance <= 5 jours non dépassée / déjà échu).
        let franchiseCardFilter = 'all';
        $.fn.dataTable.ext.search.push(function (settings, searchData, index, rowData) {
            if (settings.nTable.id !== 'franchiseTable' || franchiseCardFilter === 'all') {
                return true;
            }
            if (!rowData || !rowData.bad_valid_date) {
                return false;
            }
            const days = moment(rowData.bad_valid_date).startOf('day').diff(moment().startOf('day'), 'days');
            if (franchiseCardFilter === 'warning') {
                return days >= 0 && days <= 5;
            }
            if (franchiseCardFilter === 'alert') {
                return days < 0;
            }
            return true;
        });
        function setFranchiseCardFilter(filter) {
            franchiseCardFilter = filter;
            $('#franchiseCardTotal, #franchiseCardWarning, #franchiseCardAlert').removeClass('card-filter-active');
            if (filter === 'warning') {
                $('#franchiseCardWarning').addClass('card-filter-active');
            } else if (filter === 'alert') {
                $('#franchiseCardAlert').addClass('card-filter-active');
            } else {
                $('#franchiseCardTotal').addClass('card-filter-active');
            }
            franchiseDt.draw();
        }
        $('#franchiseCardTotal').on('click', function () { setFranchiseCardFilter('all'); });
        $('#franchiseCardWarning').on('click', function () { setFranchiseCardFilter('warning'); });
        $('#franchiseCardAlert').on('click', function () { setFranchiseCardFilter('alert'); });
        setFranchiseCardFilter('all');
        // Filtre Mandataire -> Client : le choix du mandataire restreint la liste des
        // clients à ceux qui lui appartiennent, comme dans le formulaire d'ajout de BL.
        function reloadFranchiseTable() {
            const customer = $("#franchiseCustomer").val();
            const customerCompany = $("#franchiseCustomerCompany").val();
            setFranchiseCardFilter('all');
            franchiseDt.ajax.url(`/api/v1/loading/get-franchise-loadings?customer=${customer}&customer_company=${customerCompany}`);
            franchiseDt.ajax.reload();
        }
        $('#franchiseCustomer').on('change', function () {
            const customerId = $(this).val();
            const $company = $('#franchiseCustomerCompany');
            $company.html('<option value="">--Client--</option>');
            if (!customerId) {
                $company.prop('disabled', true);
                reloadFranchiseTable();
                return;
            }
            $company.prop('disabled', true);
            $.ajax({
                url: '/api/v1/customer/companies?id=' + customerId,
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        response.message.forEach(function (company) {
                            $company.append('<option value="' + company.id + '">' + company.name + '</option>');
                        });
                        $company.removeAttr('disabled');
                    }
                    reloadFranchiseTable();
                },
                error: function () {
                    reloadFranchiseTable();
                }
            });
        });
        $('#franchiseCustomerCompany').on('change', reloadFranchiseTable);
        $('#filterFranchise').on('reset', function () {
            setTimeout(function () {
                $('#franchiseCustomerCompany').html('<option value="">--Client--</option>').prop('disabled', true);
                reloadFranchiseTable();
            });
        });
        // Exporter la franchise selon le mandataire/client actuellement choisis
        $('#exportFranchise').on('click', function () {
            const customer = $("#franchiseCustomer").val();
            const customerCompany = $("#franchiseCustomerCompany").val();
            window.location.href = `/tracking/export?customer=${customer}&customer_company=${customerCompany}`;
        });
    }
});
function loadAuthorizations(blId, editId = null) {
    // $('#loadingAuthorization').empty();
    // $('#loadingContainer').empty();
    if (!blId) {
        $('#loadingAuthorization').empty();
        $('#loadingContainer').empty();
        return;
    }
    // Utiliser select2 pour faire une requête AJAX vers le serveur et récupérer les déclarations associées au B/L sélectionné
    $('#loadingAuthorization').select2({
        theme: 'bootstrap',
        placeholder: '--Sélectionner une déclaration--',
        allowClear: true,
        ajax: {
            url: `/api/v1/authorization/get-available-auth-for-loading`,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // terme de recherche pour filtrer les résultats côté serveur
                    bl: blId,
                    edit_id: editId, // Vous pouvez ajuster cette valeur selon vos besoins
                    type: $('#loadingType').val() || 0 // 0 = chargement, 1 = dépotage
                };
            },
            processResults: function (data, _) {
                return {
                    results: data.items.map(function (item) {
                        carLoadingAuthorizations[item.id] = item; // Stocker les données de la déclaration pour une utilisation ultérieure
                        return {
                            id: item.id,
                            text: item.auth_number
                        };
                    })
                };
            },
        },
    });
}
function loadContainers(blId, containersIds = null) {
    $('#loadingAuthorization').on('change', function () {
        var authId = $(this).val();
        // $('#loadingContainer').empty();
        if (!authId) {
            $('#loadingContainer').empty();
            $("#loadingNbPackage").val('');
            $("#loadingQuantity").val('');
            return;
        }
        // const blId = $('#loadingBl').val();
        const auth = carLoadingAuthorizations[authId];
        // Pour le dépotage terminal, le Lieu est saisi manuellement (voir #sourceId visible
        // dans le formulaire) : ne pas écraser le choix de l'utilisateur avec celui de la déclaration.
        if ($('#loadingType').val() != '1') {
            $("#sourceId").val(auth.source);
        }
        $("#customerId").val(auth.customer);
        $("#customerCompanyId").val(auth.customer_company);
        // Utiliser select2 pour faire une requête AJAX vers le serveur et récupérer les conteneurs associés à la déclaration sélectionnée
        $('#loadingContainer').select2({
            theme: 'bootstrap',
            placeholder: '--Sélectionner un conteneur--',
            allowClear: true,
            ajax: {
                url: `/api/v1/container/get-available-containers-for-loading`,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // terme de recherche pour filtrer les résultats côté serveur
                        authorization: authId,
                        bl: blId,
                        edit_ids: containersIds, // Vous pouvez ajuster cette valeur selon vos besoins
                        type: $('#loadingType').val() || 0 // 0 = chargement, 1 = dépotage
                    };
                },
                processResults: function (data, _) {
                    return {
                        results: data.items.map(function (item) {
                            carLoadingContainers[item.id] = item; // Stocker les données du conteneur pour une utilisation ultérieure
                            return {
                                id: item.id,
                                text: item.numero
                            };
                        })
                    };
                },
            },
        });
    });
}
function containerChange() {
    $('#loadingContainer').on('change', function () {
        // Pour un dépotage, le nombre de colis reste saisi manuellement par l'utilisateur :
        // on ne l'écrase pas automatiquement (seul le poids reste calculé depuis les conteneurs).
        const skipPackageAutofill = $(this).data('skip-package-autofill') == 1;
        const containerIds = $(this).val();
        if (!containerIds || containerIds.length === 0) {
            if (!skipPackageAutofill) {
                $("#loadingNbPackage").val('');
            }
            $("#loadingQuantity").val('');
            return;
        }
        let remainingPackages = 0;
        let remainingQuantity = 0;
        containerIds.forEach(containerId => {
            const container = carLoadingContainers[containerId];
            if (container) {
                remainingPackages += parseInt(container.nb_package);
                remainingQuantity += parseFloat(container.quantity);
            }
        });
        const nbPackageInput = $("#loadingNbPackage");
        const quantityInput = $("#loadingQuantity");
        if (!skipPackageAutofill) {
            nbPackageInput.val(remainingPackages);
        }
        quantityInput.val(remainingQuantity);
    });
}