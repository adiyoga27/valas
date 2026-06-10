<!DOCTYPE html>
<html>
<head>
    <title>Mutasi Mata Uang</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; vertical-align: top; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .header { margin-bottom: 20px; }
        .header h2 { margin: 0 0 5px 0; }
        .header p { margin: 0; color: #555; }
        .currency-title { margin-bottom: 5px; font-size: 14px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Mutasi Mata Uang</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
    </div>

    @if(empty($groupedMutations))
        <p>Tidak ada data mutasi untuk periode ini.</p>
    @else
        @foreach ($groupedMutations as $code => $group)
            <div class="currency-title">{{ $code }} - {{ $group['currency_name'] }} (Rate: Rp {{ number_format($group['rate'], 2, ',', '.') }})</div>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Trx Number</th>
                        <th>Branch/Customer</th>
                        <th class="text-right">Buy</th>
                        <th class="text-right">Sell</th>
                        <th class="text-right">Rate</th>
                        <th class="text-right">Stock</th>
                        <th class="text-right">Valuation</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($group['items'] as $item)
                        <tr>
                            <td>{{ $item['date'] }}</td>
                            <td>{{ $item['trx_code'] }}</td>
                            <td>{{ $item['customer'] }}</td>
                            <td class="text-right">{{ $item['buy'] > 0 ? number_format($item['buy'], 0, ',', '.') : '-' }}</td>
                            <td class="text-right">{{ $item['sell'] > 0 ? number_format($item['sell'], 0, ',', '.') : '-' }}</td>
                            <td class="text-right">Rp {{ number_format($item['rate'], 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item['stock'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item['valuation'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="3" class="font-bold text-right">Total Keseluruhan</td>
                        <td class="font-bold text-right">{{ number_format($group['total_buy'], 0, ',', '.') }}</td>
                        <td class="font-bold text-right">{{ number_format($group['total_sell'], 0, ',', '.') }}</td>
                        <td></td>
                        <td class="font-bold text-right">{{ number_format($group['current_stock'], 0, ',', '.') }}</td>
                        <td class="font-bold text-right">Rp {{ number_format($group['current_stock'] * $group['rate'], 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        @endforeach
    @endif
</body>
</html>
