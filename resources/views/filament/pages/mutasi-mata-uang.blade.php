<x-filament::page>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: {
                preflight: false, // Nonaktifkan preflight agar tidak merusak style bawaan Filament
            }
        }
    </script>

    {{ $this->form }}

    @if ($searched)
        <div class="mt-8 space-y-6">
            @if (empty($groupedMutations))
                <div class="p-8 rounded-2xl bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-white/10 shadow-sm flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                        <x-heroicon-o-inbox class="w-8 h-8 text-gray-400 dark:text-gray-500"/>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Tidak Ada Data</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm">
                        Kami tidak menemukan data mutasi untuk filter yang Anda pilih. Silakan coba mengubah parameter filter.
                    </p>
                </div>
            @else
                @foreach ($groupedMutations as $code => $group)
                    <div class="rounded-2xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-200 dark:ring-white/10 overflow-hidden transition-all hover:shadow-md">
                        {{-- Header / Summary Info --}}
                        <div class="p-5 sm:px-6 sm:py-5 border-b border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/5 flex flex-col xl:flex-row xl:items-center justify-between gap-6">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-full bg-primary-50 dark:bg-primary-500/10 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold text-xl shadow-sm ring-1 ring-primary-500/20">
                                    {{ $code }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white text-xl">{{ $group['currency_name'] ?? $code }}</h3>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1.5 mt-1">
                                        <x-heroicon-m-banknotes class="w-4 h-4 text-gray-400" />
                                        Rate: Rp {{ number_format($group['rate'], 2, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 xl:gap-6 w-full xl:w-auto">
                                <div class="bg-white dark:bg-gray-800 p-3.5 rounded-xl ring-1 ring-gray-200 dark:ring-white/10 shadow-sm xl:bg-transparent xl:p-0 xl:ring-0 xl:shadow-none flex flex-col justify-center">
                                    <p class="text-gray-500 dark:text-gray-400 text-[11px] font-bold uppercase tracking-wider mb-1">Total Buy</p>
                                    <p class="font-bold text-gray-900 dark:text-white text-lg">{{ number_format($group['total_buy'], 0, ',', '.') }}</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3.5 rounded-xl ring-1 ring-gray-200 dark:ring-white/10 shadow-sm xl:bg-transparent xl:p-0 xl:ring-0 xl:shadow-none flex flex-col justify-center">
                                    <p class="text-gray-500 dark:text-gray-400 text-[11px] font-bold uppercase tracking-wider mb-1">Total Sell</p>
                                    <p class="font-bold text-gray-900 dark:text-white text-lg">{{ number_format($group['total_sell'], 0, ',', '.') }}</p>
                                </div>
                                <div class="bg-primary-50 dark:bg-primary-900/20 p-3.5 rounded-xl ring-1 ring-primary-200 dark:ring-primary-900/50 shadow-sm xl:bg-transparent xl:p-0 xl:ring-0 xl:shadow-none flex flex-col justify-center">
                                    <p class="text-primary-600 dark:text-primary-400 text-[11px] font-bold uppercase tracking-wider mb-1">Stock Balance</p>
                                    <p class="font-bold text-primary-700 dark:text-primary-300 text-lg">{{ number_format($group['current_stock'], 0, ',', '.') }}</p>
                                </div>
                                <div class="bg-primary-50 dark:bg-primary-900/20 p-3.5 rounded-xl ring-1 ring-primary-200 dark:ring-primary-900/50 shadow-sm xl:bg-transparent xl:p-0 xl:ring-0 xl:shadow-none flex flex-col justify-center">
                                    <p class="text-primary-600 dark:text-primary-400 text-[11px] font-bold uppercase tracking-wider mb-1">Valuation</p>
                                    <p class="font-bold text-primary-700 dark:text-primary-300 text-lg">Rp {{ number_format($group['current_stock'] * $group['rate'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Desktop Table View --}}
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-sm text-left border-collapse">
                                <thead class="bg-white dark:bg-gray-900 text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-white/10">
                                    <tr>
                                        <th class="px-6 py-4">Date</th>
                                        <th class="px-6 py-4">Trx Number</th>
                                        <th class="px-6 py-4">Branch/Customer</th>
                                        <th class="px-6 py-4 text-right">Buy</th>
                                        <th class="px-6 py-4 text-right">Sell</th>
                                        <th class="px-6 py-4 text-right">Rate</th>
                                        <th class="px-6 py-4 text-right">Stock</th>
                                        <th class="px-6 py-4 text-right">Valuation</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-white/5 bg-white dark:bg-gray-900">
                                    @foreach ($group['items'] as $item)
                                        <tr class="hover:bg-gray-50/80 dark:hover:bg-white/5 transition-colors group">
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400">{{ $item['date'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-mono text-xs font-semibold ring-1 ring-gray-200/50 dark:ring-white/10">
                                                    {{ $item['trx_code'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">{{ $item['customer'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right font-semibold {{ $item['buy'] > 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-300 dark:text-gray-600 font-normal' }}">
                                                {{ $item['buy'] > 0 ? number_format($item['buy'], 0, ',', '.') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right font-semibold {{ $item['sell'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-300 dark:text-gray-600 font-normal' }}">
                                                {{ $item['sell'] > 0 ? number_format($item['sell'], 0, ',', '.') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-gray-500 dark:text-gray-400">
                                                Rp {{ number_format($item['rate'], 2, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-gray-900 dark:text-white">
                                                {{ number_format($item['stock'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-gray-600 dark:text-gray-300 font-semibold">
                                                Rp {{ number_format($item['valuation'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile Card View --}}
                        <div class="md:hidden divide-y divide-gray-100 dark:divide-white/5">
                            @foreach ($group['items'] as $item)
                                <div class="p-5 hover:bg-gray-50/80 dark:hover:bg-white/5 transition-colors space-y-4">
                                    <div class="flex justify-between items-start gap-3">
                                        <div class="space-y-1.5">
                                            <div class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-mono text-xs font-semibold ring-1 ring-gray-200/50 dark:ring-white/10">
                                                {{ $item['trx_code'] }}
                                            </div>
                                            <p class="text-sm font-bold text-gray-900 dark:text-white leading-tight">{{ $item['customer'] }}</p>
                                        </div>
                                        <div class="text-right shrink-0 mt-1">
                                            <p class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 px-2 py-1 rounded-md">{{ $item['date'] }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-3 text-sm bg-gray-50/50 dark:bg-gray-800/50 p-4 rounded-xl ring-1 ring-gray-200/50 dark:ring-white/5">
                                        <div class="space-y-1">
                                            <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Buy</p>
                                            <p class="font-bold text-base {{ $item['buy'] > 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">{{ $item['buy'] > 0 ? number_format($item['buy'], 0, ',', '.') : '-' }}</p>
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sell</p>
                                            <p class="font-bold text-base {{ $item['sell'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-400 dark:text-gray-500' }}">{{ $item['sell'] > 0 ? number_format($item['sell'], 0, ',', '.') : '-' }}</p>
                                        </div>
                                        <div class="space-y-1 mt-1">
                                            <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Rate</p>
                                            <p class="text-gray-700 dark:text-gray-300 font-medium">Rp {{ number_format($item['rate'], 2, ',', '.') }}</p>
                                        </div>
                                        <div class="space-y-1 mt-1">
                                            <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Stock</p>
                                            <p class="font-bold text-gray-900 dark:text-white">{{ number_format($item['stock'], 0, ',', '.') }}</p>
                                        </div>
                                        <div class="col-span-2 pt-3 mt-2 border-t border-gray-200 dark:border-white/10 flex justify-between items-center">
                                            <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Valuation</p>
                                            <p class="text-gray-900 dark:text-white font-bold text-base">Rp {{ number_format($item['valuation'], 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="mt-6 flex justify-end">
                    <div class="inline-flex items-center gap-3 px-5 py-3 bg-white dark:bg-gray-900 rounded-xl ring-1 ring-gray-200 dark:ring-white/10 shadow-sm text-sm">
                        <span class="text-gray-500 dark:text-gray-400 font-medium">Total Record Transaksi</span>
                        <div class="w-px h-4 bg-gray-200 dark:bg-white/10"></div>
                        <span class="font-bold text-primary-600 dark:text-primary-400 text-lg">{{ $totalRecords }}</span>
                    </div>
                </div>
            @endif
        </div>
    @endif
</x-filament::page>
