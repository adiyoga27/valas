<?php

namespace App\Exports;

use App\Models\BuyTransaction;
use App\Models\SellTransaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class TransactionReportExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected string $startDate,
        protected string $endDate
    ) {}

    public function collection(): Collection
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end   = Carbon::parse($this->endDate)->endOfDay();

        $buy = BuyTransaction::whereBetween('created_at', [$start, $end])
            ->get()
            ->map(fn ($t) => [
                $t->created_at->format('d-m-Y'),
                $t->transaction_code,
                'Pembelian',
                $t->customer_name,
                $t->grand_total,
            ]);

        $sell = SellTransaction::whereBetween('created_at', [$start, $end])
            ->get()
            ->map(fn ($t) => [
                $t->created_at->format('d-m-Y'),
                $t->transaction_code,
                'Penjualan',
                $t->customer_name,
                $t->grand_total,
            ]);

        return $buy->merge($sell);
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Kode Transaksi',
            'Jenis',
            'Customer',
            'Total',
        ];
    }
}
