<table>
    <tr>
        <td colspan="9" style="text-align: center; font-weight: bold; font-size: 14pt;">PT. MONICA SEJAHTERA</td>
    </tr>
    <tr>
        <td colspan="9" style="text-align: center; font-weight: bold; font-size: 12pt;">PENATAUSAHAAN NASABAH</td>
    </tr>
    <tr>
        <td colspan="9" style="text-align: center; padding-bottom: 0;">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</td>
    </tr>
    <tr>
        <td colspan="9" style="text-align: center; font-weight: bold; font-size: 12pt; padding-top: 0; padding-bottom: 0;">PEMANTAUAN TRANSAKSI</td>
    </tr>
    <thead>
        <tr>
            <th style="border: 1px solid #000; background: #f2f2f2; width: 120px;">TANGGAL</th>
            <th style="border: 1px solid #000; background: #f2f2f2; width: 130px;">NOMOR NOTA</th>
            <th style="border: 1px solid #000; background: #f2f2f2; width: 80px;">MATA UANG</th>
            <th style="border: 1px solid #000; background: #f2f2f2; width: 100px;">JUMLAH UKA</th>
            <th style="border: 1px solid #000; background: #f2f2f2; width: 100px;">RATE/KURS</th>
            <th style="border: 1px solid #000; background: #f2f2f2; width: 120px;">JUMLAH RUPIAH</th>
            <th style="border: 1px solid #000; background: #f2f2f2; width: 150px;">NAMA NASABAH</th>
            <th style="border: 1px solid #000; background: #f2f2f2; width: 200px;">ALAMAT</th>
            <th style="border: 1px solid #000; background: #f2f2f2; width: 100px;">PASSPORT/KTP</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr>
            <td style="border: 1px solid #000;">{{ $item->transaction?->created_at?->format('d/m/Y H:i') }}</td>
            <td style="border: 1px solid #000;">{{ $item->transaction?->transaction_code }}</td>
            <td style="border: 1px solid #000;">{{ $item->currency_code }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ number_format((float) $item->qty, 2, ',', '.') }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ number_format((float) $item->buy_rate, 2, ',', '.') }}</td>
            <td style="border: 1px solid #000; text-align: right;">{{ number_format((float) $item->total, 2, ',', '.') }}</td>
            <td style="border: 1px solid #000;">{{ $item->transaction?->customer_name }}</td>
            <td style="border: 1px solid #000;">{{ $item->transaction?->customer_address }}</td>
            <td style="border: 1px solid #000;">{{ $item->transaction?->passport_number }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
