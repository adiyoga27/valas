<x-filament::page>
    @if ($this->getTotalEntities() === 0)
        <div class="mb-4 p-4 rounded-lg bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400 border border-warning-200 dark:border-warning-500/20">
            <p class="font-semibold">Belum ada data entitas yang diimport.</p>
            <p class="text-sm mt-1">Klik <strong>Import Data</strong> di atas atau buka <a href="{{ \App\Filament\Resources\SancoDatasets\SancoDatasetResource::getUrl('index') }}" class="underline">Dataset</a> lalu klik <strong>Import Entitas</strong>.</p>
        </div>
    @else
        <div class="mb-4 text-sm text-gray-500 dark:text-gray-400">
            {{ number_format($this->getTotalEntities(), 0, ',', '.') }} entitas tersedia.
        </div>
    @endif

    {{ $this->form }}

    @if ($searched)
        <div class="mt-4">
            @if ($error)
                <div class="p-4 rounded-lg bg-danger-50 text-danger-600 dark:bg-danger-500/10 dark:text-danger-400">{{ $error }}</div>
            @elseif (filled($results))
                <div class="fi-ta-ctn overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
                    <table class="fi-ta-table w-full table-auto text-start">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-white/5">
                                <th class="px-4 py-3 text-sm font-semibold text-gray-950 dark:text-white">Name</th>
                                <th class="px-4 py-3 text-sm font-semibold text-gray-950 dark:text-white">Tags</th>
                                <th class="px-4 py-3 text-sm font-semibold text-gray-950 dark:text-white">Date Birth</th>
                                <th class="px-4 py-3 text-sm font-semibold text-gray-950 dark:text-white w-10">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                            @foreach ($results as $row)
                                <tr class="transition hover:bg-gray-50 dark:hover:bg-white/5">
                                    <td class="px-4 py-2.5">
                                        <a href="{{ $row['detail_url'] }}" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:underline">
                                            {{ $row['caption'] }}
                                        </a>
                                        <span class="text-xs text-gray-400 ml-1">({{ $row['schema'] }})</span>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        @php $tags = collect(explode(', ', $row['datasets']))->filter()->unique()->values(); @endphp
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($tags as $tag)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                    @if(str_contains(strtolower($tag), 'pep')) bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400
                                                    @elseif(str_contains(strtolower($tag), 'terror') || str_contains(strtolower($tag), 'sanction') || str_contains(strtolower($tag), 'ofac') || str_contains(strtolower($tag), 'fsf'))
                                                        bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400
                                                    @else bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-400
                                                    @endif
                                                ">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                        {{ $row['birth_date'] !== '-' ? $row['birth_date'] : '-' }}
                                    </td>
                                    <td class="px-4 py-2.5 text-sm">
                                        <a href="{{ $row['detail_url'] }}" class="text-primary-600 dark:text-primary-400 text-sm font-medium hover:underline">Detail</a>
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
</x-filament::page>
