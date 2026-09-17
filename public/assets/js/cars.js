const loadingCars = {};
const loadingOwners = {};
const loadingDrivers = {};
$(function () {
    // Liste des tous les véhicules
    const listCars = $("#listCars");
    if (listCars.html() !== undefined) {
        listCars.DataTable({
            ajax: `/api/v1/car/get-cars?status=0`,
            dataSrc: 'data',
            order: [],
            columnDefs: [
                { 'visible': false, 'targets': [0], orderData: [0] }
            ],
            columns: [
                {data: (data) => {
                    return data.full_registration;
                }},
                {data: 'front_registration'},
                {data: 'back_registration'},
                {data: 'driver_name'},
                {data: 'owner_name'},
                {data: (data) => data.total_voyage > 0 ? display_number(data.total_voyage) : '-'},
                {data: (data) => {
                    return `
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-cog"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="/car/edit.html?id=${data.id}" class="dropdown-item">Modifier</a>
                            <a href="/loading.html?car=${data.id}" class="dropdown-item">Voyages</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="/car/delete-car.html?id=${data.id}" onclick="return confirm('Voulez-vous vraiment supprimer ce véhicule?')"><i class="fas fa-times"></i>&nbsp;Supprimer</a>
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
                let totalVoyages = api.column(5, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseInt(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                $(api.column(2).footer()).html(total == 0 ? '-' : display_number(total));
                $(api.column(5).footer()).html(totalVoyages == 0 ? '-' : display_number(totalVoyages));
            }
        });
    }
    // Liste des transporteurs
    const listOwners = $("#listCarOwners");
    if (listOwners.html() !== undefined) {
        listOwners.DataTable({
            ajax: `/api/v1/car/get-owners?status=0`,
            dataSrc: 'data',
            order: [],
            columnDefs: [
                { 'visible': false, 'targets': [0], orderData: [0] }
            ],
            columns: [
                {data: (data) => {
                    return data.name;
                }},
                {data: 'name'},
                {data: 'contact'},
                {data: 'address'},
                {data: (data) => data.total_cars > 0 ? display_number(data.total_cars) : '-'},
                {data: (data) => data.total_voyage > 0 ? display_number(data.total_voyage) : '-'},
                {data: (data) => {
                    return `
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-cog"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="/car/edit-owner.html?id=${data.id}" class="dropdown-item">Modifier</a>
                            <a href="/loading.html?owner=${data.id}" class="dropdown-item">Voyages</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="/car/delete-owner.html?id=${data.id}" onclick="return confirm('Voulez-vous vraiment supprimer ce transporteur?')"><i class="fas fa-times"></i>&nbsp;Supprimer</a>
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
                const totalCars = api.column(4, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseInt(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                const totalVoyages = api.column(5, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseInt(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                $(api.column(1).footer()).html(total == 0 ? '-' : display_number(total));
                $(api.column(4).footer()).html(totalCars == 0 ? '-' : display_number(totalCars));
                $(api.column(5).footer()).html(totalVoyages == 0 ? '-' : display_number(totalVoyages));
            }
        });
    }
    // Liste des transporteurs
    const listDrivers = $("#listCarDrivers");
    if (listDrivers.html() !== undefined) {
        listDrivers.DataTable({
            ajax: `/api/v1/car/get-drivers?status=0`,
            dataSrc: 'data',
            order: [],
            columnDefs: [
                { 'visible': false, 'targets': [0], orderData: [0] }
            ],
            columns: [
                {data: (data) => {
                    return data.name;
                }},
                {data: 'name'},
                {data: 'contact'},
                {data: 'owner_name'},
                {data: (data) => data.total_voyages > 0 ? display_number(data.total_voyages) : '-'},
                {data: (data) => {
                    return `
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-cog"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="/car/edit-driver.html?id=${data.id}" class="dropdown-item">Modifier</a>
                            <a href="/loading.html?driver=${data.id}" class="dropdown-item">Voyages</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="/car/delete-driver.html?id=${data.id}" onclick="return confirm('Voulez-vous vraiment supprimer ce chauffeur?')"><i class="fas fa-times"></i>&nbsp;Supprimer</a>
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
                const totalCars = api.column(4, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseInt(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                const totalVoyages = api.column(5, {search: 'applied'})
                    .data()
                    .reduce( function (a, b) {
                        let n = 0;
                        if (b !== '-') {
                            n = parseInt(b.replace(/\s/g, '').replace(',', '.'), 10);
                        }
                        return parseFloat(a) + n;
                    }, 0 );
                $(api.column(1).footer()).html(total == 0 ? '-' : display_number(total));
                $(api.column(4).footer()).html(totalCars == 0 ? '-' : display_number(totalCars));
                $(api.column(5).footer()).html(totalVoyages == 0 ? '-' : display_number(totalVoyages));
            }
        });
    }
    // Liste des transporteurs
    const ownerList = $('#ownerList');
    if (ownerList.html() !== undefined) {
        // Liste des transporteurs distincts avec select2 en ajax
        ownerList.select2({
            theme: 'bootstrap',
            placeholder: '--Transporteur--',
            allowClear: true,
            ajax: {
                url: '/api/v1/car/get-distinct-owners?status=0',
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    const results = [];
                    $.each(data.data, function (_, owner) {
                        loadingOwners[owner.id] = owner;
                        results.push({
                            id: owner.id,
                            text: owner.owner_name
                        });
                    });
                    return {
                        results: results
                    };
                }
            }
        });
    }
    // Chargement des données pour le formulaire de création et de modification d'un voyage
    const loadingForm = $('#loadingForm');
    if (loadingForm.html() !== undefined) {
        // Liste des véhicules distincts
        fetchCars();
        // Lorsque le tracteur du véhicule change
        $('#frontRegistration, #backRegistration').on('change', function () {
            const frontReg = $('#frontRegistration').val();
            const backReg = $('#backRegistration').val();
            const car = loadingCars[frontReg + '-' + backReg];
            if (car) {
                $('#carId').val(car.id);
                $('#driverName').val(car.driver_name);
                $('#driverContact').val(car.driver_contact);
                $('#ownerName').val(car.owner_name);
                $('#ownerContact').val(car.owner_contact);
                $("#driverId").val(car.driver);
                $("#ownerId").val(car.owner);
            }
        });
        // Liste des chaffeurs distincts
        fetchDrivers();
        // Lorsque le chauffeur change
        $('#driverName').on('change', function () {
            const driverName = $(this).val();
            const driver = loadingDrivers[driverName];
            if (driver) {
                $('#driverId').val(driver.id);
                $('#driverContact').val(driver.contact);
            }
        });
        // Liste des transporteurs distincts
        fetchOwners();
        // Lorsque le transporteur change
        $('#ownerName').on('change', function () {
            const ownerName = $(this).val();
            const owner = loadingOwners[ownerName];
            if (owner) {
                $('#ownerId').val(owner.id);
                $('#ownerContact').val(owner.contact);
            }
        });
    }
});
function fetchOwners() {
    $.ajax({
        url: '/api/v1/car/get-distinct-owners?status=0',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            const owners = response.data;
            const ownerNameSelect = $('#ownerNameList');
            const ownerContactSelect = $('#ownerContactList');
            const ownerName = $('#ownerName');
            const ownerContact = $('#ownerContact');
            ownerName.empty();
            ownerNameSelect.empty();
            ownerContact.empty();
            ownerContactSelect.empty();
            $.each(owners, function (_, owner) {
                loadingOwners[owner.owner_name] = owner;
                ownerNameSelect.append('<option value="' + owner.owner_name + '" />');
                ownerContactSelect.append('<option value="' + owner.contact + '" />');
            });
        },
        error: function (_, _, error) {
            console.error('Erreur de chargement des chauffeurs : ' + error);
        }
    });
}
function fetchDrivers() {
    $.ajax({
        url: '/api/v1/car/get-distinct-drivers?status=0',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            const drivers = response.data;
            const driverNameSelect = $('#driverNameList');
            const driverContactSelect = $('#driverContactList');
            const driverName = $('#driverName');
            const driverContact = $('#driverContact');
            driverName.empty();
            driverNameSelect.empty();
            driverContact.empty();
            driverContactSelect.empty();
            $.each(drivers, function (_, driver) {
                loadingDrivers[driver.driver_name] = driver;
                driverNameSelect.append('<option value="' + driver.driver_name + '" />');
                driverContactSelect.append('<option value="' + driver.contact + '" />');
            });
        },
        error: function (_, _, error) {
            console.error('Erreur de chargement des chauffeurs : ' + error);
        }
    });
}
function fetchCars() {
    $.ajax({
        url: '/api/v1/car/get-distinct-cars?status=0',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            const cars = response.data;
            const frontReg = $('#carFrontRegistrations');
            const backReg = $('#carBackRegistrations');
            frontReg.empty();
            backReg.empty();
            $.each(cars, function (_, car) {
                loadingCars[car.front_registration + '-' + car.back_registration] = car;
                frontReg.append('<option value="' + car.front_registration + '">');
                backReg.append('<option value="' + car.back_registration + '">');
            });
        },
        error: function (_, _, error) {
            console.error('Erreur de chargement des véhicules : ' + error);
        }
    });
}