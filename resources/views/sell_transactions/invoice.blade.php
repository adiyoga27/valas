<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $transaction->transaction_code }}</title>

    <style>
        body {
            font-family: monospace;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        .invoice-wrapper {
            width: 195px; /* ±76mm */
            margin: 0 auto;
        }

        .center { text-align: center; }
        .right  { text-align: right; }
        .bold   { font-weight: bold; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 1px 0;
            vertical-align: top;
        }

        th {
            text-align: left;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 4px 0;
        }

        /* TOTAL */
        .totals-table td {
            padding: 1px 0;
            font-size: 10px;
        }

        .totals-table .label {
            text-align: left;
        }

        .totals-table .value {
            text-align: right;
        }

        .totals-table .grand td {
            font-weight: bold;
            border-top: 1px dashed #000;
            padding-top: 3px;
            font-size: 11px;
        }

        .notes {
            font-size: 9px;
            margin-top: 4px;
        }

        /* SIGNATURE */
        .signature {
            margin-top: 12px;
            text-align: center;
        }

        .signature table {
            width: 100%;
            margin-top: 6px;
        }

        .signature td {
            font-size: 9px;
            text-align: center;
        }

        .sign-line {
            margin-top: 18px;
            border-top: 1px solid #000;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>

<body>
<div class="invoice-wrapper">

    <!-- HEADER -->
    <div class="center">
        <span class="bold">{{ $office->name }}</span><br>
        ( {{ $office->address }} )<br>
        PHONE {{ $office->phone }}<br>
        <span class="bold">INVOICE PENJUALAN VALAS</span><br>
        -------------------------
    </div>

    <!-- INFO -->
<table  style="
    width:100%;
    font-size:9px;
    border-collapse:collapse;
    font-family: Arial, sans-serif;
">
    <tr>
        <td style="width:80px;">CODE</td>
        <td style="width:10px;">:</td>
        <td>{{ $transaction->transaction_code }}</td>
    </tr>
    <tr>
        <td>DATE</td>
        <td>:</td>
        <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
    </tr>
    <tr>
        <td>NAME</td>
        <td>:</td>
        <td>{{ $transaction->customer_name ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td>PASSPORT</td>
        <td>:</td>
        <td>{{ $transaction->passport_number ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td>ADDRESS</td>
        <td>:</td>
        <td>{{ $transaction->customer_address ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td>COUNTRY</td>
        <td>:</td>
        <td>{{ $transaction->customer_country ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td>ADMIN</td>
        <td>:</td>
        <td>{{ $transaction->user->name ?? 'Sistem' }}</td>
    </tr>
</table>


    <!-- ITEM -->
    <table>
        <thead>
            <tr>
                <th style="width:20%">BN</th>
                <th style="width:20%">JML</th>
                <th style="width:20%" class="right">KURS</th>
                <th style="width:40%" class="right">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaction->items as $item)
                <tr>
                    <td>{{ $item->currency_code }}</td>
                    <td>{{ number_format($item->qty, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($item->sell_rate, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="line"></div>

    <!-- TOTAL -->
    <table class="totals-table">
        <tr>
            <td class="label">Subtotal</td>
            <td class="value">{{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
        </tr>

        @if (!empty($transaction->additional_amounts))
            @foreach ($transaction->additional_amounts as $add)
                <tr>
                    <td class="label">{{ $add['name'] }}</td>
                    <td class="value">{{ number_format($add['amount'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        @endif

        <tr class="grand">
            <td class="label">GRAND TOTAL</td>
            <td class="value">{{ number_format($transaction->grand_total, 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- NOTES -->
    @if ($transaction->notes)
        <div class="notes">
            Catatan: {{ $transaction->notes }}
        </div>
    @endif

    <!-- FOOTER -->
    <div class="center" style="margin-top:6px;">
        -------------------------<br>
        Terima Kasih
    </div>

    <!-- SIGNATURE -->
    <div class="signature">
        <table>
            <tr>
                <td>Customer</td>
                <td>Cashier</td>
            </tr>
            <tr>
                <td>
                    <div class="sign-line"></div>
                    {{ $transaction->customer_name ?? '__________' }}
                </td>
                <td>
                    <div class="sign-line"></div>
                    {{ $transaction->user->name ?? 'Sistem' }}
                </td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>
