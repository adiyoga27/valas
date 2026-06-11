<?php

namespace App\Http\Controllers;

use App\Models\BuyTransaction;
use App\Models\SellTransaction;
use App\Models\Office;
use Barryvdh\DomPDF\Facade\Pdf;

class CddPrintController extends Controller
{
    public function print($record)
    {
        $transaction = BuyTransaction::with('cdd')->findOrFail($record);

        if (!$transaction->cdd) {
            abort(404, 'Data CDD tidak ditemukan untuk transaksi ini.');
        }

        $pdf = Pdf::loadView('buy_transactions.cdd', [
            'transaction' => $transaction,
            'cdd' => $transaction->cdd,
            'office' => Office::first(),
        ])->setPaper('a4');

        return $pdf->stream("CDD_{$transaction->transaction_code}.pdf");
    }

    public function printSell($record)
    {
        $transaction = SellTransaction::with('cdd')->findOrFail($record);

        if (!$transaction->cdd) {
            abort(404, 'Data CDD tidak ditemukan untuk transaksi ini.');
        }

        $pdf = Pdf::loadView('sell_transactions.cdd', [
            'transaction' => $transaction,
            'cdd' => $transaction->cdd,
            'office' => Office::first(),
        ])->setPaper('a4');

        return $pdf->stream("CDD_{$transaction->transaction_code}.pdf");
    }
}
