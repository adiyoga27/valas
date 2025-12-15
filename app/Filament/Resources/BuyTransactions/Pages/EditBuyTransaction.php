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
        unset($data['items']);

        // Update header
        $record->update([
            'customer_name' => $data['customer_name'],
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
