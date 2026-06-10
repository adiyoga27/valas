<x-filament::page>
    {{ $this->form }}

    @if ($searched)
        <div class="mt-6">
            @if (empty($groupedMutations))
                <div class="p-6 rounded-xl bg-white dark:bg-gray-800 ring-1 ring-gray-950/5 shadow-sm text-gray-500 text-center">
                    <x-heroicon-o-inbox class="w-12 h-12 mx-auto text-gray-400 mb-2"/>
                    Tidak ada data mutasi untuk filter yang dipilih.
                </div>
            @else
                {{-- Table --}}
                <div class="overflow-hidden rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left" style="border-collapse: collapse; min-width: 1000px;">
                            <thead class="bg-gray-50/50 dark:bg-white/5 text-xs uppercase tracking-wider" style="color: #6b7280; border-bottom: 1px solid #e5e7eb;">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">FOREX</th>
                                    <th class="px-6 py-4 font-semibold">DATE</th>
                                    <th class="px-6 py-4 font-semibold">NUMBER</th>
                                    <th class="px-6 py-4 font-semibold">BRANCH</th>
                                    <th class="px-6 py-4 text-right font-semibold">BUY</th>
                                    <th class="px-6 py-4 text-right font-semibold">SELL</th>
                                    <th class="px-6 py-4 text-right font-semibold">RATE</th>
                                    <th class="px-6 py-4 text-right font-semibold">STOCK BALANCE</th>
                                    <th class="px-6 py-4 text-right font-semibold">VALUATION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($groupedMutations as $code => $group)
                                    {{-- Transaction Items --}}
                                    @foreach ($group['items'] as $item)
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors" style="border-bottom: 1px solid #f3f4f6;">
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $code }}</td>
                                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $item['date'] }}</td>
                                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300 font-mono text-xs">{{ $item['trx_code'] }}</td>
                                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $item['customer'] }}</td>
                                            <td class="px-6 py-4 text-right font-medium {{ $item['buy'] > 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-400' }}">
                                                {{ $item['buy'] > 0 ? number_format($item['buy'], 0, ',', '.') : '0' }}
                                            </td>
                                            <td class="px-6 py-4 text-right font-medium {{ $item['sell'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-400' }}">
                                                {{ $item['sell'] > 0 ? number_format($item['sell'], 0, ',', '.') : '0' }}
                                            </td>
                                            <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">
                                                Rp {{ number_format($item['rate'], 2, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-white">
                                                {{ number_format($item['stock'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 text-right text-gray-600 dark:text-gray-400">
                                                Rp {{ number_format($item['valuation'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach

                                    {{-- End Balance Row --}}
                                    <tr class="bg-gray-50/50 dark:bg-white/5" style="border-bottom: 2px solid #e5e7eb;">
                                        <td class="px-6 py-5 font-bold text-gray-900 dark:text-white">{{ $code }}</td>
                                        <td class="px-6 py-5"></td>
                                        <td class="px-6 py-5 text-gray-700 dark:text-gray-300 font-bold tracking-wide uppercase text-xs">End Balance</td>
                                        <td class="px-6 py-5"></td>
                                        <td class="px-6 py-5 text-right font-bold text-gray-900 dark:text-white">
                                            {{ number_format($group['total_buy'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-5 text-right font-bold text-gray-900 dark:text-white">
                                            {{ number_format($group['total_sell'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-5 text-right text-gray-600 dark:text-gray-400">
                                            Rp {{ number_format($group['rate'], 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-5 text-right font-bold text-emerald-600 dark:text-emerald-400 text-base">
                                            {{ number_format($group['current_stock'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-5 text-right font-bold text-emerald-600 dark:text-emerald-400 text-base">
                                            Rp {{ number_format($group['current_stock'] * $group['rate'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 flex justify-end font-medium text-sm text-gray-500 dark:text-gray-400">
                    Total Records: <span class="ml-2 font-bold text-gray-900 dark:text-white">{{ $totalRecords }}</span>
                </div>
            @endif
        </div>
    @endif
</x-filament::page>
