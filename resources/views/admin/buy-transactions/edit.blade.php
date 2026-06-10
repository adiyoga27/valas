@extends('layouts.admin')
@section('title', 'Edit Transaksi Beli Valas')
@section('heading', 'Edit Pembelian: ' . $buyTransaction->transaction_code)
@section('content')
<div class="max-w-4xl" x-data="editBuyForm()">
    <form method="POST" action="{{ route('admin.buy-transactions.update', $buyTransaction) }}" class="space-y-6">
        @csrf @method('PUT')
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Transaksi *</label>
                    <input type="datetime-local" name="created_at" x-model="created_at" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">No. Invoice</label>
                    <input type="text" value="{{ $buyTransaction->transaction_code }}" readonly class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-500">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-500 mb-1">Nama Pelanggan *</label><input type="text" name="customer_name" value="{{ old('customer_name', $buyTransaction->customer_name) }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></div>
                <div><label class="block text-xs font-medium text-gray-500 mb-1">Passport</label><input type="text" name="passport_number" value="{{ old('passport_number', $buyTransaction->passport_number) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></div>
                <div><label class="block text-xs font-medium text-gray-500 mb-1">Alamat</label><input type="text" name="customer_address" value="{{ old('customer_address', $buyTransaction->customer_address) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></div>
                <div><label class="block text-xs font-medium text-gray-500 mb-1">Negara</label><input type="text" name="customer_country" value="{{ old('customer_country', $buyTransaction->customer_country) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700">Detail Item</h3>
                <button type="button" x-on:click="addItem()" class="text-xs font-medium text-amber-600 hover:text-amber-700">+ Tambah Item</button>
            </div>
            <template x-for="(item, i) in items" :key="i">
                <div class="grid grid-cols-12 gap-2 items-end p-3 rounded-lg bg-gray-50/50 border border-gray-100">
                    <div class="col-span-4 sm:col-span-3">
                        <label class="block text-xs text-gray-500 mb-0.5">Mata Uang</label>
                        <select x-model="item.currency_id" required :name="'items['+i+'][currency_id]'" class="w-full rounded-lg border border-gray-300 px-2 py-2 text-sm focus:border-amber-500 focus:ring-amber-500" x-on:change="onCurrencyChange(i)">
                            <option value="">Pilih</option>
                            @foreach($currencies as $c)
                                <option value="{{ $c->id }}" data-code="{{ $c->code }}" data-name="{{ $c->name }}" data-rate="{{ $c->buy_rate }}">{{ $c->code }} - {{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2 sm:col-span-2"><label class="block text-xs text-gray-500 mb-0.5">Kode</label><input type="text" x-model="item.currency_code" readonly class="w-full rounded-lg border border-gray-200 bg-gray-100 px-2 py-2 text-sm text-gray-500"></div>
                    <div class="col-span-2 sm:col-span-1"><label class="block text-xs text-gray-500 mb-0.5">Qty</label><input type="number" x-model="item.qty" step="0.01" min="0.01" required :name="'items['+i+'][qty]'" class="w-full rounded-lg border border-gray-300 px-2 py-2 text-sm focus:border-amber-500 focus:ring-amber-500" x-on:input="recalcItem(i)"></div>
                    <div class="col-span-0 hidden sm:col-span-2 sm:block"><label class="block text-xs text-gray-500 mb-0.5">Kurs Beli</label><input type="text" :value="formatNumber(item.buy_rate)" readonly class="w-full rounded-lg border border-gray-200 bg-gray-100 px-2 py-2 text-sm text-gray-500 text-right"></div>
                    <div class="col-span-3 sm:col-span-2"><label class="block text-xs text-gray-500 mb-0.5">Total</label><input type="text" :value="formatNumber(item.total)" readonly class="w-full rounded-lg border border-gray-200 bg-gray-100 px-2 py-2 text-sm text-gray-700 font-medium text-right"></div>
                    <div class="col-span-1 text-right"><button type="button" x-on:click="removeItem(i)" class="text-red-400 hover:text-red-600 p-2" x-show="items.length > 1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></div>
                </div>
            </template>
        </div>

        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 space-y-4">
            <div class="flex items-center justify-between"><h3 class="text-sm font-semibold text-gray-700">Biaya Tambahan</h3><button type="button" x-on:click="addAdditional()" class="text-xs font-medium text-amber-600 hover:text-amber-700">+ Tambah Biaya</button></div>
            <template x-for="(a, i) in additionals" :key="'a'+i">
                <div class="flex gap-2 items-end">
                    <div class="flex-1"><label class="block text-xs text-gray-500 mb-0.5">Nama Biaya</label><input type="text" x-model="a.name" :name="'additional_amounts['+i+'][name]'" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"></div>
                    <div class="w-40"><label class="block text-xs text-gray-500 mb-0.5">Jumlah</label><input type="number" x-model="a.amount" step="0.01" min="0" :name="'additional_amounts['+i+'][amount]'" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500" x-on:input="recalcTotal()"></div>
                    <button type="button" x-on:click="removeAdditional(i)" class="text-red-400 hover:text-red-600 p-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                </div>
            </template>
        </div>

        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 space-y-3">
            <div class="flex justify-between text-sm"><span>Total Transaksi</span><span class="font-semibold" x-text="'Rp ' + formatNumber(totalItems)"></span></div>
            <div class="flex justify-between text-sm"><span>Total Biaya Tambahan</span><span class="font-semibold" x-text="'Rp ' + formatNumber(totalAdditional)"></span></div>
            <div class="flex justify-between text-lg font-bold border-t pt-3"><span>Grand Total</span><span class="text-green-600" x-text="'Rp ' + formatNumber(grandTotal)"></span></div>
        </div>

        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
            <textarea name="notes" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">{{ old('notes', $buyTransaction->notes) }}</textarea>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-700 transition-colors">Perbarui</button>
            <a href="{{ route('admin.buy-transactions.show', $buyTransaction) }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Batal</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function editBuyForm() {
    const items = @json($buyTransaction->items->map(fn($i) => ['currency_id' => $i->currency_id, 'currency_code' => $i->currency_code, 'currency_name' => $i->currency_name, 'buy_rate' => (float)$i->buy_rate, 'qty' => (float)$i->qty, 'total' => (float)$i->total]));
    const additionals = @json(collect($buyTransaction->additional_amounts ?: [])->map(fn($a) => ['name' => $a['name'] ?? '', 'amount' => (float)($a['amount'] ?? 0)])->values());
    return {
        created_at: '{{ $buyTransaction->created_at->format('Y-m-d\TH:i') }}',
        items: items.length > 0 ? items : [{ currency_id: '', currency_code: '', currency_name: '', buy_rate: 0, qty: 1, total: 0 }],
        additionals: additionals || [],
        get totalItems() { return this.items.reduce((s, i) => s + (parseFloat(i.total) || 0), 0); },
        get totalAdditional() { return this.additionals.reduce((s, a) => s + (parseFloat(a.amount) || 0), 0); },
        get grandTotal() { return this.totalItems + this.totalAdditional; },
        onCurrencyChange(i) {
            const el = document.querySelector(`[name="items[${i}][currency_id]"]`);
            const opt = el?.selectedOptions[0];
            if (opt && opt.value) { this.items[i].currency_code = opt.dataset.code; this.items[i].currency_name = opt.dataset.name; this.items[i].buy_rate = parseFloat(opt.dataset.rate); this.recalcItem(i); }
        },
        recalcItem(i) { this.items[i].total = this.items[i].buy_rate * (parseFloat(this.items[i].qty) || 0); },
        recalcTotal() { },
        addItem() { this.items.push({ currency_id: '', currency_code: '', currency_name: '', buy_rate: 0, qty: 1, total: 0 }); },
        removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1); },
        addAdditional() { this.additionals.push({ name: '', amount: 0 }); },
        removeAdditional(i) { this.additionals.splice(i, 1); },
        formatNumber(n) { return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(n || 0); }
    }
}
</script>
@endpush
