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

    public array $mutations = [];
    public array $balances = [];
    public bool $searched = false;

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
            Action::make('submit')
                ->label('Tampilkan')
                ->icon('heroicon-o-magnifying-glass')
                ->color('primary')
                ->action(fn () => $this->loadData()),
        ];
    }

    public function loadData(): void
    {
        $this->searched = true;
        $this->mutations = [];
        $this->balances = [];

        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

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
                $buyQuery->where('bti.currency_code', $currency->code);
                $sellQuery->where('sti.currency_code', $currency->code);
            }
        }

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

        $stock = [];
        foreach ($raw as $r) {
            $code = $r->currency_code;
            if (!isset($stock[$code])) $stock[$code] = 0;
            $stock[$code] += $r->buy_qty - $r->sell_qty;

            $this->mutations[] = [
                'currency_code' => $r->currency_code,
                'currency_name' => $r->currency_name,
                'date'         => Carbon::parse($r->date)->format('d/m/Y H:i'),
                'trx_code'     => $r->transaction_code,
                'buy'          => $r->buy_qty > 0 ? number_format($r->buy_qty, 2, ',', '.') : '-',
                'sell'         => $r->sell_qty > 0 ? number_format($r->sell_qty, 2, ',', '.') : '-',
                'rate'         => number_format($r->rate, 2, ',', '.'),
                'rate_raw'     => $r->rate,
                'stock'        => $stock[$code],
                'valuation'    => $stock[$code] * $r->rate,
                'type'         => $r->type,
            ];
        }

        $this->balances = $stock;
    }
}
