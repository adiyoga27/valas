@extends('layouts.admin')
@section('title', 'Dataset Sanco')
@section('heading', 'Dataset Sanco (PEP & DTTOT)')
@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ number_format($totalEntities, 0, ',', '.') }} entitas tersedia</p>
        <a href="{{ route('admin.sanco.check') }}" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700 transition-colors">Cek Nama</a>
    </div>
    <x-admin.table :headers="['Nama Dataset', 'Title', 'Entities', 'Publisher']" :actions="false">
        @forelse($datasets as $ds)
            <tr class="hover:bg-gray-50"><td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $ds->name }}</td><td class="px-4 py-3 text-sm text-gray-600">{{ $ds->title }}</td><td class="px-4 py-3 text-sm">{{ number_format($ds->entity_count ?? 0, 0, ',', '.') }}</td><td class="px-4 py-3 text-sm text-gray-600">{{ $ds->publisher_name }}</td></tr>
        @empty
            <tr><td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada dataset. Silakan import data.</td></tr>
        @endforelse
    </x-admin.table>
    <div class="mt-4">{{ $datasets->links() }}</div>
</div>
@endsection
