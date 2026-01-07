<?php

namespace App\Filament\Resources\BuyTransactions\Pages;

use App\Filament\Resources\BuyTransactions\BuyTransactionResource;
use App\Models\BuyTransactionItem;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditBuyTransaction extends EditRecord
{
    protected static string $resource = BuyTransactionResource::class;


    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['items'] = $this->record
            ->items
            ->map(fn ($item) => [
                'currency_id'   => $item->currency_id,
                'currency_code' => $item->currency_code,
                'currency_name' => $item->currency_name,
                'currency_flag' => $item->currency_flag,
                'buy_rate'      => $item->buy_rate,
                'qty'           => $item->qty,
                'total'         => $item->total,
            ])
            ->toArray();

        return $data;
    }


    protected function handleRecordUpdate(Model $record, array $data): Model
    {
          return DB::transaction(function () use ($record, $data) {
        $items = $data['items'] ?? [];
     $additionalAmounts = $data['additional_amounts'] ?? [];

            unset($data['items'], $data['additional_amounts']);

        // Update header
            $record->update([
                'transaction_code' => $data['transaction_code'],
                'created_at' => $data['created_at'],
                'user_id' => $data['user_id'],
                'customer_name' => $data['customer_name'],

                'passport_number' => $data['passport_number'] ?? null,
                'customer_address' => $data['customer_address'] ?? null,
                'customer_country' => $data['customer_country'] ?? null,
                'customer_birthdate' => $data['customer_birthdate'] ?? null,
                'notes' => $data['notes'] ?? null,
                'additional_amounts' => $additionalAmounts,
            ]);

        // Reset items lama
        $record->items()->delete();

        $total = 0;

        foreach ($items as $item) {
            $subtotal = ($item['qty'] ?? 0) * ($item['buy_rate'] ?? 0);
            $total += $subtotal;

            BuyTransactionItem::create([
                'buy_transaction_id' => $record->id,
                'currency_id'   => $item['currency_id'],
                'currency_code' => $item['currency_code'],
                'currency_name' => $item['currency_name'],
                'currency_flag' => $item['currency_flag'],
                'buy_rate'      => $item['buy_rate'],
                'qty'           => $item['qty'],
                'total'         => $subtotal,
            ]);
        }

        $record->update([
            'total_amount' => $total,
            'grand_total' => $total + collect($additionalAmounts)->sum('amount'),
        ]);

        return $record;
    });
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
