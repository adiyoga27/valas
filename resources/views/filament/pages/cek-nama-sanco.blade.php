<x-filament::page>
    @if ($this->getTotalEntities() === 0)
        <div class="mb-4 p-4 rounded-lg bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400 border border-warning-200 dark:border-warning-500/20">
            <p class="font-semibold">Belum ada data entitas yang diimport.</p>
            <p class="text-sm mt-1">Klik tombol <strong>Import Data</strong> di atas atau buka halaman <a href="{{ \App\Filament\Resources\SancoDatasets\SancoDatasetResource::getUrl('index') }}" class="underline">Dataset</a> lalu klik <strong>Import Entitas</strong> untuk mendownload data PEP & Sanctions.</p>
        </div>
    @else
        <div class="mb-4 text-sm text-gray-500 dark:text-gray-400">
            {{ number_format($this->getTotalEntities(), 0, ',', '.') }} entitas tersedia di database lokal.
        </div>
    @endif

    {{ $this->form }}

    @if ($searched)
        <div class="mt-4">
            @if ($error)
                <div class="p-4 rounded-lg bg-danger-50 text-danger-600 dark:bg-danger-500/10 dark:text-danger-400">{{ $error }}</div>
            @elseif (filled($results))
                <div class="fi-ta-ctn divide-y divide-gray-200 dark:divide-white/10 overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
                    <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                        <thead class="divide-y divide-gray-200 dark:divide-white/5">
                            <tr class="bg-gray-50 dark:bg-white/5">
                                <th class="px-4 py-3 text-sm font-semibold text-gray-950 dark:text-white">Name</th>
                                <th class="px-4 py-3 text-sm font-semibold text-gray-950 dark:text-white">Tags</th>
                                <th class="px-4 py-3 text-sm font-semibold text-gray-950 dark:text-white">Date Birth</th>
                                <th class="px-4 py-3 text-sm font-semibold text-gray-950 dark:text-white w-10">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                            @foreach ($results as $i => $row)
                                <tr class="transition hover:bg-gray-50 dark:hover:bg-white/5">
                                    <td class="px-4 py-2.5 text-sm font-medium text-gray-950 dark:text-white">
                                        {{ $row['caption'] }}
                                        <span class="text-xs text-gray-400 ml-1">({{ $row['schema'] }})</span>
                                    </td>
                                    <td class="px-4 py-2.5 text-sm">
                                        @php
                                            $tags = collect(explode(', ', $row['datasets']))
                                                ->filter()
                                                ->unique()
                                                ->values();
                                        @endphp
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($tags as $tag)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                    @if(str_contains(strtolower($tag), 'pep')) bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400
                                                    @elseif(str_contains(strtolower($tag), 'terror') || str_contains(strtolower($tag), 'sanction') || str_contains(strtolower($tag), 'ofac') || str_contains(strtolower($tag), 'fsf'))
                                                        bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400
                                                    @else
                                                        bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-400
                                                    @endif
                                                ">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                        {{ $row['birth_date'] !== '-' ? $row['birth_date'] : '-' }}
                                    </td>
                                    <td class="px-4 py-2.5 text-sm">
                                        <button type="button" wire:click="showDetail({{ $i }})"
                                            class="text-primary-600 dark:text-primary-400 hover:underline text-sm font-medium cursor-pointer">
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    {{ count($results) }} hasil @if($source) ({{ $source }}) @endif
                </div>
            @else
                <div class="p-4 rounded-lg bg-gray-50 text-gray-600 dark:bg-gray-500/10 dark:text-gray-400">
                    Tidak ada hasil untuk "{{ $keyword }}".
                </div>
            @endif
        </div>
    @endif

    {{-- Detail Modal --}}
    @if ($selectedEntity)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(2px);" wire:click.self="closeDetail">
            <div class="w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl shadow-2xl ring-1 ring-black/5 dark:ring-white/10 overflow-hidden" @click.stop="">
                {{-- Header --}}
                <div class="relative bg-gradient-to-br from-primary-500 to-primary-700 dark:from-primary-600 dark:to-primary-900 px-6 py-5">
                    <button type="button" wire:click="closeDetail" class="absolute top-4 right-4 p-1 rounded-full bg-white/20 hover:bg-white/30 text-white transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </button>

                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-lg">
                            {{ strtoupper(substr($selectedEntity['caption'], 0, 2)) }}
                        </div>
                        <div class="text-white">
                            <h2 class="text-lg font-bold leading-tight">{{ $selectedEntity['caption'] }}</h2>
                            <div class="flex items-center gap-2 mt-1">
                                @php
                                    $sc = $selectedEntity['schema'];
                                    $scColor = match ($sc) { 'Person' => 'blue', 'Organization','Company' => 'amber', default => 'slate' };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-white/20 text-white">
                                    {{ $sc }}
                                </span>
                                @if ($selectedEntity['country'] !== '-')
                                    <span class="text-xs text-white/70">{{ strtoupper($selectedEntity['country']) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Risk Tags --}}
                <div class="px-6 py-3 flex flex-wrap gap-1.5 border-b border-gray-100 dark:border-white/5">
                    @php
                        $tags = collect(explode(', ', $selectedEntity['datasets']))
                            ->filter()->unique()->values();
                    @endphp
                    @foreach ($tags as $tag)
                        @php
                            $tl = strtolower($tag);
                            $cls = match(true) {
                                str_contains($tl, 'pep') => 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400',
                                str_contains($tl, 'sanction') || str_contains($tl, 'ofac') || str_contains($tl, 'fsf') || str_contains($tl, 'terror') => 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-400',
                                default => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold {{ $cls }}">{{ $tag }}</span>
                    @endforeach
                </div>

                {{-- Details --}}
                <div class="px-6 py-4 space-y-3 max-h-[40vh] overflow-y-auto">
                    @if ($selectedEntity['birth_date'] !== '-')
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Birth Date</div>
                            <div class="text-sm text-gray-900 dark:text-white mt-0.5">{{ $selectedEntity['birth_date'] }}</div>
                        </div>
                    </div>
                    @endif

                    @if ($selectedEntity['aliases'] || $selectedEntity['weak_aliases'])
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" /></svg>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Alias</div>
                            @if ($selectedEntity['aliases'])
                                <div class="text-sm text-gray-900 dark:text-white mt-0.5">{{ $selectedEntity['aliases'] }}</div>
                            @endif
                            @if ($selectedEntity['weak_aliases'])
                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                    <span class="text-xs italic">Weak:</span> {{ $selectedEntity['weak_aliases'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if ($selectedEntity['identifiers'])
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" /></svg>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Identifiers</div>
                            <div class="text-sm text-gray-900 dark:text-white mt-0.5 break-all">{{ $selectedEntity['identifiers'] }}</div>
                        </div>
                    </div>
                    @endif

                    @if ($selectedEntity['addresses'])
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Address</div>
                            <div class="text-sm text-gray-900 dark:text-white mt-0.5">{{ $selectedEntity['addresses'] }}</div>
                        </div>
                    </div>
                    @endif

                    @if ($selectedEntity['emails'])
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</div>
                            <div class="text-sm text-gray-900 dark:text-white mt-0.5">{{ $selectedEntity['emails'] }}</div>
                        </div>
                    </div>
                    @endif

                    @if ($selectedEntity['dataset_title'])
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125v-3.75" /></svg>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dataset</div>
                            <div class="text-sm text-gray-900 dark:text-white mt-0.5">{{ $selectedEntity['dataset_title'] }}</div>
                        </div>
                    </div>
                    @endif

                    @if ($selectedEntity['last_change'])
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182" /></svg>
                        <div>
                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Update</div>
                            <div class="text-sm text-gray-900 dark:text-white mt-0.5">{{ \Carbon\Carbon::parse($selectedEntity['last_change'])->format('d M Y') }}</div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/[0.02]">
                    <a href="{{ $selectedEntity['opensanctions_url'] }}" target="_blank" rel="noopener"
                       class="inline-flex items-center justify-center w-full gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 transition">
                        Open Full Profile
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                    </a>
                </div>
            </div>
        </div>
    @endif
</x-filament::page>
