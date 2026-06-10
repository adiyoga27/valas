@props(['headers' => [], 'actions' => false, 'emptyText' => 'Belum ada data.'])

<div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead>
                <tr class="bg-gray-50/50">
                    @foreach($headers as $header)
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $header }}</th>
                    @endforeach
                    @if($actions)
                        <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-10">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @if(trim($slot) !== '')
                    {{ $slot }}
                @else
                    <tr>
                        <td colspan="{{ count($headers) + ($actions ? 1 : 0) }}" class="px-5 py-12 text-center">
                            <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            <p class="mt-3 text-sm font-medium text-gray-500">{{ $emptyText }}</p>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
