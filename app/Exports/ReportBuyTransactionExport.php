<?php

namespace App\Exports;

use App\Models\BuyTransaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportBuyTransactionExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
    }

    public function collection()
    {
        return BuyTransaction::whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ])
            ->orderBy('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Kode Transaksi',
            'Customer',
            'Total (IDR)',
        ];
    }

    public function map($row): array
    {
        return [
            $row->created_at->format('d-m-Y H:i'),
            $row->transaction_code,
            $row->customer_name,
            $row->grand_total,
        ];
    }
}
