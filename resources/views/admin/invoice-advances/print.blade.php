<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Reçu d'avance {{ $receipt->reference }}</title>
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
        .letterhead .phone {
            font-size: 13px;
        }
        .letterhead .date-cell {
            text-align: right;
            white-space: nowrap;
            vertical-align: top;
        }
        .separator {
            border: none;
            border-top: 1px solid #000;
            margin: 8px 0 0;
        }
        h1 {
            font-size: 20px;
            text-align: center;
            margin: 45px 0 30px;
            letter-spacing: 2px;
        }
        .ref {
            color: #b00;
            font-weight: bold;
        }
        .fields div {
            border-bottom: 1px dotted #000;
            padding: 8px 0;
            margin-bottom: 10px;
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
            text-align: right;
            width: 21%;
        }
        table.lines td.payment-line {
            padding-left: 40%;
            text-align: left;
        }
        .closing-fields {
            margin-top: 25px;
        }
        .closing-fields div {
            border-bottom: 1px dotted #000;
            padding: 4px 0;
            margin-bottom: 6px;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
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
            <td class="logo-cell"><img src="{{ asset('images/logo-facture.jpeg') }}" alt="DENOU LOGITRANS"></td>
            <td class="company-cell">
                <div class="company-name">DENOU LOGITRANS TOGO</div>
                <div class="company-tagline">Transport - Logistique - Transit - Entreposage</div>
                <div class="company-tagline">Négoce international - Import-Export - Conseil</div>
                <div class="phone">Tél: +228 93 82 58 20&nbsp;&nbsp;&nbsp;HEDZRANAWOE - Lomé</div>
            </td>
            <td class="date-cell">Lomé, le {{ optional($receipt->date_issued)->format('d/m/Y') }}</td>
        </tr>
    </table>
    <hr class="separator">

    <h1>REÇU D'AVANCE&nbsp;&nbsp;N° <span class="ref">{{ $receipt->reference }}</span></h1>

    <div class="fields">
        <div><span class="label">Nom et contacts du chauffeur :</span> {{ $receipt->carDriver?->name }}{{ $receipt->carDriver?->contact ? ' — '.$receipt->carDriver->contact : '' }}</div>
        <div><span class="label">N° du camion :</span> {{ $receipt->vehicle?->full_registration }}</div>
        <div><span class="label">N°BL/TC :</span> {{ $receipt->parentBl?->bl }}</div>
        <div><span class="label">Nom et contact du client :</span> {{ $receipt->contact_client }}</div>
        <div><span class="label">Nom et contact du transitaire Cincassé :</span> {{ $receipt->contact_transitaire }}</div>
        <div><span class="label">Destination :</span> {{ $receipt->destination }}</div>
    </div>

    <table class="lines">
        <thead>
            <tr>
                <th class="designation">DESIGNATION</th>
                <th class="qty">QUANTITE</th>
                <th class="unit-price">PRIX UNITAIRE</th>
                <th class="amount">PRIX TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($receipt->lines as $line)
                <tr>
                    <td class="designation">{{ $line->designation }}</td>
                    <td class="qty">{{ number_format($line->quantity, 2, ',', ' ') }}</td>
                    <td class="unit-price">{{ number_format($line->unit_price, 2, ',', ' ') }}</td>
                    <td class="amount">{{ number_format($line->amount, 2, ',', ' ') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" class="payment-line">Avance reçu : {{ $receipt->avance_recu !== null ? number_format($receipt->avance_recu, 2, ',', ' ') : '' }}</td>
            </tr>
            <tr>
                <td colspan="4" class="payment-line">Reste à payer : {{ $receipt->reste_a_payer !== null ? number_format($receipt->reste_a_payer, 2, ',', ' ') : '' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="closing-fields">
        <div>Arrêté le présent reçu à la somme de : {{ $receipt->arrete_somme }}</div>
        <div>Reste à payer à destination : {{ $receipt->reste_a_payer_destination }}</div>
    </div>

    <div class="signatures">
        <div>Signature du Caissier</div>
        <div>Signature du Chauffeur</div>
    </div>
</body>
</html>
