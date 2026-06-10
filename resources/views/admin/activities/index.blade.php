@extends('layouts.admin')
@section('title', 'Activity Log')
@section('heading', 'Log Aktivitas')
@section('content')
<div class="space-y-4">
    <form method="GET" class="relative max-w-sm">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas..." class="w-full rounded-lg border border-gray-300 pl-9 pr-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
        <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </form>
    <x-admin.table :headers="['Waktu', 'Aksi', 'Model', 'User', 'IP']" :actions="false">
        @forelse($activities as $activity)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">{{ $activity->created_at->format('d M Y H:i') }}</td>
                <td class="px-4 py-3 text-sm">
                    @php $colors = ['created' => 'bg-green-100 text-green-700', 'updated' => 'bg-blue-100 text-blue-700', 'deleted' => 'bg-red-100 text-red-700']; $ev = explode(' ', $activity->description ?? '')[0] ?? ''; @endphp
                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $colors[$ev] ?? 'bg-gray-100 text-gray-600' }}">{{ $activity->description }}</span>
                </td>
                <td class="px-4 py-3 text-xs text-gray-500">{{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}</td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ $activity->causer?->name }}</td>
                <td class="px-4 py-3 text-xs text-gray-400 font-mono">{{ $activity->properties['ip'] ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada log aktivitas.</td></tr>
        @endforelse
    </x-admin.table>
    <div class="mt-4">{{ $activities->links() }}</div>
</div>
@endsection
