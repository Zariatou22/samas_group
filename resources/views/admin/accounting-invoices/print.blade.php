<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Facture {{ $invoice->reference }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
            color: #000;
            margin: 30px;
        }
        .letterhead {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .letterhead td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .letterhead .logo-cell {
            width: 130px;
        }
        .letterhead .logo-cell img {
            width: 120px;
        }
        .letterhead .company-cell {
            padding-left: 12px;
        }
        .letterhead .company-name {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 0 0 2px;
        }
        .letterhead .company-tagline {
            font-size: 11px;
            margin: 0 0 2px;
        }
        .date-line {
            text-align: right;
            white-space: nowrap;
            margin: 12px 0 0;
        }
        .separator {
            border: none;
            border-top: 1px solid #000;
            margin: 8px 0 0;
        }
        h1 {
            font-size: 20px;
            text-align: center;
            margin: 30px 0 20px;
            letter-spacing: 2px;
        }
        .ref {
            color: #b00;
            font-weight: bold;
        }
        .fields div {
            padding: 6px 0;
        }
        .fields span.label {
            font-weight: normal;
        }
        table.lines {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table.lines th, table.lines td {
            border: 1px solid #000;
            padding: 8px;
            font-weight: bold;
            color: #000;
        }
        table.lines th {
            text-align: center;
        }
        table.lines td.designation {
            width: 46%;
        }
        table.lines td.qty, table.lines th.qty {
            text-align: center;
            width: 12%;
        }
        table.lines td.unit-price, table.lines th.unit-price,
        table.lines td.amount, table.lines th.amount {
            text-align: center;
            width: 21%;
        }
        table.lines tr.bl-group-header td {
            background-color: #eee;
        }
        .closing-fields {
            margin-top: 25px;
        }
        .signatures {
            display: flex;
            justify-content: flex-end;
            margin-top: 60px;
        }
        .page-footer {
            margin-top: 80px;
            padding-top: 8px;
            border-top: 1px solid #000;
            font-size: 10px;
            text-align: center;
            color: #000;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <p class="no-print text-right"><button onclick="window.print()">Imprimer</button></p>

    <table class="letterhead">
        <tr>
            <td class="logo-cell"><img src="{{ asset('images/logo.png') }}" alt="SAMAS GROUPE"></td>
            <td class="company-cell">
                <div class="company-name">SAMAS GROUPE</div>
                <div class="company-tagline">TRANSPORT-LOGISTIQUE, TRANSIT-DOUANE, IMPORT-EXPORT, COMMERCE GENERAL,
                    PRESTATIONS DE SERVICES</div>
            </td>
        </tr>
    </table>
    <hr class="separator">
    <div class="date-line">Lomé, le {{ $date_facture }}</div>

    <h1>FACTURE N&deg; <span class="ref">{{ $invoice->reference }}</span></h1>

    <div class="fields">
        <div><span class="label">Doit :</span> {{ $customer_name }}</div>
        <div><span class="label">PC/</span>{{ $mandataire_name }}</div>
    </div>

    @php
        $multiBl = $bl_groups->count() > 1;
        $roman = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
    @endphp
    <table class="lines">
        <thead>
            <tr>
                <th class="designation">DESIGNATION</th>
                <th class="qty">QUANTITE</th>
                <th class="unit-price">PRIX UNITAIRE</th>
                <th class="amount">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bl_groups as $i => $group)
                @if ($multiBl)
                    <tr class="bl-group-header">
                        <td class="designation" colspan="3">{{ ($roman[$i] ?? ($i + 1)).' - BL '.$group->bl_name }}</td>
                        <td class="amount">{{ number_format($group->subtotal, 2, ',', ' ') }}</td>
                    </tr>
                @endif
                @foreach ($group->fields as $field)
                    <tr>
                        <td class="designation">{{ $field->display_designation }}</td>
                        <td class="qty">{{ number_format($field->quantity, 2, ',', ' ') }}</td>
                        <td class="unit-price">{{ number_format($field->unit_price, 2, ',', ' ') }}</td>
                        <td class="amount">{{ number_format($field->amount, 2, ',', ' ') }}</td>
                    </tr>
                @endforeach
            @endforeach
            <tr class="total">
                <td class="designation">TOTAL A PAYER</td>
                <td class="qty"></td>
                <td class="unit-price"></td>
                <td class="amount">{{ number_format($invoice->amount_ttc, 2, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="closing-fields">
        <div>Arrêtée la présente facture à la somme de {{ $amount_words }} francs CFA.</div>
    </div>

    <div class="signatures">
        <div class="text-center">
            <div>Le gérant</div>
            <div style="margin-top: 50px;"><strong style="text-decoration: underline;">Bassirou SANA</strong></div>
        </div>
    </div>

    <div class="page-footer">
        Siège social : SAMAS GROUPE, Boulevard de la paix, Rue de l'aéroport, Lomé &ndash; Togo.
        NIF : 1001876866. Tel : (+228) 72 27 22 83 / 96 26 05 27<br>
        Email : contact@samasgroupe.com / bassirsana@gmail.com &nbsp; Site web : https://www.samasgroupe.com
    </div>
</body>
</html>
