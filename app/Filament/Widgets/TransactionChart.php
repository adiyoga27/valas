<?php

namespace App\Filament\Widgets;

use App\Models\BuyTransaction;
use App\Models\SellTransaction;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class TransactionChart extends ChartWidget
{
    protected  ?string $heading = 'Grafik Pembelian & Penjualan';

    protected static ?int $sort = 1;

    /** FULL WIDTH */
    protected int | string | array $columnSpan = 'full';

    public ?string $filter = null;

    /**
     * Dropdown filter tahun
     */
    protected function getFilters(): ?array
    {
        $years = range(now()->year, now()->year - 5);

        return collect($years)->mapWithKeys(fn ($year) => [
            $year => (string) $year,
        ])->toArray();
    }

    protected function getData(): array
    {
        $year = $this->filter ?? now()->year;

        $months = collect(range(1, 12))->map(fn ($month) =>
            Carbon::create()->month($month)->translatedFormat('F')
        );

        $buyData  = $this->getMonthlyTotal(BuyTransaction::class, $year);
        $sellData = $this->getMonthlyTotal(SellTransaction::class, $year);

        return [
            'datasets' => [
                [
                    'label' => 'Pembelian',
                    'data' => $buyData,
                    'borderColor' => '#16a34a',          // hijau
                    'backgroundColor' => 'rgba(22,163,74,0.15)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Penjualan',
                    'data' => $sellData,
                    'borderColor' => '#dc2626',          // merah
                    'backgroundColor' => 'rgba(220,38,38,0.15)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    /**
     * Ambil total per bulan
     */
    protected function getMonthlyTotal(string $model, int $year): array
    {
        $data = $model::query()
            ->selectRaw('MONTH(created_at) as month, SUM(grand_total) as total')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->pluck('total', 'month');

        return collect(range(1, 12))->map(fn ($month) =>
            (int) ($data[$month] ?? 0)
        )->toArray();
    }

    protected function getType(): string
    {
        return 'line';
    }
}
