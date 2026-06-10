@extends('layouts.admin')
@section('title', 'Detail Mata Uang')
@section('heading', 'Detail Mata Uang: ' . $currency->code)
@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label class="text-xs font-medium text-gray-500 uppercase">Kode</label><p class="text-sm font-semibold">{{ $currency->code }}</p></div>
            <div><label class="text-xs font-medium text-gray-500 uppercase">Kode Negara</label><p class="text-sm">{{ $currency->country_code }}</p></div>
            <div class="sm:col-span-2"><label class="text-xs font-medium text-gray-500 uppercase">Nama</label><p class="text-sm">{{ $currency->name }}</p></div>
            <div><label class="text-xs font-medium text-gray-500 uppercase">Kurs Beli</label><p class="text-sm">Rp {{ number_format($currency->buy_rate, 0, ',', '.') }}</p></div>
            <div><label class="text-xs font-medium text-gray-500 uppercase">Kurs Jual</label><p class="text-sm">Rp {{ number_format($currency->sell_rate, 0, ',', '.') }}</p></div>
            <div><label class="text-xs font-medium text-gray-500 uppercase">Flag</label>@if($currency->flag)<img src="{{ asset('storage/'.$currency->flag) }}" class="h-10 rounded">@else - @endif</div>
            <div><label class="text-xs font-medium text-gray-500 uppercase">Status</label> <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $currency->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $currency->is_active ? 'Aktif' : 'Nonaktif' }}</span></div>
        </div>
        <div class="flex gap-3 pt-2">
            <a href="{{ route('admin.currencies.edit', $currency) }}" class="rounded-lg bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-700 transition-colors">Edit</a>
            <form method="POST" action="{{ route('admin.currencies.destroy', $currency) }}" onsubmit="return confirm('Hapus {{ $currency->code }}?')">
                @csrf @method('DELETE')
                <button class="rounded-lg border border-red-300 px-5 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">Hapus</button>
            </form>
            <a href="{{ route('admin.currencies.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Kembali</a>
        </div>
    </div>
</div>
@endsection
