$(function () {
    // Liste des containers d'un BL
    const blContainers = $("#listBlContainers");
    if (blContainers.html() !== undefined) {
        const id = blContainers.data('id');
        blContainers.DataTable({
            ajax: `/api/v1/bl/containers?id=${id}`,
            dataSrc: 'data',
            order: [],
            columnDefs: [
                { 'visible': false, 'targets': [0], orderData: [0] }
            ],
            columns: [
                {data: (data) => {
                    return moment(data.eta).format('YYYYMMDD');
                }},
                {data: 'type_tc'},
                {data: 'numero'},
                {data: 'lead_number'},
                {data: 'type_name'},
                {data: (data) => display_number(data.nb_package)},
                {data: (data) => display_number(data.quantity)}
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
                const nbPackages = api
                    .column(5, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        const nb = parseFloat(b.replace(/\s/g, '').replace(',', '.'));
                        return parseFloat(a) + nb;
                    }, 0 );
                const quantity = api
                    .column(6, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        const nb = parseFloat(b.replace(/\s/g, '').replace(',', '.'));
                        return parseFloat(a) + nb;
                    }, 0 );
                $(api.column(1).footer()).html(total == 0 ? '-' : total);
                $(api.column(5).footer()).html(nbPackages == 0 ? '-' : display_number(nbPackages));
                $(api.column(6).footer()).html(quantity == 0 ? '-' : display_number(quantity));
            }
        });
    }
    // Toutes les opérations d'un BL
    const blOp = $("#listBlOperations");
    if (blOp.html() !== undefined) {
        const blId = blOp.data('id');
        blOp.DataTable({
            ajax: `/api/v1/bl/operations?id=${blId}`,
            dataSrc: 'data',
            order: [],
            columnDefs: [
                { 'visible': false, 'targets': [0], orderData: [0] }
            ],
            columns: [
                {data: (data) => {
                    return moment(data.modified).format('YYYYMMDD');
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
                    if (data.transfert_date === undefined || data.transfert_date === null) {
                        return '-';
                    }
                    return moment(data.transfert_date).format('DD/MM/YYYY');
                }},
                {data: (data) => {
                    if (data.tc_invoice === undefined || data.tc_invoice === null) {
                        return '-';
                    }
                    return moment(data.tc_invoice).format('DD/MM/YYYY');
                }},
                {data: (data) => {
                    if (data.dfu_invoice === undefined || data.dfu_invoice === null) {
                        return '-';
                    }
                    return moment(data.dfu_invoice).format('DD/MM/YYYY');
                }},
                {data: (data) => {
                    if (data.type_operation !== 'DEPOTAGE') {
                        return '-';
                    }
                    if (data.unpot_state === undefined || data.unpot_state === null || data.unpoted === 0) {
                        return 'En attente';
                    }
                    if ((data.unpotable - data.unpoted) == 0) {
                        return 'Dépotage terminé';
                    }
                    return `Dépotage en cours (${data.unpoted}/${data.unpotable})`;
                }},
                {data: () => '-'}
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
                $(api.column(2).footer()).html(total == 0 ? '-' : total);
            }
        });
    }
    // Toutes les opérations d'un BL
    const blDocs = $("#listBlDocuments");
    if (blDocs.html() !== undefined) {
        const blId = blDocs.data('id');
        blDocs.DataTable({
            ajax: `/api/v1/bl/documents?id=${blId}`,
            dataSrc: 'data',
            order: [],
            columnDefs: [
                { 'visible': false, 'targets': [0], orderData: [0] }
            ],
            columns: [
                {data: (data) => {
                    return moment(data.modified).format('YYYYMMDD');
                }},
                {data: () => '-'},
                {data: () => '-'},
                {data: () => '-'}
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
                $(api.column(1).footer()).html(total == 0 ? '-' : total);
            }
        });
    }
    // Liste des BL disponible pour déclaration
    const blAuthAv = $("#listAvailableAuthBl");
    if (blAuthAv.html() !== undefined) {
        const dest = blAuthAv.data('dest');
        const encours = blAuthAv.data('encours');
        blAuthAv.DataTable({
            ajax: `/api/v1/bl/get-available`,
            dataSrc: 'data',
            order: [],
            columnDefs: [
                { 'visible': false, 'targets': [0], orderData: [0] }
            ],
            columns: [
                {data: (data) => {
                    return moment(data.eta_date).format('YYYYMMDD');
                }},
                {data: 'bl'},
                {data: (data) => {
                    const diff = parseInt(data.nb_containers) - parseInt(data.authorized_containers);
                    return display_number(diff);
                }},
                {data: (data) => {
                    const diff = parseInt(data.nb_package) - parseInt(data.authorized_package);
                    return display_number(diff);
                }},
                {data: (data) => {
                    const diff = parseFloat(data.quantity) - parseFloat(data.authorized);
                    return display_number(diff);
                }},
                {data: (data) => {
                    let url = dest;
                    if (dest.includes('?')) {
                        url += `&bl=${data.id}`;
                    } else {
                        url += `?bl=${data.id}`;
                    }
                    if (encours) {
                        url += `&encours=${encours}`;
                    }
                    return `<a href="${url}">Aller&nbsp;&rarr;</a>`;
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
                const totalContainers = api
                    .column(2, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        const nb = parseFloat(b.replace(/\s/g, '').replace(',', '.'));
                        return parseFloat(a) + nb;
                    }, 0 );
                const totalPackages = api
                    .column(3, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        const nb = parseFloat(b.replace(/\s/g, '').replace(',', '.'));
                        return parseFloat(a) + nb;
                    }, 0 );
                const totalQuantity = api
                    .column(4, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        const nb = parseFloat(b.replace(/\s/g, '').replace(',', '.'));
                        return parseFloat(a) + nb;
                    }, 0 );
                $(api.column(1).footer()).html(total == 0 ? '-' : display_number(total));
                $(api.column(2).footer()).html(totalContainers == 0 ? '-' : display_number(totalContainers));
                $(api.column(3).footer()).html(totalPackages == 0 ? '-' : display_number(totalPackages));
                $(api.column(4).footer()).html(totalQuantity == 0 ? '-' : display_number(totalQuantity));
            }
        });
    }
});