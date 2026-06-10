@extends('layouts.admin')
@section('title', 'Report Pembelian Valas')
@section('heading', 'Report Transaksi Pembelian Valas')
@section('content')
<div class="space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <form method="GET" class="flex flex-col sm:flex-row gap-2">
            <input type="date" name="start_date" value="{{ $startDate }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <input type="date" name="end_date" value="{{ $endDate }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <button type="submit" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700 transition-colors">Filter</button>
        </form>
        <a href="{{ route('admin.reports.buy-export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export Excel
        </a>
    </div>
    <x-admin.table :headers="['Tanggal', 'Kode', 'Customer', 'Total']" :actions="true">
        @forelse($transactions as $t)
            <tr class="hover:bg-gray-50"><td class="px-4 py-3 text-sm text-gray-500">{{ $t->created_at->format('d M Y H:i') }}</td><td class="px-4 py-3 text-sm font-medium">{{ $t->transaction_code }}</td><td class="px-4 py-3 text-sm">{{ $t->customer_name }}</td><td class="px-4 py-3 text-sm font-semibold">Rp {{ number_format($t->grand_total, 0, ',', '.') }}</td><td class="px-4 py-3 text-right"><a href="{{ route('admin.buy-transactions.show', $t) }}" class="text-amber-600 hover:underline text-sm">Detail</a></td></tr>
        @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">Tidak ada data.</td></tr>
        @endforelse
    </x-admin.table>
    <div class="mt-4">{{ $transactions->links() }}</div>
</div>
@endsection
