<?php

namespace App\Filament\Pages;

use App\Models\BuyTransactionItem;
use App\Models\Currency;
use App\Models\SellTransactionItem;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class MutasiMataUang extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationLabel = 'Mutasi Mata Uang';
    protected static ?string $title = 'Mutasi Mata Uang';
    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi';

    protected string $view = 'filament.pages.mutasi-mata-uang';

    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?int $currencyId = null;
    public ?string $keyword = null;

    public array $groupedMutations = [];
    public bool $searched = false;
    public int $totalRecords = 0;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(4)
            ->components([
                Forms\Components\DatePicker::make('startDate')
                    ->label('Tgl Awal')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y'),

                Forms\Components\DatePicker::make('endDate')
                    ->label('Tgl Akhir')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y'),

                Forms\Components\Select::make('currencyId')
                    ->label('Mata Uang')
                    ->options(
                        Currency::where('is_active', true)
                            ->get()
                            ->mapWithKeys(fn ($c) => [$c->id => $c->code . ' - ' . $c->name])
                    )
                    ->searchable()
                    ->placeholder('Semua'),

                Forms\Components\TextInput::make('keyword')
                    ->label('Keyword')
                    ->placeholder('No. Trx / Nama...'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    $this->loadData();
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.mutasi-mata-uang', [
                        'groupedMutations' => $this->groupedMutations,
                        'startDate' => $this->startDate,
                        'endDate' => $this->endDate,
                    ]);
                    return response()->streamDownload(fn () => print($pdf->output()), 'mutasi-mata-uang-' . date('YmdHis') . '.pdf');
                }),

            Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->action(function () {
                    $this->loadData();
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\MutasiMataUangExport($this->groupedMutations, $this->startDate, $this->endDate), 
                        'mutasi-mata-uang-' . date('YmdHis') . '.xlsx'
                    );
                }),

            Action::make('submit')
                ->label('Submit Filter')
                ->icon('heroicon-o-funnel')
                ->color('primary')
                ->action(fn () => $this->loadData()),
        ];
    }

    public function loadData(): void
    {
        $this->searched = true;
        $this->groupedMutations = [];
        $this->totalRecords = 0;

        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        // Query for previous balances
        $buyBefore = DB::table('buy_transaction_items as bti')
            ->join('buy_transactions as bt', 'bti.buy_transaction_id', '=', 'bt.id')
            ->select('bti.currency_code', DB::raw('SUM(bti.qty) as total_buy'))
            ->where('bt.created_at', '<', $start);
            
        $sellBefore = DB::table('sell_transaction_items as sti')
            ->join('sell_transactions as st', 'sti.sell_transaction_id', '=', 'st.id')
            ->select('sti.currency_code', DB::raw('SUM(sti.qty) as total_sell'))
            ->where('st.created_at', '<', $start);

        $buyQuery = DB::table('buy_transaction_items as bti')
            ->join('buy_transactions as bt', 'bti.buy_transaction_id', '=', 'bt.id')
            ->select(
                'bti.currency_code',
                'bti.currency_name',
                'bt.created_at as date',
                'bt.transaction_code',
                DB::raw('bti.qty as buy_qty'),
                DB::raw('0 as sell_qty'),
                'bti.buy_rate as rate',
                DB::raw("'BUY' as type"),
                'bt.customer_name'
            )
            ->whereBetween('bt.created_at', [$start, $end]);

        $sellQuery = DB::table('sell_transaction_items as sti')
            ->join('sell_transactions as st', 'sti.sell_transaction_id', '=', 'st.id')
            ->select(
                'sti.currency_code',
                'sti.currency_name',
                'st.created_at as date',
                'st.transaction_code',
                DB::raw('0 as buy_qty'),
                DB::raw('sti.qty as sell_qty'),
                'sti.sell_rate as rate',
                DB::raw("'SELL' as type"),
                'st.customer_name'
            )
            ->whereBetween('st.created_at', [$start, $end]);

        if ($this->currencyId) {
            $currency = Currency::find($this->currencyId);
            if ($currency) {
                $buyBefore->where('bti.currency_code', $currency->code);
                $sellBefore->where('sti.currency_code', $currency->code);
                $buyQuery->where('bti.currency_code', $currency->code);
                $sellQuery->where('sti.currency_code', $currency->code);
            }
        }

        $bbBuy = $buyBefore->groupBy('bti.currency_code')->pluck('total_buy', 'currency_code');
        $bbSell = $sellBefore->groupBy('sti.currency_code')->pluck('total_sell', 'currency_code');

        $union = $buyQuery->unionAll($sellQuery);
        $raw = DB::table(DB::raw("({$union->toSql()}) as m"))
            ->mergeBindings($union)
            ->orderBy('currency_code')
            ->orderBy('date')
            ->get();

        if ($this->keyword) {
            $kw = strtolower($this->keyword);
            $raw = $raw->filter(fn ($r) =>
                str_contains(strtolower($r->transaction_code), $kw) ||
                str_contains(strtolower($r->customer_name), $kw) ||
                str_contains(strtolower($r->currency_code), $kw)
            );
        }

        $activeRates = Currency::where('is_active', true)->pluck('buy_rate', 'code');
        
        $mutationsByCurrency = [];

        // First, initialize the currencies that have mutations in this period
        foreach ($raw as $r) {
            $code = $r->currency_code;
            if (!isset($mutationsByCurrency[$code])) {
                $mutationsByCurrency[$code] = [
                    'currency_code' => $code,
                    'currency_name' => $r->currency_name,
                    'beginning_balance' => ($bbBuy[$code] ?? 0) - ($bbSell[$code] ?? 0),
                    'current_stock' => ($bbBuy[$code] ?? 0) - ($bbSell[$code] ?? 0),
                    'total_buy' => 0,
                    'total_sell' => 0,
                    'rate' => $activeRates[$code] ?? 0,
                    'items' => []
                ];
            }
            
            $mutationsByCurrency[$code]['current_stock'] += $r->buy_qty - $r->sell_qty;
            $mutationsByCurrency[$code]['total_buy'] += $r->buy_qty;
            $mutationsByCurrency[$code]['total_sell'] += $r->sell_qty;

            $mutationsByCurrency[$code]['items'][] = [
                'date'         => Carbon::parse($r->date)->format('j F Y'),
                'trx_code'     => $r->transaction_code,
                'customer'     => $r->customer_name ?? 'Head Office', // Assuming Branch/Customer column
                'buy'          => $r->buy_qty,
                'sell'         => $r->sell_qty,
                'rate'         => $r->rate,
                'stock'        => $mutationsByCurrency[$code]['current_stock'],
                'valuation'    => $mutationsByCurrency[$code]['current_stock'] * $r->rate,
                'type'         => $r->type,
            ];
            
            $this->totalRecords++;
        }

        $this->groupedMutations = $mutationsByCurrency;
    }
}
