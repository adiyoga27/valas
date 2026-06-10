{{-- Shared sidebar navigation --}}
<nav class="flex-1 overflow-y-auto py-5 px-4 space-y-6">
    <div>
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                  {{ request()->routeIs('admin.dashboard') ? 'bg-amber-50 text-amber-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
    </div>

    <div>
        <p class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-widest">Data Master</p>
        <div class="space-y-0.5">
            <x-admin.side-link href="{{ route('admin.currencies.index') }}" :active="request()->routeIs('admin.currencies.*')">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Mata Uang
            </x-admin.side-link>
            <x-admin.side-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                Pengguna
            </x-admin.side-link>
            <x-admin.side-link href="{{ route('admin.office.setting') }}" :active="request()->routeIs('admin.office.*')">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Setting Kantor
            </x-admin.side-link>
        </div>
    </div>

    <div>
        <p class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-widest">Transaksi</p>
        <div class="space-y-0.5">
            <x-admin.side-link href="{{ route('admin.buy-transactions.index') }}" :active="request()->routeIs('admin.buy-transactions.*')">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                Beli Valas
            </x-admin.side-link>
            <x-admin.side-link href="{{ route('admin.sell-transactions.index') }}" :active="request()->routeIs('admin.sell-transactions.*')">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Jual Valas
            </x-admin.side-link>
            <x-admin.side-link href="{{ route('admin.reports.mutation') }}" :active="request()->routeIs('admin.reports.mutation')">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                Mutasi Mata Uang
            </x-admin.side-link>
        </div>
    </div>

    <div>
        <p class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-widest">Report</p>
        <div class="space-y-0.5">
            <x-admin.side-link href="{{ route('admin.reports.buy') }}" :active="request()->routeIs('admin.reports.buy')">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Report Pembelian
            </x-admin.side-link>
            <x-admin.side-link href="{{ route('admin.reports.sell') }}" :active="request()->routeIs('admin.reports.sell')">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Report Penjualan
            </x-admin.side-link>
            <x-admin.side-link href="{{ route('admin.activities.index') }}" :active="request()->routeIs('admin.activities.*')">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Activity Log
            </x-admin.side-link>
        </div>
    </div>

    <div>
        <p class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-widest">PEP & DTTOT</p>
        <div class="space-y-0.5">
            <x-admin.side-link href="{{ route('admin.sanco.datasets.index') }}" :active="request()->routeIs('admin.sanco.datasets.*')">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Dataset
            </x-admin.side-link>
            <x-admin.side-link href="{{ route('admin.sanco.check') }}" :active="request()->routeIs('admin.sanco.check')">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Cek Nama
            </x-admin.side-link>
        </div>
    </div>
</nav>
