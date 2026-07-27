@extends('layouts.admin')

@section('title', 'Liste des factures du B/L N° '.$bl->bl)

@section('content')
    <p>
        <a href="{{ route('admin.invoices.index', ['customer' => $bl->customer]) }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <div class="card">
        <div class="card-header"><h5>Liste des factures du B/L N° {{ $bl->bl }}</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="blInvoicesTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">CLIENT</th>
                            <th class="text-center align-middle">D&Eacute;SIGNATION</th>
                            <th class="text-center align-middle">R&Eacute;F&Eacute;RENCE DE<br>LA FACTURE</th>
                            <th class="text-center align-middle">MONTANT DE LA<br>FACTURE</th>
                            <th class="text-center align-middle">DATE DE<br>R&Eacute;CEPTION</th>
                            <th class="text-center align-middle">R&Eacute;F&Eacute;RENCE DU<br>PAIEMENT</th>
                            <th class="text-center align-middle">MONTANT<br>PAY&Eacute;</th>
                            <th class="text-center align-middle">RESTE &Agrave; PAYER</th>
                            <th class="text-center align-middle">DATE DE<br>R&Egrave;GLEMENT</th>
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
            $('#blInvoicesTable').DataTable({
                ajax: '{{ route('admin.invoices.bl.data', $bl) }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: [
                    {data: (d) => d.customer_company_name ?? '-'},
                    {data: (d) => d.label_name ?? '-'},
                    {data: (d) => `<a href="/admin/invoices/${d.id}/edit">${d.reference}</a>`},
                    {data: (d) => Number(d.amount ?? 0).toFixed(2)},
                    {data: (d) => d.created ? moment(d.created).format('DD/MM/YYYY') : '-'},
                    {data: (d) => d.payment_reference ?? '-'},
                    {data: (d) => d.paid_amount ? Number(d.paid_amount).toFixed(2) : '-'},
                    {data: (d) => {
                        if (!d.paid_amount) return Number(d.amount ?? 0).toFixed(2);
                        const rest = Number(d.amount ?? 0) - Number(d.paid_amount);
                        return rest === 0 ? '-' : rest.toFixed(2);
                    }},
                    {data: (d) => d.paid_date ? moment(d.paid_date).format('DD/MM/YYYY') : '-'},
                ],
            });
        });
    </script>
@endpush
