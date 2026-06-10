<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuyTransaction;
use App\Models\BuyTransactionCdd;
use App\Models\BuyTransactionItem;
use App\Models\Currency;
use App\Models\Office;
use App\Models\SancoEntity;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BuyTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = BuyTransaction::with('items')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('transaction_code', 'like', '%'.$request->search.'%')
                  ->orWhere('customer_name', 'like', '%'.$request->search.'%');
            });
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $transactions = $query->paginate(15)->withQueryString();

        return view('admin.buy-transactions.index', compact('transactions'));
    }

    public function create()
    {
        $currencies = Currency::where('buy_rate', '>', 0)->where('is_active', true)->get();
        $office = Office::first();

        return view('admin.buy-transactions.create', compact('currencies', 'office'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'transaction_code' => 'required|string|max:50|unique:buy_transactions',
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

        $totalAmount = 0;
        $itemsToInsert = [];

        foreach ($request->items as $item) {
            $currency = Currency::find($item['currency_id']);
            $qty = (float) $item['qty'];
            $total = $currency->buy_rate * $qty;
            $totalAmount += $total;

            $itemsToInsert[] = [
                'currency_id' => $currency->id,
                'currency_code' => $currency->code,
                'currency_name' => $currency->name,
                'currency_flag' => $currency->flag,
                'buy_rate' => $currency->buy_rate,
                'qty' => $qty,
                'total' => $total,
            ];
        }

        $additional = collect($data['additional_amounts'])->sum('amount');
        $data['total_amount'] = $totalAmount;
        $data['grand_total'] = $totalAmount + $additional;

        $transaction = BuyTransaction::create($data);

        foreach ($itemsToInsert as $itemData) {
            $itemData['buy_transaction_id'] = $transaction->id;
            BuyTransactionItem::create($itemData);
        }

        return redirect()->route('admin.buy-transactions.show', $transaction)->with('success', 'Transaksi pembelian berhasil dibuat.');
    }

    public function show(BuyTransaction $buyTransaction)
    {
        $buyTransaction->load('items', 'user', 'cdd');
        $office = Office::first();
        $showCdd = $office->cdd_threshold && $buyTransaction->grand_total >= $office->cdd_threshold;

        return view('admin.buy-transactions.show', compact('buyTransaction', 'office', 'showCdd'));
    }

    public function edit(BuyTransaction $buyTransaction)
    {
        $buyTransaction->load('items');
        $currencies = Currency::where('buy_rate', '>', 0)->where('is_active', true)->get();
        $office = Office::first();

        return view('admin.buy-transactions.edit', compact('buyTransaction', 'currencies', 'office'));
    }

    public function update(Request $request, BuyTransaction $buyTransaction)
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

        $totalAmount = 0;
        $itemsToInsert = [];

        foreach ($request->items as $item) {
            $currency = Currency::find($item['currency_id']);
            $qty = (float) $item['qty'];
            $total = $currency->buy_rate * $qty;
            $totalAmount += $total;

            $itemsToInsert[] = [
                'currency_id' => $currency->id,
                'currency_code' => $currency->code,
                'currency_name' => $currency->name,
                'currency_flag' => $currency->flag,
                'buy_rate' => $currency->buy_rate,
                'qty' => $qty,
                'total' => $total,
            ];
        }

        $additional = collect($data['additional_amounts'])->sum('amount');
        $data['total_amount'] = $totalAmount;
        $data['grand_total'] = $totalAmount + $additional;

        $buyTransaction->update($data);

        $buyTransaction->items()->delete();
        foreach ($itemsToInsert as $itemData) {
            $itemData['buy_transaction_id'] = $buyTransaction->id;
            BuyTransactionItem::create($itemData);
        }

        return redirect()->route('admin.buy-transactions.show', $buyTransaction)->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(BuyTransaction $buyTransaction)
    {
        $buyTransaction->items()->delete();
        $buyTransaction->cdd()->delete();
        $buyTransaction->delete();
        return redirect()->route('admin.buy-transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    public function cdd(BuyTransaction $buyTransaction)
    {
        $buyTransaction->load('cdd');
        $office = Office::first();
        return view('admin.buy-transactions.cdd', compact('buyTransaction', 'office'));
    }

    public function cddStore(Request $request, BuyTransaction $buyTransaction)
    {
        $data = $request->validate([
            'jenis_nasabah' => 'nullable|string',
            'nama_lengkap' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:50',
            'nama_jalan' => 'nullable|string|max:255',
            'rt_rw' => 'nullable|string|max:50',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'cabang' => 'nullable|string|max:255',
            'tujuan_transaksi' => 'nullable|string|max:255',
            'hubungan_pemilik_dana' => 'nullable|string|max:255',
            'sumber_dana' => 'nullable|string|max:255',
            'total_dana_tunai' => 'nullable|string|max:50',
            'no_telp' => 'nullable|string|max:50',
            'penghasilan_tahun' => 'nullable|numeric|min:0',
            'negara' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:20',
            'jenis_pekerjaan' => 'nullable|string|max:255',
            'jenis_pekerjaan_lainnya' => 'nullable|string|max:255',
            'nama_perusahaan' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'bentuk_hukum' => 'nullable|string|max:255',
            'bentuk_hukum_lainnya' => 'nullable|string|max:255',
            'bidang_usaha' => 'nullable|string|max:255',
        ]);

        $cdd = $buyTransaction->cdd;

        if ($cdd) {
            $cdd->update($data);
        } else {
            $data['buy_transaction_id'] = $buyTransaction->id;
            BuyTransactionCdd::create($data);
        }

        return redirect()->route('admin.buy-transactions.show', $buyTransaction)->with('success', 'Data CDD berhasil disimpan.');
    }

    public function checkPep(Request $request)
    {
        $keyword = $request->get('q', '');
        if (strlen($keyword) < 2) return response()->json([]);

        $entities = SancoEntity::where('name', 'like', '%' . $keyword . '%')
            ->select('name', 'entity_id', 'dataset_title', 'dataset_name')
            ->orderBy('entity_id')
            ->limit(50)
            ->get();

        $result = [];
        $seen = [];

        foreach ($entities as $e) {
            if (isset($seen[$e->entity_id])) continue;
            $seen[$e->entity_id] = true;
            $result[] = ['name' => $e->name, 'dataset' => $e->dataset_title ?? $e->dataset_name ?? '-', 'entity_id' => $e->entity_id, 'url' => route('sanco.entity.show', $e->entity_id)];
        }

        return response()->json(array_slice($result, 0, 10));
    }
}
