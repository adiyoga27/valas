<?php

namespace App\Http\Controllers;

use App\Models\SellTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SellTransactionPrintController extends Controller
{
    public function print($record)
    {
        $transaction = SellTransaction::with('items', 'user')->findOrFail($record);

        $pdf = Pdf::loadView('buy_transactions.invoice', [
            'transaction' => $transaction
        ])
            ->setPaper([0, 0, 215.5, 1000])
            ->setOptions([
                // Hapus semua margin
                'margin-top'    => 0,
                'margin-right'  => 0,
                'margin-bottom' => 0,
                'margin-left'   => 0,
                // Aktifkan mode kompatibilitas untuk hasil render yang lebih konsisten
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]); // ukuran kecil, bisa disesuaikan untuk printer TM-U220

        return $pdf->stream("invoice_{$transaction->transaction_code}.pdf");
    }
}
