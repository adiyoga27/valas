@extends('layouts.admin')
@section('title', 'Detail Transaksi Pembelian')
@section('heading', 'Detail Pembelian Valas')
@section('content')
<div class="max-w-4xl space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.buy-transactions.edit', $buyTransaction) }}" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700 transition-colors">Edit</a>
        <a href="{{ route('admin.buy-transactions.print', $buyTransaction) }}" target="_blank" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Print Invoice</a>
        @if($showCdd)
            <a href="{{ route('admin.buy-transactions.cdd', $buyTransaction) }}" class="rounded-lg border border-yellow-400 bg-yellow-50 px-4 py-2 text-sm font-medium text-yellow-700 hover:bg-yellow-100 transition-colors">Form CDD</a>
        @endif
        <form method="POST" action="{{ route('admin.buy-transactions.destroy', $buyTransaction) }}" onsubmit="return confirm('Hapus transaksi ini?')" class="ml-auto">
            @csrf @method('DELETE')
            <button class="rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">Hapus</button>
        </form>
        <a href="{{ route('admin.buy-transactions.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Kembali</a>
    </div>

    <!-- Invoice Info -->
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div><label class="text-xs font-medium text-gray-500 uppercase">Kode Transaksi</label><p class="text-sm font-bold">{{ $buyTransaction->transaction_code }}</p></div>
            <div><label class="text-xs font-medium text-gray-500 uppercase">Tanggal</label><p class="text-sm">{{ $buyTransaction->created_at->format('d M Y H:i') }}</p></div>
        </div>
        <div class="border-t pt-3">
            <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Informasi Pelanggan</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div><label class="text-xs text-gray-400">Nama</label><p class="text-sm font-medium">{{ $buyTransaction->customer_name }}</p></div>
                <div><label class="text-xs text-gray-400">Passport</label><p class="text-sm">{{ $buyTransaction->passport_number ?: '-' }}</p></div>
                <div><label class="text-xs text-gray-400">Alamat</label><p class="text-sm">{{ $buyTransaction->customer_address ?: '-' }}</p></div>
                <div><label class="text-xs text-gray-400">Negara</label><p class="text-sm">{{ $buyTransaction->customer_country ?: '-' }}</p></div>
                <div><label class="text-xs text-gray-400">Dibuat Oleh</label><p class="text-sm">{{ $buyTransaction->user?->name ?: 'Sistem' }}</p></div>
            </div>
        </div>
    </div>

    <!-- Items -->
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b"><h3 class="text-sm font-semibold text-gray-700">Detail Item Pembelian</h3></div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50"><tr><th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Mata Uang</th><th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Qty</th><th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Kurs Beli</th><th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Subtotal</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($buyTransaction->items as $item)
                        <tr><td class="px-4 py-3 text-sm">{{ $item->currency_code }} - {{ $item->currency_name }}</td><td class="px-4 py-3 text-sm text-right">{{ number_format($item->qty, 0, ',', '.') }}</td><td class="px-4 py-3 text-sm text-right">Rp {{ number_format($item->buy_rate, 0, ',', '.') }}</td><td class="px-4 py-3 text-sm text-right font-semibold">Rp {{ number_format($item->total, 0, ',', '.') }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Additional -->
    @if(!empty($buyTransaction->additional_amounts))
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b"><h3 class="text-sm font-semibold text-gray-700">Biaya Tambahan</h3></div>
            <table class="min-w-full divide-y divide-gray-200">
                @foreach($buyTransaction->additional_amounts as $add)
                    <tr><td class="px-4 py-3 text-sm">{{ $add['name'] }}</td><td class="px-4 py-3 text-sm text-right font-semibold">Rp {{ number_format($add['amount'], 0, ',', '.') }}</td></tr>
                @endforeach
            </table>
        </div>
    @endif

    <!-- Summary -->
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 space-y-2">
        <div class="flex justify-between text-sm"><span class="text-gray-600">Total Item</span><span class="font-semibold">Rp {{ number_format($buyTransaction->total_amount, 0, ',', '.') }}</span></div>
        @php $addTotal = collect($buyTransaction->additional_amounts)->sum('amount'); @endphp
        @if($addTotal > 0)
            <div class="flex justify-between text-sm"><span class="text-gray-600">Total Biaya Tambahan</span><span class="font-semibold">Rp {{ number_format($addTotal, 0, ',', '.') }}</span></div>
        @endif
        <div class="flex justify-between text-lg font-bold border-t pt-3"><span>Grand Total</span><span class="text-green-600">Rp {{ number_format($buyTransaction->grand_total, 0, ',', '.') }}</span></div>
        @if($buyTransaction->notes)
            <div class="border-t pt-2 mt-2"><span class="text-xs text-gray-400">Catatan:</span><p class="text-sm text-gray-600 mt-1">{{ $buyTransaction->notes }}</p></div>
        @endif
    </div>

    <!-- CDD Status -->
    @if($buyTransaction->cdd)
        <div class="bg-yellow-50 rounded-xl shadow-sm ring-1 ring-yellow-200 p-4 text-sm">
            <span class="font-semibold text-yellow-700">Formulir CDD sudah diisi.</span>
            <a href="{{ route('admin.buy-transactions.cdd-print', $buyTransaction) }}" target="_blank" class="ml-2 text-amber-600 hover:underline font-medium">Cetak CDD</a>
        </div>
    @elseif($showCdd)
        <div class="bg-red-50 rounded-xl shadow-sm ring-1 ring-red-200 p-4 text-sm text-red-700">
            <span class="font-semibold">Transaksi ini melebihi batas CDD (Rp {{ number_format($office->cdd_threshold, 0, ',', '.') }}).</span>
            <a href="{{ route('admin.buy-transactions.cdd', $buyTransaction) }}" class="ml-2 font-medium hover:underline">Isi Formulir CDD</a>
        </div>
    @endif
</div>
@endsection
