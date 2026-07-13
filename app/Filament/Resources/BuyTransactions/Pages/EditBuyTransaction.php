<?php

namespace App\Filament\Resources\BuyTransactions\Pages;

use App\Filament\Resources\BuyTransactions\BuyTransactionResource;
use App\Models\BuyTransactionItem;
use App\Models\Office;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

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

            return $record;
        });
    }

    protected function getHeaderActions(): array
    {
        $actions = [
            DeleteAction::make(),
        ];

        if ($this->record->cdd()->exists()) {
            $actions[] = Action::make('editCdd')
                ->label('Edit CDD')
                ->icon('heroicon-o-document-check')
                ->color('warning')
                ->modalHeading('Edit Formulir Transaksi Tunai (CDD)')
                ->modalWidth('3xl')
                ->fillForm(fn () => $this->record->cdd->toArray())
                ->form($this->cddFormSchema())
                ->action(function (array $data): void {
                    $this->record->cdd->update($data);
                    Notification::make()->success()->title('Data CDD berhasil diperbarui.')->send();
                });
        }

        return $actions;
    }

    protected function cddFormSchema(): array
    {
        return [
            Select::make('jenis_nasabah')
                ->label('Jenis Nasabah')
                ->options([
                    'Perorangan WNI' => 'Perorangan WNI',
                    'Perorangan WNA' => 'Perorangan WNA',
                    'Korporasi-Resident' => 'Korporasi-Resident',
                    'Korporasi-Non Resident' => 'Korporasi-Non Resident',
                ])
                ->required(),
            TextInput::make('nama_lengkap')->label('Nama Lengkap')->required(),
            TextInput::make('npwp')->label('NPWP'),
            TextInput::make('cabang')->label('Kantor Pusat'),
            TextInput::make('nama_jalan')->label('Alamat (Nama Jalan)'),
            TextInput::make('rt_rw')->label('RT/RW'),
            TextInput::make('kecamatan')->label('Kecamatan'),
            TextInput::make('kabupaten')->label('Kabupaten'),
            TextInput::make('provinsi')->label('Provinsi'),
            TextInput::make('negara')->label('Negara'),
            TextInput::make('kode_pos')->label('Kode Pos'),
            Select::make('tujuan_transaksi')->label('Tujuan Transaksi')->options([
                'Tabungan' => 'Tabungan / Investasi',
                'Pajak' => 'Pembayaran Pajak',
                'Bisnis' => 'Bisnis',
            ])->required(),
            Select::make('hubungan_pemilik_dana')->label('Hubungan Pemilik Dana')->options([
                'Sendiri' => 'Rekening Sendiri',
                'Keluarga' => 'Keluarga Dekat',
            ])->required(),
            Select::make('sumber_dana')->label('Sumber Dana')->options([
                'Gaji' => 'Gaji / Penghasilan',
                'Usaha' => 'Hasil Usaha',
            ])->required(),
            TextInput::make('total_dana_tunai')->label('Total Jumlah Dana Tunai'),
            TextInput::make('no_telp')->label('No. Telp Pelaku'),
            TextInput::make('penghasilan_tahun')->label('Rata-rata Penghasilan/Tahun (Juta Rp)')->numeric(),
            Select::make('jenis_pekerjaan')->label('Jenis Pekerjaan')->options([
                'Pegawai Negeri' => 'Pegawai Negeri',
                'ABRI' => 'ABRI',
                'Pegawai Swasta' => 'Pegawai Swasta (termasuk pensiunan)',
                'Wiraswasta' => 'Wiraswasta',
                'Ibu Rumah Tangga' => 'Ibu Rumah Tangga',
                'Pelajar' => 'Pelajar',
                'Pedagang' => 'Pedagang',
                'Lainnya' => 'Lainnya',
            ])->reactive(),
            TextInput::make('jenis_pekerjaan_lainnya')->label('Sebutkan Jenis Pekerjaan')
                ->visible(fn ($get) => $get('jenis_pekerjaan') === 'Lainnya'),
            TextInput::make('nama_perusahaan')->label('Nama Perusahaan Tempat Bekerja'),
            TextInput::make('jabatan')->label('Jabatan'),
            Select::make('bentuk_hukum')->label('Bentuk Hukum Tempat Bekerja')->options([
                'CV' => 'CV',
                'PT' => 'PT',
                'Yayasan' => 'Yayasan',
                'Firma' => 'Firma',
                'Lainnya' => 'Lainnya',
            ])->reactive(),
            TextInput::make('bentuk_hukum_lainnya')->label('Sebutkan Bentuk Hukum')
                ->visible(fn ($get) => $get('bentuk_hukum') === 'Lainnya'),
            TextInput::make('bidang_usaha')->label('Bidang Usaha Korporasi'),
        ];
    }
}
