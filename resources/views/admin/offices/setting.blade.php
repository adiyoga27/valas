@extends('layouts.admin')
@section('title', 'Setting Kantor')
@section('heading', 'Setting Kantor')
@section('content')
<div class="max-w-xl">
    <form method="POST" action="{{ route('admin.office.update') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 space-y-5">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kantor *</label>
            <input type="text" name="name" value="{{ old('name', $office->name) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
            @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat *</label>
            <textarea name="address" rows="3" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">{{ old('address', $office->address) }}</textarea>
            @error('address')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon *</label>
            <input type="text" name="phone" value="{{ old('phone', $office->phone) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
            @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Batas Nominal CDD (Rp)</label>
            <input type="number" name="cdd_threshold" value="{{ old('cdd_threshold', $office->cdd_threshold) }}" step="1" placeholder="Contoh: 500000000" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
            <p class="mt-1 text-xs text-gray-500">Transaksi pembelian di atas nominal ini wajib mengisi formulir CDD. Kosongkan untuk menonaktifkan.</p>
            @error('cdd_threshold')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
            @if($office->logo)
                <div class="mb-2"><img src="{{ asset('storage/'.$office->logo) }}" class="h-12 rounded object-cover border"></div>
            @endif
            <input type="file" name="logo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
            @error('logo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="pt-2">
            <button type="submit" class="rounded-lg bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-700 transition-colors">Simpan</button>
        </div>
    </form>
</div>
@endsection
