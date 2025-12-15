<?php

namespace App\Filament\Resources\BuyTransactions\Pages;

use App\Filament\Resources\BuyTransactions\BuyTransactionResource;
use App\Models\BuyTransaction;
use App\Models\BuyTransactionItem;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateBuyTransaction extends CreateRecord
{
    protected static string $resource = BuyTransactionResource::class;



    public static function getPluralLabel(): string
    {
        return 'Transaksi Pembelian';
    }

    public static function getEmptyStateHeading(): ?string
    {
        return 'Data transaksi masih kosong';
    }
    
    /**
     * Override proses create
     */
    protected function handleRecordCreation(array $data): BuyTransaction
    {
        return DB::transaction(function () use ($data) {
            $items = $data['items'] ?? [];
            $totalAmount = 0;
            unset($data['items']);
            $transaction = BuyTransaction::create([
                'transaction_code' => $data['transaction_code'] ?? 'BUY-' . time(),
                'user_id' => $data['user_id'] ?? auth()->id(),
                'customer_name' => $data['customer_name'] ?? null,
                'total_amount' => 0,
            ]);
            foreach ($items as $item) {
                $totalAmount += ($item['qty'] * $item['buy_rate']);
                BuyTransactionItem::create([
                    'buy_transaction_id' => $transaction->id,
                    'currency_id' => $item['currency_id'],
                    'currency_code' => $item['currency_code'],
                    'currency_name' => $item['currency_name'],
                    'currency_flag' => $item['currency_flag'],
                    'buy_rate' => $item['buy_rate'] ?? 0,
                    'qty' => $item['qty'] ?? 0,
                    'total' => $item['qty'] * $item['buy_rate'] ,
                ]);
            }
            $transaction->total_amount = $totalAmount;
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
