@props(['active' => false, 'href' => '#'])

<a href="{{ $href }}"
   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
          {{ $active
              ? 'bg-amber-50 text-amber-700'
              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
    {{ $slot }}
</a>
