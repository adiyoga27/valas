<x-filament::page>
    {{ $this->form }}

    @if ($searched)
        <div class="mt-6">
            @if (empty($mutations))
                <div class="p-4 rounded-lg bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-300 text-sm">
                    Tidak ada data mutasi untuk filter yang dipilih.
                </div>
            @else
                {{-- Summary Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    @foreach ($balances as $code => $balance)
                        @php
                            $activeRates = \App\Models\Currency::where('is_active', true)->pluck('buy_rate', 'code');
                            $rate = $activeRates[$code] ?? 0;
                            $val = $balance * $rate;
                        @endphp
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-4">
                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $code }}</div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white mt-1">
                                {{ number_format($balance, 2, ',', '.') }}
                            </div>
                            <div class="text-xs text-gray-400 mt-1">
                                Rate: Rp {{ number_format($rate, 2, ',', '.') }} &middot;
                                Val: Rp {{ number_format($val, 2, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-white/5 text-gray-600 dark:text-gray-400 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3">Mata Uang</th>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">No. Transaksi</th>
                                <th class="px-4 py-3 text-right">Buy</th>
                                <th class="px-4 py-3 text-right">Sell</th>
                                <th class="px-4 py-3 text-right">Rate</th>
                                <th class="px-4 py-3 text-right">Stock Balance</th>
                                <th class="px-4 py-3 text-right">Valuation (IDR)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                            @foreach ($mutations as $m)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                    <td class="px-4 py-2.5 font-medium text-gray-900 dark:text-white">
                                        <span @class([
                                            'inline-flex items-center gap-1',
                                            'text-green-600' => $m['type'] === 'BUY',
                                            'text-red-600' => $m['type'] === 'SELL',
                                        ])>
                                            {{ $m['currency_code'] }}
                                            <span class="text-[10px] opacity-60">({{ $m['currency_name'] }})</span>
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-gray-600 dark:text-gray-300">{{ $m['date'] }}</td>
                                    <td class="px-4 py-2.5">
                                        <span class="font-mono text-xs text-gray-500">{{ $m['trx_code'] }}</span>
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-mono text-green-600">
                                        {{ $m['buy'] }}
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-mono text-red-600">
                                        {{ $m['sell'] }}
                                    </td>
                                    <td class="px-4 py-2.5 text-right text-gray-600 dark:text-gray-300">
                                        {{ $m['rate'] }}
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-semibold text-gray-900 dark:text-white">
                                        {{ number_format($m['stock'], 2, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-2.5 text-right text-gray-500">
                                        Rp {{ number_format($m['valuation'], 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 text-xs text-gray-400">
                    {{ count($mutations) }} data ditemukan
                </div>
            @endif
        </div>
    @endif
</x-filament::page>
