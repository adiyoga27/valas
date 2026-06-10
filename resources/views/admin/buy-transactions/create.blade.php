@extends('layouts.admin')
@section('title', 'Transaksi Beli Valas')
@section('heading', 'Buat Transaksi Pembelian Valas')
@section('content')
<div class="max-w-4xl" x-data="buyTransactionForm()">
    <form method="POST" action="{{ route('admin.buy-transactions.store') }}" class="space-y-6">
        @csrf
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 space-y-4">
            <h3 class="text-sm font-semibold text-gray-700">Invoice Pembelian</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Transaksi *</label>
                    <input type="datetime-local" name="created_at" x-model="created_at" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">No. Invoice</label>
                    <input type="text" name="transaction_code" x-model="transaction_code" readonly class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-500">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Nama Pelanggan *</label>
                    <input type="text" name="customer_name" x-model="customer_name" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500" placeholder="Nama lengkap pelanggan"
                           x-on:input.debounce.500ms="checkPep()">
                    <div x-show="pepMatches !== null" class="mt-2" x-cloak>
                        <template x-if="pepMatches.length === 0">
                            <div class="rounded-lg bg-green-50 border border-green-200 px-3 py-2 text-sm text-green-700">Nama ini aman. Tidak terdaftar di database PEP/DTTOT.</div>
                        </template>
                        <template x-if="pepMatches.length > 0">
                            <div class="rounded-lg bg-red-50 border border-red-200 px-3 py-2 text-sm text-red-700 max-h-40 overflow-y-auto">
                                <p class="font-semibold mb-1" x-text="'Ditemukan ' + pepMatches.length + ' kecocokan:'"></p>
                                <div class="space-y-1">
                                    <template x-for="m in pepMatches" :key="m.entity_id">
                                        <div class="flex items-center gap-1 text-xs"><a :href="m.url" target="_blank" class="font-medium text-red-600 hover:underline" x-text="m.name"></a> <span class="text-gray-500" x-text="'(' + m.dataset + ')'"></span></div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                    @error('customer_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Nomor Passport / KTP</label>
                    <input type="text" name="passport_number" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Alamat Pelanggan</label>
                    <input type="text" name="customer_address" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Negara Pelanggan</label>
                    <input type="text" name="customer_country" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700">Detail Item Pembelian</h3>
                <button type="button" x-on:click="addItem()" class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 hover:text-amber-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Tambah Item
                </button>
            </div>
            <template x-for="(item, i) in items" :key="i">
                <div class="grid grid-cols-12 gap-2 items-end p-3 rounded-lg bg-gray-50/50 border border-gray-100">
                    <div class="col-span-4 sm:col-span-3">
                        <label class="block text-xs text-gray-500 mb-0.5">Mata Uang</label>
                        <select x-model="item.currency_id" required :name="'items['+i+'][currency_id]'" class="w-full rounded-lg border border-gray-300 px-2 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"
                                x-on:change="onCurrencyChange(i)">
                            <option value="">Pilih</option>
                            @foreach($currencies as $c)
                                <option value="{{ $c->id }}" data-code="{{ $c->code }}" data-name="{{ $c->name }}" data-rate="{{ $c->buy_rate }}">{{ $c->code }} - {{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2 sm:col-span-2">
                        <label class="block text-xs text-gray-500 mb-0.5">Kode</label>
                        <input type="text" x-model="item.currency_code" readonly class="w-full rounded-lg border border-gray-200 bg-gray-100 px-2 py-2 text-sm text-gray-500">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-xs text-gray-500 mb-0.5">Qty</label>
                        <input type="number" x-model="item.qty" step="0.01" min="0.01" required :name="'items['+i+'][qty]'" class="w-full rounded-lg border border-gray-300 px-2 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"
                               x-on:input="recalcItem(i)">
                    </div>
                    <div class="col-span-0 hidden sm:col-span-2 sm:block">
                        <label class="block text-xs text-gray-500 mb-0.5">Kurs Beli</label>
                        <input type="text" :value="formatNumber(item.buy_rate)" readonly class="w-full rounded-lg border border-gray-200 bg-gray-100 px-2 py-2 text-sm text-gray-500 text-right">
                    </div>
                    <div class="col-span-3 sm:col-span-2">
                        <label class="block text-xs text-gray-500 mb-0.5">Total</label>
                        <input type="text" :value="formatNumber(item.total)" readonly class="w-full rounded-lg border border-gray-200 bg-gray-100 px-2 py-2 text-sm text-gray-700 font-medium text-right">
                    </div>
                    <div class="col-span-1 text-right">
                        <button type="button" x-on:click="removeItem(i)" class="text-red-400 hover:text-red-600 p-2" x-show="items.length > 1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Additional amounts -->
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700">Biaya Tambahan</h3>
                <button type="button" x-on:click="addAdditional()" class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 hover:text-amber-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Tambah Biaya
                </button>
            </div>
            <template x-for="(a, i) in additionals" :key="'a'+i">
                <div class="flex gap-2 items-end">
                    <div class="flex-1">
                        <label class="block text-xs text-gray-500 mb-0.5">Nama Biaya</label>
                        <input type="text" x-model="a.name" :name="'additional_amounts['+i+'][name]'" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                    </div>
                    <div class="w-40">
                        <label class="block text-xs text-gray-500 mb-0.5">Jumlah</label>
                        <input type="number" x-model="a.amount" step="0.01" min="0" :name="'additional_amounts['+i+'][amount]'" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500"
                               x-on:input="recalcTotal()">
                    </div>
                    <button type="button" x-on:click="removeAdditional(i)" class="text-red-400 hover:text-red-600 p-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                </div>
            </template>
        </div>

        <!-- Summary -->
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 space-y-3">
            <div class="flex justify-between text-sm"><span class="text-gray-600">Total Transaksi</span><span class="font-semibold" x-text="'Rp ' + formatNumber(totalItems)"></span></div>
            <div class="flex justify-between text-sm"><span class="text-gray-600">Total Biaya Tambahan</span><span class="font-semibold" x-text="'Rp ' + formatNumber(totalAdditional)"></span></div>
            <div class="flex justify-between text-lg font-bold border-t pt-3"><span>Grand Total</span><span class="text-green-600" x-text="'Rp ' + formatNumber(grandTotal)"></span></div>
        </div>

        <!-- Notes -->
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
            <textarea name="notes" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500" placeholder="Tambahkan catatan..."></textarea>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-700 transition-colors">Simpan Transaksi</button>
            <a href="{{ route('admin.buy-transactions.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Batal</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function buyTransactionForm() {
    return {
        created_at: '{{ now()->format('Y-m-d\TH:i') }}',
        transaction_code: '{{ \App\Models\BuyTransaction::generateCode() }}',
        customer_name: '',
        pepMatches: null,
        items: [{ currency_id: '', currency_code: '', currency_name: '', buy_rate: 0, qty: 1, total: 0 }],
        additionals: [],
        get totalItems() { return this.items.reduce((s, i) => s + (parseFloat(i.total) || 0), 0); },
        get totalAdditional() { return this.additionals.reduce((s, a) => s + (parseFloat(a.amount) || 0), 0); },
        get grandTotal() { return this.totalItems + this.totalAdditional; },
        async checkPep() {
            if (!this.customer_name || this.customer_name.length < 2) { this.pepMatches = null; return; }
            try {
                const res = await fetch('{{ route("admin.buy-transactions.check-pep") }}?q=' + encodeURIComponent(this.customer_name));
                const data = await res.json();
                this.pepMatches = data;
            } catch(e) { this.pepMatches = null; }
        },
        onCurrencyChange(i) {
            const el = document.querySelector(`[name="items[${i}][currency_id]"]`);
            const opt = el?.selectedOptions[0];
            if (opt && opt.value) {
                this.items[i].currency_code = opt.dataset.code;
                this.items[i].currency_name = opt.dataset.name;
                this.items[i].buy_rate = parseFloat(opt.dataset.rate);
                this.recalcItem(i);
            }
        },
        recalcItem(i) {
            const item = this.items[i];
            item.total = item.buy_rate * (parseFloat(item.qty) || 0);
        },
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
