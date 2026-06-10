<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CurrencyController extends Controller
{
    public function index(Request $request)
    {
        $query = Currency::query()->orderBy('is_active', 'desc')->orderBy('code');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('code', 'like', '%'.$request->search.'%')
                  ->orWhere('name', 'like', '%'.$request->search.'%')
                  ->orWhere('country_code', 'like', '%'.$request->search.'%');
            });
        }

        $currencies = $query->paginate(15)->withQueryString();

        return view('admin.currencies.index', compact('currencies'));
    }

    public function create()
    {
        return view('admin.currencies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:10',
            'country_code' => 'required|string|max:10',
            'name' => 'required|string|max:100',
            'buy_rate' => 'required|numeric|min:0',
            'sell_rate' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'flag' => 'nullable|image|max:2048',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('flag')) {
            $data['flag'] = $request->file('flag')->store('currencies', 'public');
        }

        Currency::create($data);

        return redirect()->route('admin.currencies.index')->with('success', 'Mata uang berhasil ditambahkan.');
    }

    public function show(Currency $currency)
    {
        return view('admin.currencies.show', compact('currency'));
    }

    public function edit(Currency $currency)
    {
        return view('admin.currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $data = $request->validate([
            'code' => 'required|string|max:10',
            'country_code' => 'required|string|max:10',
            'name' => 'required|string|max:100',
            'buy_rate' => 'required|numeric|min:0',
            'sell_rate' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'flag' => 'nullable|image|max:2048',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('flag')) {
            if ($currency->flag) Storage::disk('public')->delete($currency->flag);
            $data['flag'] = $request->file('flag')->store('currencies', 'public');
        }

        $currency->update($data);

        return redirect()->route('admin.currencies.index')->with('success', 'Mata uang berhasil diperbarui.');
    }

    public function destroy(Currency $currency)
    {
        if ($currency->flag) Storage::disk('public')->delete($currency->flag);
        $currency->delete();
        return redirect()->route('admin.currencies.index')->with('success', 'Mata uang berhasil dihapus.');
    }
}
