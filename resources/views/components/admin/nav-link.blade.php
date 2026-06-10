@props(['active' => false, 'href' => '#'])

<a href="{{ $href }}"
   class="group flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors
          {{ $active
              ? 'bg-gray-800 text-white'
              : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
    {{ $slot }}
</a>
