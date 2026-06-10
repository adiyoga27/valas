@extends('layouts.admin')
@section('title', 'Data Pengguna')
@section('heading', 'Data Master Pengguna')
@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
    <form method="GET" class="flex flex-col sm:flex-row gap-2">
        <div class="relative">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/email..." class="w-full sm:w-56 rounded-lg border border-gray-300 pl-9 pr-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
            <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <select name="role" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
            <option value="">Semua Role</option>
            @foreach(['admin' => 'Admin', 'staff' => 'Staff', 'kasir' => 'Kasir', 'manager' => 'Manager'] as $v => $l)
                <option value="{{ $v }}" {{ request('role') == $v ? 'selected' : '' }}>{{ $l }}</option>
            @endforeach
        </select>
    </form>
    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah
    </a>
</div>

<x-admin.table :headers="['Nama', 'Email', 'Role', 'Dibuat']" :actions="true">
    @forelse($users as $user)
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $user->name }}</td>
            <td class="px-4 py-3 text-sm text-gray-600">{{ $user->email }}</td>
            <td class="px-4 py-3">
                @php $colors = ['admin' => 'bg-green-100 text-green-700', 'staff' => 'bg-blue-100 text-blue-700', 'kasir' => 'bg-yellow-100 text-yellow-700', 'manager' => 'bg-purple-100 text-purple-700']; @endphp
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $colors[$user->role] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($user->role) }}</span>
            </td>
            <td class="px-4 py-3 text-sm text-gray-500">{{ $user->created_at->format('d M Y H:i') }}</td>
            <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('admin.users.show', $user) }}" class="text-gray-400 hover:text-amber-600" title="Lihat"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>
                    <a href="{{ route('admin.users.edit', $user) }}" class="text-gray-400 hover:text-blue-600" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                    @if(auth()->id() !== $user->id)
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus {{ $user->name }}?')">
                            @csrf @method('DELETE')
                            <button class="text-gray-400 hover:text-red-600" title="Hapus"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                        </form>
                    @endif
                </div>
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada data pengguna.</td></tr>
    @endforelse
</x-admin.table>
<div class="mt-4">{{ $users->links() }}</div>
@endsection
