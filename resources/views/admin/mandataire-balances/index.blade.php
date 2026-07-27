@extends('layouts.admin')

@section('title', 'Factures prestataires')

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <div class="card">
        <div class="card-header"><h5>Liste des mandataires</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="balanceTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">MANDATAIRE</th>
                            <th class="text-center align-middle">MONTANT TOTAL</th>
                            <th class="text-center align-middle">MONTANT PAY&Eacute;</th>
                            <th class="text-center align-middle">RESTE</th>
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
            $('#balanceTable').DataTable({
                ajax: '{{ route('admin.mandataire-balances.data') }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: [
                    {data: 'customer_name'},
                    {data: (d) => Number(d.invoiced_total ?? 0).toFixed(2)},
                    {data: (d) => Number(d.paid_total ?? 0).toFixed(2)},
                    {data: (d) => `<span class="badge badge-${d.remaining > 0 ? 'danger' : 'success'}">${Number(d.remaining ?? 0).toFixed(2)}</span>`},
                    {data: (d) => `<a href="/admin/invoices?customer=${d.id}" class="btn btn-sm btn-primary" title="Voir les factures"><i class="fa fa-eye"></i></a>`},
                ],
            });
        });
    </script>
@endpush
