@extends('layouts.admin')
@section('title', 'Tambah Mata Uang')
@section('heading', 'Tambah Mata Uang')
@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.currencies.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 space-y-5">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode *</label>
                <input type="text" name="code" value="{{ old('code') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500" placeholder="USD">
                @error('code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Negara *</label>
                <input type="text" name="country_code" value="{{ old('country_code') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500" placeholder="US">
                @error('country_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500" placeholder="US Dollar">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kurs Beli (Rp) *</label>
                <input type="number" name="buy_rate" value="{{ old('buy_rate') }}" required step="0.01" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500" placeholder="16000">
                @error('buy_rate')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kurs Jual (Rp) *</label>
                <input type="number" name="sell_rate" value="{{ old('sell_rate') }}" required step="0.01" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500" placeholder="16200">
                @error('sell_rate')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Flag</label>
                <input type="file" name="flag" accept="image/*" class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                @error('flag')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                <label for="is_active" class="text-sm text-gray-700">Aktif</label>
            </div>
        </div>
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="rounded-lg bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-700 transition-colors">Simpan</button>
            <a href="{{ route('admin.currencies.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Batal</a>
        </div>
    </form>
</div>
@endsection
