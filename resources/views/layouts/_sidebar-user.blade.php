{{-- Shared sidebar user info + logout --}}
<div class="flex items-center gap-3">
    <div class="h-9 w-9 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
        <span class="text-amber-700 font-semibold text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
    </div>
    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Logout">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        </button>
    </form>
</div>
