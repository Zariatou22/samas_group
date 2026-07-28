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
            vertical-align: top;
            border: none;
            padding: 0;
        }
        .letterhead .logo-cell {
            width: 90px;
        }
        .letterhead .logo-cell img {
            width: 80px;
        }
        .letterhead .company-cell {
            padding-left: 12px;
        }
        .letterhead .company-name {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .letterhead .tagline {
            font-size: 12px;
        }
        .letterhead .phone {
            font-size: 12px;
            margin-top: 4px;
        }
        .letterhead .date-cell {
            text-align: right;
            white-space: nowrap;
        }
        h1 {
            font-size: 20px;
            text-align: center;
            margin: 15px 0 20px;
            letter-spacing: 2px;
        }
        .ref {
            color: #b00;
            font-weight: bold;
        }
        .fields div {
            border-bottom: 1px dotted #000;
            padding: 4px 0;
            margin-bottom: 4px;
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
            width: 70%;
        }
        table.lines td.amount, table.lines th.amount {
            text-align: right;
            width: 30%;
        }
        table.lines tfoot td {
            font-weight: bold;
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
            <td class="logo-cell"><img src="{{ asset('images/denou-logitrans-logo.jpg') }}" alt="DENOU LOGITRANS"></td>
            <td class="company-cell">
                <div class="company-name">DENOU LOGITRANS TOGO</div>
                <div class="tagline">Transport - Logistique - Transit - Entreposage</div>
                <div class="tagline">Négoce international - Import-Export - Conseil</div>
                <div class="phone">Tél: +228 93 82 58 20&nbsp;&nbsp;&nbsp;HEDZRANAWOE - Lomé</div>
            </td>
            <td class="date-cell">Lomé, le {{ optional($receipt->date_issued)->format('d/m/Y') }}</td>
        </tr>
    </table>

    <h1>REÇU D'AVANCE&nbsp;&nbsp;N° <span class="ref">{{ $receipt->reference }}</span></h1>

    <div class="fields">
        <div><span class="label">Nom et contacts du chauffeur :</span> {{ $receipt->carDriver?->name }}{{ $receipt->carDriver?->contact ? ' — '.$receipt->carDriver->contact : '' }}</div>
        <div><span class="label">N° du camion :</span> {{ $receipt->vehicle?->full_registration }}</div>
        <div><span class="label">N°BL/TC :</span> {{ $receipt->parentBl?->bl }}</div>
        <div><span class="label">Contact client :</span> {{ $receipt->contact_client }}</div>
        <div><span class="label">Contact Transitaire Cincassé :</span> {{ $receipt->contact_transitaire }}</div>
        <div><span class="label">Destination :</span> {{ $receipt->destination }}</div>
    </div>

    <table class="lines">
        <thead>
            <tr>
                <th class="designation">DESIGNATION</th>
                <th class="amount">PRIX TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($receipt->lines as $line)
                <tr>
                    <td class="designation">{{ $line->designation }}</td>
                    <td class="amount">{{ number_format($line->amount, 2, ',', ' ') }}</td>
                </tr>
            @endforeach
            @for ($i = $receipt->lines->count(); $i < 4; $i++)
                <tr>
                    <td class="designation">&nbsp;</td>
                    <td class="amount">&nbsp;</td>
                </tr>
            @endfor
        </tbody>
        <tfoot>
            <tr>
                <td class="designation text-right">TOTAL</td>
                <td class="amount">{{ number_format($receipt->lines->sum('amount'), 2, ',', ' ') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="signatures">
        <div>Signature du Caissier</div>
        <div>Signature du Chauffeur</div>
    </div>
</body>
</html>
