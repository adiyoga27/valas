@extends('layouts.admin')
@section('title', 'Detail Pengguna')
@section('heading', 'Detail Pengguna: ' . $user->name)
@section('content')
<div class="max-w-xl">
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2"><label class="text-xs font-medium text-gray-500 uppercase">Nama</label><p class="text-sm font-semibold">{{ $user->name }}</p></div>
            <div class="sm:col-span-2"><label class="text-xs font-medium text-gray-500 uppercase">Email</label><p class="text-sm">{{ $user->email }}</p></div>
            <div><label class="text-xs font-medium text-gray-500 uppercase">Role</label>@php $colors = ['admin' => 'bg-green-100 text-green-700', 'staff' => 'bg-blue-100 text-blue-700', 'kasir' => 'bg-yellow-100 text-yellow-700', 'manager' => 'bg-purple-100 text-purple-700']; @endphp <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $colors[$user->role] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($user->role) }}</span></div>
            <div><label class="text-xs font-medium text-gray-500 uppercase">Dibuat</label><p class="text-sm">{{ $user->created_at->format('d M Y H:i') }}</p></div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.users.edit', $user) }}" class="rounded-lg bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-700 transition-colors">Edit</a>
            <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Kembali</a>
        </div>
    </div>
</div>
@endsection
