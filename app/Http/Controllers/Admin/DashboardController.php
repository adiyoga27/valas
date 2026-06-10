<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuyTransaction;
use App\Models\Currency;
use App\Models\Office;
use App\Models\SellTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $office = Office::first();
        $year = $request->get('year', now()->year);

        $months = collect(range(1, 12))->map(fn($m) => Carbon::create()->month($m)->translatedFormat('F'));

        $buyData = BuyTransaction::query()
            ->selectRaw('MONTH(created_at) as month, SUM(grand_total) as total')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $sellData = SellTransaction::query()
            ->selectRaw('MONTH(created_at) as month, SUM(grand_total) as total')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $chartLabels = $months->values()->toArray();
        $chartBuy = collect(range(1, 12))->map(fn($m) => (int)($buyData[$m] ?? 0))->toArray();
        $chartSell = collect(range(1, 12))->map(fn($m) => (int)($sellData[$m] ?? 0))->toArray();

        $years = range(now()->year, now()->year - 5);

        $activeCurrencies = Currency::where('is_active', true)->count();
        $todayBuyTotal = BuyTransaction::whereDate('created_at', today())->sum('grand_total');
        $todaySellTotal = SellTransaction::whereDate('created_at', today())->sum('grand_total');
        $monthBuyTotal = BuyTransaction::whereMonth('created_at', now()->month)->whereYear('created_at', $year)->sum('grand_total');
        $monthSellTotal = SellTransaction::whereMonth('created_at', now()->month)->whereYear('created_at', $year)->sum('grand_total');

        return view('admin.dashboard.index', compact(
            'office', 'year', 'years', 'chartLabels', 'chartBuy', 'chartSell',
            'activeCurrencies', 'todayBuyTotal', 'todaySellTotal', 'monthBuyTotal', 'monthSellTotal'
        ));
    }
}
