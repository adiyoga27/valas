@props(['id' => 'modal', 'title' => '', 'maxWidth' => 'max-w-lg'])

<div x-data="{ open: @json($attributes->get('show') ?? false) }"
     x-on:open-modal.window="if ($event.detail.id === '{{ $id }}') open = true"
     x-on:close-modal.window="if ($event.detail.id === '{{ $id }}') open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="open" x-on:click="$dispatch('close-modal', { id: '{{ $id }}' })"
             class="fixed inset-0 bg-gray-900/50 transition-opacity"></div>
        <div x-show="open" class="relative w-full {{ $maxWidth }} bg-white rounded-xl shadow-xl">
            @if($title)
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                    <h3 class="text-base font-semibold text-gray-900">{{ $title }}</h3>
                    <button x-on:click="$dispatch('close-modal', { id: '{{ $id }}' })"
                            class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif
            <div class="p-5">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
