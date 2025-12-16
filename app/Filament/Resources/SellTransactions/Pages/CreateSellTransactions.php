<?php

namespace App\Filament\Resources\SellTransactions\Pages;

use App\Filament\Resources\SellTransactions\SellTransactionsResource;
use App\Models\SellTransaction;
use App\Models\SellTransactionItem;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateSellTransactions extends CreateRecord
{
    protected static string $resource = SellTransactionsResource::class;
   // Properti ini mengatur judul/heading halaman Create
    protected  ?string $heading = 'Buat Transaksi Pembelian Mata Uang Asing'; // Gunakan $heading untuk Filament v3/v4

    
    public static function getEmptyStateHeading(): ?string
    {
        return 'Data transaksi masih kosong';
    }
    
    /**
     * Override proses create
     */
    protected function handleRecordCreation(array $data): SellTransaction
    {
      
        return DB::transaction(function () use ($data) {
            $items = $data['items'] ?? [];
            $itemsTotal = 0;
            $additionalAmounts = $data['additional_amounts'] ?? [];

            unset($data['items'], $data['additional_amounts']);
            $transaction = SellTransaction::create([
                'transaction_code' => $data['transaction_code'] ?? 'SELL-' . time(),
                'user_id' => $data['user_id'] ?? auth()->id(),
                'customer_name' => $data['customer_name'] ?? null,
                'notes' => $data['notes'] ?? null,
                'total_amount' => 0,
            ]);
            foreach ($items as $item) {
                $itemsTotal += ($item['qty'] * $item['sell_rate']);
                SellTransactionItem::create([
                    'sell_transaction_id' => $transaction->id,
                    'currency_id' => $item['currency_id'],
                    'currency_code' => $item['currency_code'],
                    'currency_name' => $item['currency_name'],
                    'currency_flag' => $item['currency_flag'],
                    'sell_rate' => $item['sell_rate'] ?? 0,
                    'qty' => $item['qty'] ?? 0,
                    'total' => $item['qty'] * $item['sell_rate'] ,
                ]);
            }

             // Hitung total biaya tambahan
        $additionalTotal = collect($additionalAmounts)->sum('amount');

        // Grand Total
        $grandTotal = $itemsTotal + $additionalTotal;

            $transaction->total_amount = $itemsTotal;
            $transaction->additional_amounts = $additionalAmounts;
            $transaction->grand_total = $grandTotal;
            $transaction->save();
            return $transaction;
        });
    }
    protected function getRedirectUrl(): string
    {
        // Mengarahkan ke halaman 'view' dari resource, menggunakan ID dari record yang baru dibuat ($this->record).
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
        
    }
}
