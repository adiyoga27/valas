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
            /* Penting: Pastikan lebar body diambil 100% dari kertas */
            width: 100%; 
        }
        /* Wrapper untuk memaksa konten agar pas di lebar kertas yang sempit */
        .invoice-wrapper {
            width: 195px; /* Sedikit lebih kecil dari kertas (200) untuk memberikan 2.5px margin di setiap sisi (kira-kira 0.8mm) */
            margin: 0 auto; /* Tengah, tapi karena 195px hampir penuh, dia akan mengisi */
            padding: 0;
            box-sizing: border-box;
        }

        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 5px 0; /* Jarak antara tabel dan teks */
        }
        /* Hapus atau kurangi padding di tabel */
        th, td { 
            padding: 1px 0px; /* Padding minimal (0px horizontal) */
            vertical-align: top;
        }
        th { text-align: left; }
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

        <table>
            <thead>
                <tr>
                    <th class="left" style="width: 20%;" >BN</th> 
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
                        <td class="right">{{ number_format($item->buy_rate, 0,',','.') }}</td>
                        <td class="right">{{ number_format($item->total, 0,',','.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p class="right bold" style="margin-top: 10px;">
            TOTAL: {{ number_format($transaction->total_amount, 0,',','.') }}
        </p>

        <div class="center" style="margin-top: 10px;">
            -------------------------<br>
            Terima Kasih
        </div>
    </div> </body>
</html>