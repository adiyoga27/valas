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
            width: 100%; 
        }
        .invoice-wrapper {
            width: 195px; /* Thermal-friendly, ±76mm paper */
            margin: 0 auto; 
            padding: 0;
            box-sizing: border-box;
        }

        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 5px 0; 
        }
        th, td { 
            padding: 1px 0px; 
            vertical-align: top;
        }
        th { text-align: left; }
        .line { border-top: 1px dashed #000; margin: 5px 0; }
        .notes { font-size: 9px; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="invoice-wrapper">
        <div class="center bold">
            INVOICE PEMBELIAN<br>
            -------------------------
        </div>

        <p style="margin: 5px 0;">
            Kode: {{ $transaction->transaction_code }}<br>
            Tanggal: {{ $transaction->created_at->format('d M Y H:i:s') }}<br>
            Customer: {{ $transaction->customer_name ?? 'N/A' }}<br>
            Dibuat Oleh: {{ $transaction->user->name ?? 'Sistem' }}
        </p>

        <!-- Detail Item Pembelian -->
        <table>
            <thead>
                <tr>
                    <th class="left" style="width: 20%;">BN</th> 
                    <th class="left" style="width: 20%;">AMOUNT</th>
                    <th class="right" style="width: 20%;">RATE</th>
                    <th class="right" style="width: 40%;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $item)
                    <tr>
                        <td class="left">{{ $item->currency_code }}</td>
                        <td class="left">{{ number_format($item->qty,0,',','.') }}</td>
                        <td class="right">{{ number_format($item->sell_rate, 0,',','.') }}</td>
                        <td class="right">{{ number_format($item->total, 0,',','.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="line"></div>

        <!-- Total Item -->
        <p class="right bold">
            TOTAL ITEM: {{ number_format($transaction->total_amount, 0,',','.') }}
        </p>

        <!-- Biaya Tambahan Dijabarkan -->
        @if(!empty($transaction->additional_amounts))
            <table>
                <thead>
                    <tr>
                        <th class="left" style="width: 60%;">BIAYA TAMBAHAN</th>
                        <th class="right" style="width: 40%;">JUMLAH</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->additional_amounts as $add)
                        <tr>
                            <td class="left">{{ $add['name'] }}</td>
                            <td class="right">{{ number_format($add['amount'],0,',','.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Total Biaya Tambahan -->
            <p class="right bold">
                TOTAL BIAYA TAMBAHAN: {{ number_format(collect($transaction->additional_amounts)->sum('amount'),0,',','.') }}
            </p>
        @endif

        <!-- Grand Total -->
        <p class="right bold">
            GRAND TOTAL: {{ number_format($transaction->grand_total, 0,',','.') }}
        </p>

        <!-- Notes -->
        @if($transaction->notes)
            <p class="notes">
                Catatan: {{ $transaction->notes }}
            </p>
        @endif

        <div class="center" style="margin-top: 10px;">
            -------------------------<br>
            Terima Kasih
        </div>
    </div>
</body>
</html>
