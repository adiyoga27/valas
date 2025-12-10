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

    /**
     * Override proses create
     */
    protected function handleRecordCreation(array $data): BuyTransaction
    {
        return DB::transaction(function () use ($data) {
            $items = $data['items'] ?? [];
            unset($data['items']);
            $transaction = BuyTransaction::create([
                'transaction_code' => $data['transaction_code'] ?? 'BUY-' . time(),
                'user_id' => $data['user_id'] ?? auth()->id(),
                'customer_name' => $data['customer_name'] ?? null,
                'total_amount' => $data['total_amount'] ?? collect($items)->sum(fn($i) => ($i['total'] ?? $i['total_amount'] ?? ($i['buy_rate'] * $i['qty'] ?? 0))),
            ]);
            foreach ($items as $item) {
                $itemTotal = $item['total'] ?? $item['total_amount'] ?? (($item['buy_rate'] ?? 0) * ($item['qty'] ?? 0));

                BuyTransactionItem::create([
                    'buy_transaction_id' => $transaction->id,
                    'currency_id' => $item['currency_id'],
                    'currency_code' => $item['currency_code'],
                    'currency_name' => $item['currency_name'],
                    'currency_flag' => $item['currency_flag'],
                    'buy_rate' => $item['buy_rate'] ?? 0,
                    'qty' => $item['qty'] ?? 0,
                    'total' => $itemTotal,
                ]);
            }
            return $transaction;
        });
    }
    protected function getRedirectUrl(): string
    {
        // Mengarahkan ke halaman 'view' dari resource, menggunakan ID dari record yang baru dibuat ($this->record).
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
        
    }
}
