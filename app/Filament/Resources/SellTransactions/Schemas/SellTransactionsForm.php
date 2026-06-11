<?php

namespace App\Filament\Resources\SellTransactions\Schemas;

use App\Models\Currency;
use App\Models\SancoEntity;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class SellTransactionsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                DateTimePicker::make('created_at')
                    ->label('Tanggal Transaksi')
                    ->default(now())
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->columnSpan(2)
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        if (! $state) return;
                        $date = \Carbon\Carbon::parse($state);
                        $today = $date->format('Ymd');
                        $countToday = \App\Models\SellTransaction::where('transaction_code', 'like', "SELL-{$today}%")->count() + 1;
                        $set('transaction_code', "SELL-{$today}1000{$countToday}");
                    }),

                TextInput::make('transaction_code')
                    ->default(function () {
                        $today = now()->format('Ymd');
                        $countToday = \App\Models\SellTransaction::where('transaction_code', 'like', "SELL-{$today}%")->count() + 1;
                        return "SELL-{$today}1000{$countToday}";
                    })
                    ->disabled()
                    ->columnSpan(2)
                    ->dehydrated()
                    ->label("No. Invoice"),

                TextInput::make('customer_name')
                    ->label('Customer Name')
                    ->columnSpan(2)
                    ->required()->label("Nama Pelanggan")
                    ->live(onBlur: true),

                \Filament\Schemas\Components\Actions::make([
                    \Filament\Actions\Action::make('check_pep')
                        ->label('Check PEP/DTTOT')
                        ->icon('heroicon-m-magnifying-glass')
                        ->action(function (Get $get, Set $set) {
                            $state = $get('customer_name');
                            
                            if (empty(trim($state ?? '')) || strlen(trim($state)) < 2) {
                                $set('_pep_matches', null);
                                return;
                            }

                            $searchTerm = '%' . trim($state) . '%';
                            $entities = \App\Models\SancoEntity::where(function ($query) use ($searchTerm) {
                                    $query->where('name', 'like', $searchTerm)
                                          ->orWhere('aliases', 'like', $searchTerm)
                                          ->orWhere('weak_aliases', 'like', $searchTerm);
                                })
                                ->select('name', 'entity_id', 'dataset_title', 'dataset_name')
                                ->orderBy('entity_id')
                                ->limit(100)
                                ->get();

                            $result = [];
                            $groupedEntities = [];

                            foreach ($entities as $e) {
                                $dataset = $e->dataset_title ?? $e->dataset_name ?? '-';
                                if (!isset($groupedEntities[$e->entity_id])) {
                                    $groupedEntities[$e->entity_id] = [
                                        'name' => $e->name,
                                        'entity_id' => $e->entity_id,
                                        'datasets' => []
                                    ];
                                }
                                if (!in_array($dataset, $groupedEntities[$e->entity_id]['datasets'])) {
                                    $groupedEntities[$e->entity_id]['datasets'][] = $dataset;
                                }
                            }

                            foreach ($groupedEntities as $group) {
                                $result[] = [
                                    'name' => $group['name'],
                                    'dataset' => implode(', ', $group['datasets']),
                                    'entity_id' => $group['entity_id'],
                                ];
                            }

                            $set('_pep_matches', array_slice($result, 0, 10));
                        })
                ])->columnSpan(2),
                Placeholder::make('_pep_display')
                    ->label('PEP/DTTOT Check')
                    ->content(function (callable $get) {
                        $matches = $get('_pep_matches');

                        if ($matches === null) return '';

                        if (empty($matches)) {
                            return new \Illuminate\Support\HtmlString(
                                '<div style="padding:8px;border-radius:6px;background:#f0fdf4;border:1px solid #bbf7d0;font-size:13px;color:#15803d;">'
                                . 'Nama ini aman. Tidak terdaftar di database PEP/DTTOT.</div>'
                            );
                        }

                        $html = '<div style="max-height:200px;overflow-y:auto;border:1px solid #fecaca;border-radius:6px;padding:8px;background:#fef2f2;font-size:13px;">';
                        $html .= '<div style="color:#dc2626;font-weight:600;margin-bottom:4px;">Ditemukan ' . count($matches) . ' kecocokan:</div>';
                        foreach ($matches as $m) {
                            $url = route('sanco.entity.show', $m['entity_id']);
                            $html .= '<div style="padding:4px 0;border-bottom:1px solid #fecaca;">';
                            $html .= '<a href="' . $url . '" target="_blank" style="font-weight:600;color:#dc2626;text-decoration:none;">' . e($m['name']) . '</a>';
                            $html .= ' <span style="color:#6b7280;font-size:11px;">(' . e($m['dataset']) . ')</span>';
                            $html .= '</div>';
                        }
                        $html .= '</div>';
                        return new \Illuminate\Support\HtmlString($html);
                    })
                    ->columnSpan(2)
                    ->visible(fn (callable $get) => $get('_pep_matches') !== null),
                TextInput::make('passport_number')
                    ->label('Passport Number')
                    ->columnSpan(2)
                    ->nullable()->label("Nomor Passport / KTP"),
                TextInput::make('customer_address')
                    ->label('Customer Address')
                    ->columnSpan(2)
                    ->nullable()->label("Alamat Pelanggan"),
                TextInput::make('customer_country')
                    ->label('Customer Country')
                    ->columnSpan(2)
                    ->nullable()->label("Negara Pelanggan"),
               

                Textarea::make('notes')
                    ->label('Notes')
                    ->columnSpan(2)
                    ->rows(3)
                    ->nullable()
                    ->placeholder('Tambahkan catatan tambahan di sini...'),

                Hidden::make('user_id')
                    ->default(fn() => auth()->id()),
                Hidden::make('total_amount')
                    ->default(0),
                Hidden::make('grand_total')
                    ->default(0)
                    ->live(),

                Repeater::make('items')
                    ->dehydrated()
                    ->schema([
                        Grid::make(12)->schema([
                            Select::make('currency_id')
                                ->label('Currency')
                                ->required()
                                ->options(
                                    Currency::all()->where('sell_rate', '>', 0)->where('is_active', true)->pluck('display_name', 'id')
                                )
                                ->searchable()
                                ->reactive()
                                ->columnSpan(3)
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if (! $state) return;
                                    $currency = Currency::find($state);
                                    $set('currency_code', $currency->code);
                                    $set('currency_name', $currency->name);
                                    $set('currency_flag', $currency->flag);
                                    $set('sell_rate', $currency->sell_rate);
                                }),

                            Placeholder::make('currency_code_view')
                                ->label('Kode')
                                ->content(fn($get) => $get('currency_code'))
                                ->columnSpan(2),



                            TextInput::make('qty')
                                ->label('Jumlah')
                                ->dehydrated()
                                ->numeric()
                                ->columnSpan(2)
                                ->reactive()
                                ->debounce(800)
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    $set('total', $get('sell_rate') * $state);
                                    SellTransactionsForm::updateParentTotal($get, $set);
                                }),
                            Placeholder::make('sell_rate_view')
                                ->label('Kurs Jual')
                                ->content(fn($get) => number_format($get('sell_rate') ?? 0, 0, ',', '.'))
                                ->columnSpan(2),
                            Placeholder::make('total_view')
                                ->label('Total')
                                ->content(fn($get) => number_format($get('total') ?? 0, 0, ',', '.'))
                                ->columnSpan(2),
                        ]),
                    ])
                    ->columns(1)
                    ->columnSpanFull()
                    ->afterStateUpdated(function ($get, $set) {
                        SellTransactionsForm::updateParentTotal($get, $set);
                    }),

                Repeater::make('additional_amounts')
                    ->label('Biaya Tambahan')
                    ->dehydrated()
                    ->schema([
                        Grid::make(12)->schema([
                            TextInput::make('name')->label('Nama Biaya')->columnSpan(7),
                            TextInput::make('amount')->label('Jumlah')->numeric()->reactive()->columnSpan(5)->debounce(800)
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    SellTransactionsForm::updateGrandTotal($get, $set);
                                }),
                        ])
                    ])
                    ->columns(1)
                    ->columnSpanFull()
                    ->afterStateUpdated(fn($get, $set) => SellTransactionsForm::updateGrandTotal($get, $set))
                    ->addActionLabel('+ Tambah Biaya Tambahan'),

                Placeholder::make('total_display')
                    ->label('Total Transaksi')
                    ->content(fn($get) => 'Rp ' . number_format(collect($get('items'))->sum('total') ?? 0, 0, ',', '.'))
                    ->columnSpan(2),

                Placeholder::make('additional_display')
                    ->label('Total Biaya Tambahan')
                    ->content(fn($get) => 'Rp ' . number_format(collect($get('additional_amounts'))->sum('amount') ?? 0, 0, ',', '.'))
                    ->columnSpan(2),

                Placeholder::make('grand_total_display')
                    ->label('Grand Total')
                    ->content(fn($get) => 'Rp ' . number_format(collect($get('items'))->sum('total') + collect($get('additional_amounts'))->sum('amount') ?? 0, 0, ',', '.'))
                    ->extraAttributes(['class' => 'text-right text-3xl font-bold text-success'])
                    ->columnSpan(2),
            ]);
    }

    public static function updateGrandTotal($get, $set)
    {
        $itemsTotal = collect($get('items'))->sum('total');
        $additional = collect($get('additional_amounts'))->sum('amount');

        $set('total_amount', $itemsTotal);
        $set('grand_total', $itemsTotal + $additional);
    }

    public static function updateParentTotal($get, $set)
    {
        $itemsTotal = collect($get('items'))->sum('total');
        $set('total_amount', $itemsTotal);
        self::updateGrandTotal($get, $set);
    }
}
