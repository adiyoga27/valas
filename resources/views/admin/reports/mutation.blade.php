@extends('layouts.admin')
@section('title', 'Mutasi Mata Uang')
@section('heading', 'Mutasi Mata Uang')
@section('content')
<div class="space-y-4">
    <form method="GET" class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Tgl Awal</label><input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></div>
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Tgl Akhir</label><input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></div>
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Mata Uang</label><select name="currency_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"><option value="">Semua</option>@foreach($currencies as $c)<option value="{{ $c->id }}" {{ $currencyId == $c->id ? 'selected' : '' }}>{{ $c->code }} - {{ $c->name }}</option>@endforeach</select></div>
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Keyword</label><input type="text" name="keyword" value="{{ $keyword }}" placeholder="No. Trx / Nama..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"></div>
        </div>
        <button type="submit" class="mt-3 rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700 transition-colors">Submit Filter</button>
    </form>

    @if($searched && empty($groupedMutations))
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-8 text-center text-gray-500">Tidak ada data mutasi untuk filter yang dipilih.</div>
    @endif

    @if(!empty($groupedMutations))
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b">
                        <tr><th class="px-4 py-3 font-semibold text-left">FOREX</th><th class="px-4 py-3 font-semibold text-left">DATE</th><th class="px-4 py-3 font-semibold text-left">NUMBER</th><th class="px-4 py-3 font-semibold text-left">BRANCH</th><th class="px-4 py-3 font-semibold text-right">BUY</th><th class="px-4 py-3 font-semibold text-right">SELL</th><th class="px-4 py-3 font-semibold text-right">RATE</th><th class="px-4 py-3 font-semibold text-right">STOCK</th><th class="px-4 py-3 font-semibold text-right">VALUATION</th></tr>
                    </thead>
                    <tbody>
                        @foreach($groupedMutations as $code => $group)
                            @foreach($group['items'] as $item)
                                <tr class="hover:bg-gray-50 border-b border-gray-100">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $code }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $item['date'] }}</td>
                                    <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $item['trx_code'] }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $item['customer'] }}</td>
                                    <td class="px-4 py-3 text-right font-medium {{ $item['buy'] > 0 ? 'text-green-600' : 'text-gray-400' }}">{{ $item['buy'] > 0 ? number_format($item['buy'], 0, ',', '.') : '0' }}</td>
                                    <td class="px-4 py-3 text-right font-medium {{ $item['sell'] > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $item['sell'] > 0 ? number_format($item['sell'], 0, ',', '.') : '0' }}</td>
                                    <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($item['rate'], 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ number_format($item['stock'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($item['valuation'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-50 border-b-2 border-gray-200">
                                <td class="px-4 py-3 font-bold text-gray-900">{{ $code }}</td><td class="px-4 py-3"></td><td class="px-4 py-3 font-bold uppercase text-xs">End Balance</td><td class="px-4 py-3"></td>
                                <td class="px-4 py-3 text-right font-bold">{{ number_format($group['total_buy'], 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-bold">{{ number_format($group['total_sell'], 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right">Rp {{ number_format($group['rate'], 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-bold text-emerald-600">{{ number_format($group['current_stock'], 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-bold text-emerald-600">Rp {{ number_format($group['current_stock'] * $group['rate'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="text-right text-sm text-gray-500">Total Records: <span class="font-bold text-gray-900">{{ $totalRecords }}</span></div>
    @endif
</div>
@endsection
