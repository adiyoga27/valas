<?php

namespace App\Http\Controllers;

use App\Models\BuyTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class BuyTransactionPrintController extends Controller
{
    // public function print($record)
    // {
    //     $transaction = BuyTransaction::with('items')->findOrFail($record);

    //     // Nama printer lokal (sesuai yang terinstall di Windows)
    //     $printerName = "TM-U220";

    //     try {
    //         $connector = new WindowsPrintConnector($printerName);
    //         $printer = new Printer($connector);

    //         // Cetak header
    //         $printer->setJustification(Printer::JUSTIFY_CENTER);
    //         $printer->text("INVOICE PEMBELIAN\n");
    //         $printer->text("------------------------------\n");

    //         $printer->setJustification(Printer::JUSTIFY_LEFT);
    //         $printer->text("Kode: " . $transaction->transaction_code . "\n");
    //         $printer->text("Tanggal: " . $transaction->created_at->format('d M Y H:i:s') . "\n");
    //         $printer->text("Customer: " . ($transaction->customer_name ?? 'N/A') . "\n");
    //         // $printer->text("Dibuat Oleh: " . ($transaction->user->name ?? 'Sistem') . "\n");
    //         $printer->text("------------------------------\n");

    //         // Cetak item
    //         foreach ($transaction->items as $item) {
    //             $line = str_pad($item->currency_name, 10) .
    //                     str_pad($item->qty, 5, ' ', STR_PAD_LEFT) .
    //                     str_pad(number_format($item->buy_rate, 2), 10, ' ', STR_PAD_LEFT) .
    //                     str_pad(number_format($item->total, 2), 12, ' ', STR_PAD_LEFT);
    //             $printer->text($line . "\n");
    //         }

    //         $printer->text("------------------------------\n");
    //         $printer->setJustification(Printer::JUSTIFY_RIGHT);
    //         $printer->text("TOTAL: " . number_format($transaction->total_amount, 2) . "\n");

    //         // Footer
    //         $printer->setJustification(Printer::JUSTIFY_CENTER);
    //         $printer->text("\nTerima Kasih\n");

    //         $printer->cut();
    //         $printer->close();

    //         return response()->json(['status' => 'success', 'message' => 'Invoice berhasil dicetak']);
    //     } catch (\Exception $e) {
    //         return response()->json(['status' => 'error', 'message' => 'Gagal mencetak: ' . $e->getMessage()]);
    //     }
    // }

    public function print($record)
    {
        $transaction = BuyTransaction::with('items', 'user')->findOrFail($record);

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
    ]);// ukuran kecil, bisa disesuaikan untuk printer TM-U220

        return $pdf->stream("invoice_{$transaction->transaction_code}.pdf");
    }
}
