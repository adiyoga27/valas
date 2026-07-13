<?php

namespace App\Exports;

use App\Models\BuyTransactionItem;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReportBuyTransactionExport implements FromView
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
    }

    public function view(): View
    {
        $items = BuyTransactionItem::query()
            ->select('buy_transaction_items.*')
            ->join('buy_transactions', 'buy_transaction_items.buy_transaction_id', '=', 'buy_transactions.id')
            ->whereBetween('buy_transactions.created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ])
            ->orderBy('buy_transactions.created_at')
            ->get();

        return view('exports.report-buy-transaction', [
            'items' => $items,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }
}
