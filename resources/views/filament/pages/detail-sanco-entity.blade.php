<x-filament::page>
    @if ($entity)
        <div class="border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-900 mb-6">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-950 dark:text-white">{{ $entity['caption'] }}</h2>
                <div class="flex flex-wrap items-center gap-2 mt-1.5">
                    <x-filament::badge :color="match ($entity['schema']) { 'Person' => 'info', default => 'warning' }">{{ $entity['schema'] }}</x-filament::badge>
                    @if ($entity['country'] !== '-')<span class="text-sm text-gray-500">{{ strtoupper($entity['country']) }}</span>@endif
                    @if ($entity['birth_date'] !== '-')<span class="text-sm text-gray-400">&middot; {{ $entity['birth_date'] }}</span>@endif
                    @if ($entity['gender'])<span class="text-sm text-gray-400">&middot; {{ $entity['gender'] }}</span>@endif
                </div>
                <div class="flex items-center justify-between mt-3">
                    <div class="flex flex-wrap gap-1.5">
                        @foreach (collect(explode(', ', $entity['datasets']))->filter()->unique() as $tag)
                            <x-filament::badge :color="str_contains(strtolower($tag), 'pep') ? 'info' : 'danger'" size="sm">{{ $tag }}</x-filament::badge>
                        @endforeach
                    </div>
                    <a href="{{ $entity['opensanctions_url'] }}" target="_blank" class="text-sm text-primary-500 hover:underline shrink-0 ml-4">OpenSanctions &nearr;</a>
                </div>
            </div>
            @php $rows = array_filter(['Place of Birth' => $entity['birth_place'] ?? null, 'Nationality' => $entity['nationality'] ?? null, 'Position' => $entity['position'] ?? null, 'Notes' => $entity['notes'] ?? null, 'Address' => $entity['addresses'] ?? null, 'Email' => $entity['emails'] ?? null, 'Identifiers' => $entity['identifiers'] ?? null]); @endphp
            @if (!empty($rows))
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($rows as $label => $value)
                <div class="flex flex-col sm:flex-row sm:gap-8 px-6 py-3.5">
                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider sm:w-40 shrink-0">{{ $label }}</div>
                    <div class="text-sm text-gray-900 dark:text-white mt-1 sm:mt-0">{{ $value }}</div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        @if ($entity['aliases'] || $entity['weak_aliases'])
        <div class="border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-900 mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Names & Aliases</h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                @if ($entity['aliases'])
                <div>
                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Also Known As</div>
                    <div class="flex flex-wrap gap-1.5">@foreach (explode('; ', $entity['aliases']) as $a)<x-filament::badge color="gray" size="sm">{{ trim($a) }}</x-filament::badge>@endforeach</div>
                </div>
                @endif
                @if ($entity['weak_aliases'])
                <div>
                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Weak References</div>
                    <div class="flex flex-wrap gap-1.5">@foreach (explode('; ', $entity['weak_aliases']) as $a)<span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">{{ trim($a) }}</span>@endforeach</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <div class="border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-900">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Details</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-6 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Entity ID</th>
                            <th class="px-6 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">First Seen</th>
                            <th class="px-6 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Updated</th>
                            <th class="px-6 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dataset</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-6 py-2.5 text-gray-900 dark:text-white font-mono text-xs break-all">{{ $entity['id'] }}</td>
                            <td class="px-6 py-2.5 text-gray-900 dark:text-white">{{ $entity['first_seen'] ? \Carbon\Carbon::parse($entity['first_seen'])->format('d M Y') : '-' }}</td>
                            <td class="px-6 py-2.5 text-gray-900 dark:text-white">{{ $entity['last_change'] ? \Carbon\Carbon::parse($entity['last_change'])->format('d M Y') : '-' }}</td>
                            <td class="px-6 py-2.5 text-gray-900 dark:text-white">{{ $entity['dataset_title'] ?? $entity['datasets'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-20 text-center text-gray-500">
            <x-filament::icon icon="heroicon-o-magnifying-glass" class="w-12 h-12 mb-4 opacity-30" />
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Data tidak ditemukan</h3>
        </div>
    @endif
</x-filament::page>
