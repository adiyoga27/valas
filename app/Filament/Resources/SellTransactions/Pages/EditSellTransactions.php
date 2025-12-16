<?php

namespace App\Filament\Resources\SellTransactions\Pages;

use App\Filament\Resources\SellTransactions\SellTransactionsResource;
use App\Models\SellTransactionItem;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditSellTransactions extends EditRecord
{
    protected static string $resource = SellTransactionsResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['items'] = $this->record
            ->items
            ->map(fn($item) => [
                'currency_id'   => $item->currency_id,
                'currency_code' => $item->currency_code,
                'currency_name' => $item->currency_name,
                'currency_flag' => $item->currency_flag,
                'sell_rate'      => $item->sell_rate,
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
                'customer_name' => $data['customer_name'],
                'notes' => $data['notes'] ?? null,
                'additional_amounts' => $additionalAmounts,
            ]);

            // Reset items lama
            $record->items()->delete();

            $total = 0;

            foreach ($items as $item) {
                $subtotal = ($item['qty'] ?? 0) * ($item['sell_rate'] ?? 0);
                $total += $subtotal;

                SellTransactionItem::create([
                    'sell_transaction_id' => $record->id,
                    'currency_id'   => $item['currency_id'],
                    'currency_code' => $item['currency_code'],
                    'currency_name' => $item['currency_name'],
                    'currency_flag' => $item['currency_flag'],
                    'sell_rate'      => $item['sell_rate'],
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
