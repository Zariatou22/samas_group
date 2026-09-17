@extends('layouts.admin')

@section('title', 'Factures prestataires')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/select2-admin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2-bootstrap.min.css') }}">
@endpush

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <div class="card card-featured card-featured-primary mt-3">
        <div class="card-header">
            <h3 class="card-title">LISTE DES MANDATAIRES</h3>
        </div>
        <div class="card-body">
            <form class="form-inline mb-3" id="filterInvoiceCustomers">
                <div class="form-group mb-2">
                    <select name="customer_company" id="invoiceFilterCustomerCompany" class="form-control" style="width: 250px;">
                        <option value="">--Client--</option>
                    </select>
                </div>
                <div class="form-group mb-2 mx-sm-3">
                    <select name="customer" id="invoiceFilterCustomer" class="form-control" style="width: 200px;">
                        <option value="">--Mandataire--</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-2">
                    <button class="btn btn-tertiary btn-sm" type="reset"><i class="fas fa-undo"></i></button>
                </div>
            </form>
            <div class="text-muted small mb-2" id="invoiceCustomersTotals"></div>
            <div class="row" id="invoiceCustomers"></div>
            <p class="text-muted text-center mb-0" id="invoiceCustomersEmpty" style="display:none;">Aucun mandataire trouvé.</p>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/i18n/select2/fr.js') }}"></script>
    <script>
        $(function () {
            const ivcCustomers = $("#invoiceCustomers");

            function displayNumber(n) {
                return Number(n ?? 0).toLocaleString('fr-FR');
            }

            function renderInvoiceCustomers(list) {
                ivcCustomers.empty();
                $("#invoiceCustomersEmpty").toggle(list.length === 0);
                let totalAmount = 0;
                let totalPaid = 0;
                list.forEach(function (data) {
                    const total = parseFloat(data.invoiced_total) || 0;
                    const paid = parseFloat(data.paid_total) || 0;
                    const reste = total - paid;
                    totalAmount += total;
                    totalPaid += paid;
                    ivcCustomers.append(`
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-2">
                            <a href="/admin/invoices?customer=${data.id}" class="card h-100 no-select text-decoration-none mandataire-card" style="cursor: pointer;">
                                <div class="card-body p-2">
                                    <h6 class="mb-1 text-truncate text-dark" style="font-size: 0.8rem;" title="${data.customer_name}">${data.customer_name}</h6>
                                    <div class="d-flex justify-content-between text-muted" style="font-size: 0.7rem;">
                                        <span>Total</span><span>${displayNumber(total)}</span>
                                    </div>
                                    <div class="d-flex justify-content-between text-muted" style="font-size: 0.7rem;">
                                        <span>Payé</span><span>${displayNumber(paid)}</span>
                                    </div>
                                    <div class="d-flex justify-content-between font-weight-bold ${reste > 0 ? 'text-danger' : 'text-success'}" style="font-size: 0.7rem;">
                                        <span>Reste</span><span>${reste == 0 ? '-' : displayNumber(reste)}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    `);
                });
                $("#invoiceCustomersTotals").text(`${list.length} mandataire(s) — Total : ${displayNumber(totalAmount)} · Payé : ${displayNumber(totalPaid)} · Reste : ${displayNumber(totalAmount - totalPaid)}`);
            }

            function loadInvoiceCustomers() {
                const customer = $("#invoiceFilterCustomer").val();
                const customerCompany = $("#invoiceFilterCustomerCompany").val();
                $.ajax({
                    url: `{{ route('admin.mandataire-balances.data') }}?customer=${customer}&customer_company=${customerCompany}`,
                    dataType: 'json',
                    success: function (response) {
                        renderInvoiceCustomers(response.data || []);
                    }
                });
            }

            loadInvoiceCustomers();

            $("#invoiceFilterCustomerCompany").select2({
                theme: 'bootstrap',
                placeholder: '--Sélectionner un client--',
                allowClear: true,
                ajax: {
                    url: '/api/v1/customer/search-companies-with-commands',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        return {
                            results: data.items.map(function (item) {
                                return { id: item.id, text: item.name };
                            })
                        };
                    },
                },
            });
            $("#invoiceFilterCustomer").select2({ theme: 'bootstrap' });

            $("#filterInvoiceCustomers").on('change', loadInvoiceCustomers);
            $("#filterInvoiceCustomers").on('reset', function () {
                setTimeout(loadInvoiceCustomers);
            });
        });
    </script>
@endpush
