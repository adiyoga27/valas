<?php

namespace App\Filament\Resources\BuyTransactions\Pages;

use App\Filament\Resources\BuyTransactions\BuyTransactionResource;
use App\Models\BuyTransactionCdd;
use App\Models\BuyTransactionItem;
use App\Models\Office;
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

        if ($this->record->cdd) {
            $data['cdd'] = [
                'jenis_nasabah' => $this->record->cdd->jenis_nasabah,
                'nama_lengkap' => $this->record->cdd->nama_lengkap,
                'npwp' => $this->record->cdd->npwp,
                'nama_jalan' => $this->record->cdd->nama_jalan,
                'rt_rw' => $this->record->cdd->rt_rw,
                'kecamatan' => $this->record->cdd->kecamatan,
                'kabupaten' => $this->record->cdd->kabupaten,
                'provinsi' => $this->record->cdd->provinsi,
                'cabang' => $this->record->cdd->cabang,
                'tujuan_transaksi' => $this->record->cdd->tujuan_transaksi,
                'hubungan_pemilik_dana' => $this->record->cdd->hubungan_pemilik_dana,
                'sumber_dana' => $this->record->cdd->sumber_dana,
                'total_dana_tunai' => $this->record->cdd->total_dana_tunai,
                'no_telp' => $this->record->cdd->no_telp,
            ];
        }

        return $data;
    }


    protected function handleRecordUpdate(Model $record, array $data): Model
    {
          return DB::transaction(function () use ($record, $data) {
        $items = $data['items'] ?? [];
     $additionalAmounts = $data['additional_amounts'] ?? [];
     $cddData = $data['cdd'] ?? [];

            unset($data['items'], $data['additional_amounts'], $data['cdd']);

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

        $grandTotal = $total + collect($additionalAmounts)->sum('amount');

        $record->update([
            'total_amount' => $total,
            'grand_total' => $grandTotal,
        ]);

        $threshold = (float) (Office::first()?->cdd_threshold ?? 0);
        if ($threshold > 0 && $grandTotal >= $threshold && !empty(array_filter($cddData))) {
            BuyTransactionCdd::updateOrCreate(
                ['buy_transaction_id' => $record->id],
                [
                    'jenis_nasabah' => $cddData['jenis_nasabah'] ?? null,
                    'nama_lengkap' => $cddData['nama_lengkap'] ?? null,
                    'npwp' => $cddData['npwp'] ?? null,
                    'nama_jalan' => $cddData['nama_jalan'] ?? null,
                    'rt_rw' => $cddData['rt_rw'] ?? null,
                    'kecamatan' => $cddData['kecamatan'] ?? null,
                    'kabupaten' => $cddData['kabupaten'] ?? null,
                    'provinsi' => $cddData['provinsi'] ?? null,
                    'cabang' => $cddData['cabang'] ?? null,
                    'tujuan_transaksi' => $cddData['tujuan_transaksi'] ?? null,
                    'hubungan_pemilik_dana' => $cddData['hubungan_pemilik_dana'] ?? null,
                    'sumber_dana' => $cddData['sumber_dana'] ?? null,
                    'total_dana_tunai' => $cddData['total_dana_tunai'] ?? null,
                    'no_telp' => $cddData['no_telp'] ?? null,
                ]
            );
        } elseif ($record->cdd && empty(array_filter($cddData))) {
            $record->cdd->delete();
        }

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
