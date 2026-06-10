@extends('layouts.admin')
@section('title', 'Data Mata Uang')
@section('heading', 'Data Master Mata Uang')
@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
    <form method="GET" class="relative">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari mata uang..." class="w-full sm:w-64 rounded-lg border border-gray-300 pl-9 pr-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
        <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </form>
    <a href="{{ route('admin.currencies.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah
    </a>
</div>

<x-admin.table :headers="['Kode', 'Nama', 'Kode Negara', 'Flag', 'Kurs Beli', 'Kurs Jual', 'Status']" :actions="true">
    @forelse($currencies as $currency)
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $currency->code }}</td>
            <td class="px-4 py-3 text-sm text-gray-600">{{ $currency->name }}</td>
            <td class="px-4 py-3 text-sm text-gray-600">{{ $currency->country_code }}</td>
            <td class="px-4 py-3">
                @if($currency->flag)
                    <img src="{{ asset('storage/'.$currency->flag) }}" class="h-8 w-8 rounded object-cover">
                @endif
            </td>
            <td class="px-4 py-3 text-sm text-gray-600">Rp {{ number_format($currency->buy_rate, 0, ',', '.') }}</td>
            <td class="px-4 py-3 text-sm text-gray-600">Rp {{ number_format($currency->sell_rate, 0, ',', '.') }}</td>
            <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $currency->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ $currency->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </td>
            <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('admin.currencies.show', $currency) }}" class="text-gray-400 hover:text-amber-600" title="Lihat">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a>
                    <a href="{{ route('admin.currencies.edit', $currency) }}" class="text-gray-400 hover:text-blue-600" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    <form method="POST" action="{{ route('admin.currencies.destroy', $currency) }}" onsubmit="return confirm('Hapus {{ $currency->code }}?')">
                        @csrf @method('DELETE')
                        <button class="text-gray-400 hover:text-red-600" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
    @empty
        <tr><td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada data mata uang.</td></tr>
    @endforelse
</x-admin.table>
<div class="mt-4">{{ $currencies->links() }}</div>
@endsection
