<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\ReportBuyTransactionExport;
use App\Exports\ReportSellTransactionExport;
use App\Models\BuyTransaction;
use App\Models\Currency;
use App\Models\SellTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function buy(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $transactions = BuyTransaction::whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay(),
        ])->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.reports.buy', compact('transactions', 'startDate', 'endDate'));
    }

    public function buyExport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        return Excel::download(new ReportBuyTransactionExport($startDate, $endDate), 'report-pembelian-valas.xlsx');
    }

    public function sell(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $transactions = SellTransaction::whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay(),
        ])->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.reports.sell', compact('transactions', 'startDate', 'endDate'));
    }

    public function sellExport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        return Excel::download(new ReportSellTransactionExport($startDate, $endDate), 'report-penjualan-valas.xlsx');
    }

    public function mutation(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        $currencyId = $request->get('currency_id');
        $keyword = $request->get('keyword');

        $currencies = Currency::where('is_active', true)->get();
        $searched = $request->hasAny(['start_date', 'end_date', 'currency_id', 'keyword']);
        $groupedMutations = [];
        $totalRecords = 0;

        if ($searched) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            $bbBuy = DB::table('buy_transaction_items as bti')
                ->join('buy_transactions as bt', 'bti.buy_transaction_id', '=', 'bt.id')
                ->select('bti.currency_code', DB::raw('SUM(bti.qty) as total_buy'))
                ->where('bt.created_at', '<', $start);
            $bbSell = DB::table('sell_transaction_items as sti')
                ->join('sell_transactions as st', 'sti.sell_transaction_id', '=', 'st.id')
                ->select('sti.currency_code', DB::raw('SUM(sti.qty) as total_sell'))
                ->where('st.created_at', '<', $start);

            $buyQuery = DB::table('buy_transaction_items as bti')
                ->join('buy_transactions as bt', 'bti.buy_transaction_id', '=', 'bt.id')
                ->select('bti.currency_code', 'bti.currency_name', 'bt.created_at as date', 'bt.transaction_code', DB::raw('bti.qty as buy_qty'), DB::raw('0 as sell_qty'), 'bti.buy_rate as rate', DB::raw("'BUY' as type"), 'bt.customer_name')
                ->whereBetween('bt.created_at', [$start, $end]);
            $sellQuery = DB::table('sell_transaction_items as sti')
                ->join('sell_transactions as st', 'sti.sell_transaction_id', '=', 'st.id')
                ->select('sti.currency_code', 'sti.currency_name', 'st.created_at as date', 'st.transaction_code', DB::raw('0 as buy_qty'), DB::raw('sti.qty as sell_qty'), 'sti.sell_rate as rate', DB::raw("'SELL' as type"), 'st.customer_name')
                ->whereBetween('st.created_at', [$start, $end]);

            if ($currencyId) {
                $currency = Currency::find($currencyId);
                if ($currency) {
                    $bbBuy->where('bti.currency_code', $currency->code);
                    $bbSell->where('sti.currency_code', $currency->code);
                    $buyQuery->where('bti.currency_code', $currency->code);
                    $sellQuery->where('sti.currency_code', $currency->code);
                }
            }

            $bbBuyData = $bbBuy->groupBy('bti.currency_code')->pluck('total_buy', 'currency_code');
            $bbSellData = $bbSell->groupBy('sti.currency_code')->pluck('total_sell', 'currency_code');

            $unionSql = $buyQuery->unionAll($sellQuery);
            $raw = DB::table(DB::raw("({$unionSql->toSql()}) as m"))
                ->mergeBindings($unionSql)
                ->orderBy('currency_code')->orderBy('date')->get();

            if ($keyword) {
                $kw = strtolower($keyword);
                $raw = $raw->filter(fn($r) => str_contains(strtolower($r->transaction_code), $kw) || str_contains(strtolower($r->customer_name), $kw) || str_contains(strtolower($r->currency_code), $kw));
            }

            $activeRates = Currency::where('is_active', true)->pluck('buy_rate', 'code');
            $mutationsByCurrency = [];

            foreach ($raw as $r) {
                $code = $r->currency_code;
                if (!isset($mutationsByCurrency[$code])) {
                    $mutationsByCurrency[$code] = [
                        'currency_code' => $code, 'currency_name' => $r->currency_name,
                        'beginning_balance' => ($bbBuyData[$code] ?? 0) - ($bbSellData[$code] ?? 0),
                        'current_stock' => ($bbBuyData[$code] ?? 0) - ($bbSellData[$code] ?? 0),
                        'total_buy' => 0, 'total_sell' => 0, 'rate' => $activeRates[$code] ?? 0, 'items' => []
                    ];
                }
                $mutationsByCurrency[$code]['current_stock'] += $r->buy_qty - $r->sell_qty;
                $mutationsByCurrency[$code]['total_buy'] += $r->buy_qty;
                $mutationsByCurrency[$code]['total_sell'] += $r->sell_qty;
                $mutationsByCurrency[$code]['items'][] = [
                    'date' => Carbon::parse($r->date)->format('j F Y'), 'trx_code' => $r->transaction_code,
                    'customer' => $r->customer_name ?? 'Head Office', 'buy' => $r->buy_qty, 'sell' => $r->sell_qty,
                    'rate' => $r->rate, 'stock' => $mutationsByCurrency[$code]['current_stock'],
                    'valuation' => $mutationsByCurrency[$code]['current_stock'] * $r->rate, 'type' => $r->type,
                ];
                $totalRecords++;
            }
            $groupedMutations = $mutationsByCurrency;
        }

        return view('admin.reports.mutation', compact('currencies', 'startDate', 'endDate', 'currencyId', 'keyword', 'searched', 'groupedMutations', 'totalRecords'));
    }
}
