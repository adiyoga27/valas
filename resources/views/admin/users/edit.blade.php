@extends('layouts.admin')
@section('title', 'Edit Pengguna')
@section('heading', 'Edit Pengguna: ' . $user->name)
@section('content')
<div class="max-w-xl">
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 space-y-5">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-xs text-gray-400">(kosongkan jika tidak diubah)</span></label>
                <input type="password" name="password" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                <select name="role" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                    @foreach(['admin' => 'Admin', 'staff' => 'Staff', 'kasir' => 'Kasir', 'manager' => 'Manager'] as $v => $l)
                        <option value="{{ $v }}" {{ old('role', $user->role) == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
                @error('role')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="rounded-lg bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-700 transition-colors">Perbarui</button>
            <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Batal</a>
        </div>
    </form>
</div>
@endsection
