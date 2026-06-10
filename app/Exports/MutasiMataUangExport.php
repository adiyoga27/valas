<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MutasiMataUangExport implements FromView, ShouldAutoSize
{
    public function __construct(
        public array $groupedMutations,
        public string $startDate,
        public string $endDate
    ) {}

    public function view(): View
    {
        return view('exports.mutasi-mata-uang', [
            'groupedMutations' => $this->groupedMutations,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }
}
