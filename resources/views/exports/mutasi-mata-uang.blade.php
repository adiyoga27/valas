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
        .header { margin-bottom: 20px; text-align: center; }
        .header h2 { margin: 0 0 5px 0; text-decoration: underline; font-size: 14px; }
        .header p { margin: 0; color: #333; font-weight: bold; }
        .currency-title { margin-bottom: 5px; font-size: 13px; font-weight: bold; }
        
        .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; border: none; }
        .kop-table td, .kop-table th { border: none; padding: 0; vertical-align: top; }
        .kop-title { font-size: 15px; font-weight: bold; margin: 0; text-align: center; }
        .kop-subtitle { font-size: 11px; text-align: center; margin: 0; }
        .kop-address { font-size: 10px; text-align: center; margin-top: 2px; }
        .divider { border-bottom: 2px solid #000; margin-bottom: 15px; }
    </style>
</head>
<body>
    @if(isset($isPdf) && $isPdf && isset($office))
        <table class="kop-table">
            <tr>
                <td width="20%" style="text-align: left;">
                    <img src="{{ public_path('assets/images/logo_kop.png') }}" style="max-width: 200px; max-height: 100px; display: block; margin-top: -5px;">
                </td>
                <td width="60%" style="text-align: center;">
                    <div class="kop-title">Authorized Money Changer</div>
                    <div class="kop-subtitle">(Pedagang Valuta Asing)</div>
                    <div class="kop-address">
                        {{ $office->address ?? '' }}<br>
                        Phone / WA : {{ $office->phone ?? '' }}<br>
                        E-mail : monicasejahtera24@gmail.com
                    </div>
                </td>
                <td width="20%"></td>
            </tr>
        </table>
        <div class="divider"></div>
    @endif

    <div class="header">
        <h2>LAPORAN MUTASI MATA UANG</h2>
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
