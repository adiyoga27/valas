<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\SellTransaction;
use App\Models\SellTransactionItem;
use Illuminate\Http\Request;

class SellTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = SellTransaction::with('items')->orderBy('created_at', 'desc');
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('transaction_code', 'like', '%'.$request->search.'%')->orWhere('customer_name', 'like', '%'.$request->search.'%');
            });
        }
        if ($request->filled('start_date')) $query->whereDate('created_at', '>=', $request->start_date);
        if ($request->filled('end_date')) $query->whereDate('created_at', '<=', $request->end_date);
        $transactions = $query->paginate(15)->withQueryString();
        return view('admin.sell-transactions.index', compact('transactions'));
    }

    public function create()
    {
        $currencies = Currency::where('sell_rate', '>', 0)->where('is_active', true)->get();
        return view('admin.sell-transactions.create', compact('currencies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'transaction_code' => 'required|string|max:50|unique:sell_transactions',
            'created_at' => 'required|date',
            'customer_name' => 'required|string|max:255',
            'passport_number' => 'nullable|string|max:100',
            'customer_address' => 'nullable|string|max:500',
            'customer_country' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.currency_id' => 'required|exists:currencies,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'additional_amounts' => 'nullable|array',
            'additional_amounts.*.name' => 'nullable|string|max:255',
            'additional_amounts.*.amount' => 'nullable|numeric|min:0',
        ]);
        $data['user_id'] = auth()->id();
        $data['additional_amounts'] = collect($request->additional_amounts ?? [])->filter(fn($a) => !empty($a['name']))->values()->toArray();
        $totalAmount = 0; $itemsToInsert = [];
        foreach ($request->items as $item) {
            $currency = Currency::find($item['currency_id']);
            $qty = (float)$item['qty']; $total = $currency->sell_rate * $qty; $totalAmount += $total;
            $itemsToInsert[] = ['currency_id' => $currency->id, 'currency_code' => $currency->code, 'currency_name' => $currency->name, 'currency_flag' => $currency->flag, 'sell_rate' => $currency->sell_rate, 'qty' => $qty, 'total' => $total];
        }
        $additional = collect($data['additional_amounts'])->sum('amount');
        $data['total_amount'] = $totalAmount; $data['grand_total'] = $totalAmount + $additional;
        $transaction = SellTransaction::create($data);
        foreach ($itemsToInsert as $itemData) { $itemData['sell_transaction_id'] = $transaction->id; SellTransactionItem::create($itemData); }
        return redirect()->route('admin.sell-transactions.show', $transaction)->with('success', 'Transaksi penjualan berhasil dibuat.');
    }

    public function show(SellTransaction $sellTransaction)
    {
        $sellTransaction->load('items', 'user');
        return view('admin.sell-transactions.show', compact('sellTransaction'));
    }

    public function edit(SellTransaction $sellTransaction)
    {
        $sellTransaction->load('items');
        $currencies = Currency::where('sell_rate', '>', 0)->where('is_active', true)->get();
        return view('admin.sell-transactions.edit', compact('sellTransaction', 'currencies'));
    }

    public function update(Request $request, SellTransaction $sellTransaction)
    {
        $data = $request->validate([
            'created_at' => 'required|date',
            'customer_name' => 'required|string|max:255',
            'passport_number' => 'nullable|string|max:100',
            'customer_address' => 'nullable|string|max:500',
            'customer_country' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.currency_id' => 'required|exists:currencies,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'additional_amounts' => 'nullable|array',
            'additional_amounts.*.name' => 'nullable|string|max:255',
            'additional_amounts.*.amount' => 'nullable|numeric|min:0',
        ]);
        $data['additional_amounts'] = collect($request->additional_amounts ?? [])->filter(fn($a) => !empty($a['name']))->values()->toArray();
        $totalAmount = 0; $itemsToInsert = [];
        foreach ($request->items as $item) {
            $currency = Currency::find($item['currency_id']);
            $qty = (float)$item['qty']; $total = $currency->sell_rate * $qty; $totalAmount += $total;
            $itemsToInsert[] = ['currency_id' => $currency->id, 'currency_code' => $currency->code, 'currency_name' => $currency->name, 'currency_flag' => $currency->flag, 'sell_rate' => $currency->sell_rate, 'qty' => $qty, 'total' => $total];
        }
        $additional = collect($data['additional_amounts'])->sum('amount');
        $data['total_amount'] = $totalAmount; $data['grand_total'] = $totalAmount + $additional;
        $sellTransaction->update($data);
        $sellTransaction->items()->delete();
        foreach ($itemsToInsert as $itemData) { $itemData['sell_transaction_id'] = $sellTransaction->id; SellTransactionItem::create($itemData); }
        return redirect()->route('admin.sell-transactions.show', $sellTransaction)->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(SellTransaction $sellTransaction)
    {
        $sellTransaction->items()->delete(); $sellTransaction->delete();
        return redirect()->route('admin.sell-transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
