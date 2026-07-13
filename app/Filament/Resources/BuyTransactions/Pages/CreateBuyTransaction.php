<?php

namespace App\Filament\Resources\BuyTransactions\Pages;

use App\Filament\Resources\BuyTransactions\BuyTransactionResource;
use App\Models\BuyTransaction;
use App\Models\BuyTransactionCdd;
use App\Models\BuyTransactionItem;
use App\Models\Office;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\DB;

class CreateBuyTransaction extends CreateRecord
{
    protected static string $resource = BuyTransactionResource::class;
    protected  ?string $heading = 'Buat Transaksi Pembelian Mata Uang Asing';

    public ?array $cddData = null;

    public static function getEmptyStateHeading(): ?string
    {
        return 'Data transaksi masih kosong';
    }

    protected function cddThreshold(): float
    {
        return (float) (Office::first()?->buy_cdd_threshold ?? 0);
    }

    protected function calculateGrandTotal(): float
    {
        $state = $this->form->getRawState();
        $itemsTotal = collect($state['items'] ?? [])->sum(function ($item) {
            return ($item['qty'] ?? 0) * ($item['buy_rate'] ?? 0);
        });
        $additional = collect($state['additional_amounts'] ?? [])->sum('amount');
        return $itemsTotal + $additional;
    }

    protected function beforeCreate(): void
    {
        $threshold = $this->cddThreshold();
        $grandTotal = $this->calculateGrandTotal();

        if ($threshold > 0 && $grandTotal >= $threshold && $this->cddData === null) {
            $this->mountAction('cddModal');
            throw new Halt();
        }
    }

    protected function handleRecordCreation(array $data): BuyTransaction
    {
        return DB::transaction(function () use ($data) {
            $items = $data['items'] ?? [];
            $itemsTotal = 0;
            $additionalAmounts = $data['additional_amounts'] ?? [];
            unset($data['items'], $data['additional_amounts']);

            $transaction = BuyTransaction::create([
                'transaction_code' => $data['transaction_code'] ?? 'BUY-' . time(),
                'user_id' => $data['user_id'] ?? auth()->id(),
                'customer_name' => $data['customer_name'] ?? null,
                'passport_number' => $data['passport_number'] ?? null,
                'customer_address' => $data['customer_address'] ?? null,
                'customer_country' => $data['customer_country'] ?? null,
                'customer_birthdate' => $data['customer_birthdate'] ?? null,
                'notes' => $data['notes'] ?? null,
                'total_amount' => 0,
                'created_at' => $data['created_at'] ?? now(),
            ]);

            foreach ($items as $item) {
                $itemsTotal += ($item['qty'] * $item['buy_rate']);
                BuyTransactionItem::create([
                    'buy_transaction_id' => $transaction->id,
                    'currency_id' => $item['currency_id'],
                    'currency_code' => $item['currency_code'],
                    'currency_name' => $item['currency_name'],
                    'currency_flag' => $item['currency_flag'],
                    'buy_rate' => $item['buy_rate'] ?? 0,
                    'qty' => $item['qty'] ?? 0,
                    'total' => $item['qty'] * $item['buy_rate'],
                ]);
            }

            $additionalTotal = collect($additionalAmounts)->sum('amount');
            $grandTotal = $itemsTotal + $additionalTotal;

            $transaction->total_amount = $itemsTotal;
            $transaction->additional_amounts = $additionalAmounts;
            $transaction->grand_total = $grandTotal;
            $transaction->save();

            $cddData = $this->cddData;
            $threshold = $this->cddThreshold();
            if ($threshold > 0 && $grandTotal >= $threshold && !empty($cddData) && !empty(array_filter($cddData))) {
                BuyTransactionCdd::create(array_merge(
                    ['buy_transaction_id' => $transaction->id],
                    $cddData
                ));
            }

            $this->cddData = null;

            return $transaction;
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function cddModalAction(): Action
    {
        return Action::make('cddModal')
            ->label('Isi Formulir CDD')
            ->modalHeading('Formulir Transaksi Tunai (CDD)')
            ->modalDescription('Deteksi customer batas threshold 25.000 USD per bulan')
            ->modalSubmitActionLabel('Simpan & Lanjutkan')
            ->modalCancelActionLabel('Batal')
            ->modalWidth('3xl')
            ->form([
                Select::make('jenis_nasabah')
                    ->label('Jenis Nasabah')
                    ->options([
                        'Perorangan WNI' => 'Perorangan WNI',
                        'Perorangan WNA' => 'Perorangan WNA',
                        'Korporasi-Resident' => 'Korporasi-Resident',
                        'Korporasi-Non Resident' => 'Korporasi-Non Resident',
                    ])
                    ->required()
                    ->columnSpan(2),

                TextInput::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->required()
                    ->default(fn () => $this->form->getRawState()['customer_name'] ?? '')
                    ->columnSpan(2),

                TextInput::make('npwp')
                    ->label('NPWP')
                    ->default(fn () => $this->form->getRawState()['passport_number'] ?? '')
                    ->columnSpan(1),

                TextInput::make('cabang')
                    ->label('Kantor Pusat')
                    ->default(fn () => Office::first()?->name)
                    ->columnSpan(1),

                TextInput::make('nama_jalan')
                    ->label('Alamat (Nama Jalan)')
                    ->default(fn () => $this->form->getRawState()['customer_address'] ?? '')
                    ->columnSpan(2),

                TextInput::make('rt_rw')
                    ->label('RT/RW')
                    ->columnSpan(1),

                TextInput::make('kecamatan')
                    ->label('Kecamatan')
                    ->columnSpan(1),

                TextInput::make('kabupaten')
                    ->label('Kabupaten')
                    ->columnSpan(1),

                TextInput::make('provinsi')
                    ->label('Provinsi')
                    ->columnSpan(1),

                TextInput::make('negara')
                    ->label('Negara')
                    ->default(fn () => $this->form->getRawState()['customer_country'] ?? '')
                    ->columnSpan(1),

                TextInput::make('kode_pos')
                    ->label('Kode Pos')
                    ->columnSpan(1),

                Select::make('tujuan_transaksi')
                    ->label('Tujuan Transaksi')
                    ->options([
                        'Tabungan' => 'Tabungan / Investasi',
                        'Pajak' => 'Pembayaran Pajak',
                        'Bisnis' => 'Bisnis',
                    ])
                    ->required()
                    ->columnSpan(1),

                Select::make('hubungan_pemilik_dana')
                    ->label('Hubungan Pemilik Dana')
                    ->options([
                        'Sendiri' => 'Rekening Sendiri',
                        'Keluarga' => 'Keluarga Dekat',
                    ])
                    ->required()
                    ->columnSpan(1),

                Select::make('sumber_dana')
                    ->label('Sumber Dana')
                    ->options([
                        'Gaji' => 'Gaji / Penghasilan',
                        'Usaha' => 'Hasil Usaha',
                    ])
                    ->required()
                    ->columnSpan(1),

                TextInput::make('total_dana_tunai')
                    ->label('Total Jumlah Dana Tunai')
                    ->columnSpan(1),

                TextInput::make('no_telp')
                    ->label('No. Telp Pelaku')
                    ->columnSpan(1),

                TextInput::make('penghasilan_tahun')
                    ->label('Rata-rata Penghasilan/Tahun (Juta Rp)')
                    ->numeric()
                    ->columnSpan(1),

                Select::make('jenis_pekerjaan')
                    ->label('Jenis Pekerjaan')
                    ->options([
                        'Pegawai Negeri' => 'Pegawai Negeri',
                        'ABRI' => 'ABRI',
                        'Pegawai Swasta' => 'Pegawai Swasta (termasuk pensiunan)',
                        'Wiraswasta' => 'Wiraswasta',
                        'Ibu Rumah Tangga' => 'Ibu Rumah Tangga',
                        'Pelajar' => 'Pelajar',
                        'Pedagang' => 'Pedagang',
                        'Lainnya' => 'Lainnya',
                    ])
                    ->reactive()
                    ->columnSpan(1),

                TextInput::make('jenis_pekerjaan_lainnya')
                    ->label('Sebutkan Jenis Pekerjaan')
                    ->visible(fn ($get) => $get('jenis_pekerjaan') === 'Lainnya')
                    ->columnSpan(1),

                TextInput::make('nama_perusahaan')
                    ->label('Nama Perusahaan Tempat Bekerja')
                    ->columnSpan(2),

                TextInput::make('jabatan')
                    ->label('Jabatan')
                    ->columnSpan(1),

                Select::make('bentuk_hukum')
                    ->label('Bentuk Hukum Tempat Bekerja')
                    ->options([
                        'CV' => 'CV',
                        'PT' => 'PT',
                        'Yayasan' => 'Yayasan',
                        'Firma' => 'Firma',
                        'Lainnya' => 'Lainnya',
                    ])
                    ->reactive()
                    ->columnSpan(1),

                TextInput::make('bentuk_hukum_lainnya')
                    ->label('Sebutkan Bentuk Hukum')
                    ->visible(fn ($get) => $get('bentuk_hukum') === 'Lainnya')
                    ->columnSpan(1),

                TextInput::make('bidang_usaha')
                    ->label('Bidang Usaha Korporasi')
                    ->columnSpan(2),
            ])
            ->action(function (array $data): void {
                $this->cddData = $data;
                $this->create();
            });
    }
}
