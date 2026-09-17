@extends('layouts.admin')

@section('title', 'Factures clients')

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <a href="{{ route('admin.accounting-invoices.create') }}" class="btn btn-outline-primary"><i class="fa fa-plus"></i> Nouvelle facture</a>
        <a href="{{ route('admin.accounting-invoices.operations.unbilled') }}" class="btn btn-outline-warning"><i class="fa fa-exclamation-circle"></i> Opérations non facturées</a>
        <a href="{{ route('admin.accounting-invoice-labels.index') }}" class="btn btn-outline-secondary"><i class="fa fa-tags"></i> Libellés</a>
        <a href="{{ route('admin.accounting-invoice-field-regulars.index') }}" class="btn btn-outline-secondary"><i class="fa fa-list"></i> Champs récurrents</a>
    </p>

    <div class="card">
        <div class="card-header"><h5>Liste des factures clients</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="accountingInvoiceTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">R&Eacute;F&Eacute;RENCE</th>
                            <th class="text-center align-middle">CLIENT</th>
                            <th class="text-center align-middle">DATE</th>
                            <th class="text-center align-middle">MONTANT</th>
                            <th class="text-center align-middle">MONTANT TTC</th>
                            <th class="text-center align-middle">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            $('#accountingInvoiceTable').DataTable({
                ajax: '{{ route('admin.accounting-invoices.data') }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: [
                    {data: (d) => `<a href="/admin/accounting-invoices/${d.id}/edit">${d.reference}</a>`},
                    {data: (d) => d.customer_name ?? '-'},
                    {data: (d) => d.date_issued ?? '-'},
                    {data: (d) => Number(d.amount ?? 0).toFixed(2)},
                    {data: (d) => Number(d.amount_ttc ?? 0).toFixed(2)},
                    {data: (d) => `
                        <a href="/admin/accounting-invoices/${d.id}/edit" class="btn btn-sm btn-primary" title="Modifier"><i class="fa fa-edit"></i></a>
                        <a href="/admin/accounting-invoices/${d.id}/print" class="btn btn-sm btn-secondary" title="Imprimer" target="_blank"><i class="fa fa-print"></i></a>
                        <form method="POST" action="/admin/accounting-invoices/${d.id}" class="d-inline" onsubmit="return confirm('Archiver cette facture ?')">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button class="btn btn-sm btn-danger" title="Archiver"><i class="fa fa-trash"></i></button>
                        </form>
                    `},
                ],
            });
        });
    </script>
@endpush
