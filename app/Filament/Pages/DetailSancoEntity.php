<?php

namespace App\Filament\Pages;

use App\Models\SancoDataset;
use App\Models\SancoEntity;
use Filament\Pages\Page;

class DetailSancoEntity extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = null;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Detail';

    protected string $view = 'filament.pages.detail-sanco-entity';

    public ?array $entity = null;

    public function mount(): void
    {
        $entityId = request()->query('entityId');

        if (!$entityId) {
            return;
        }

        $record = SancoEntity::where('entity_id', $entityId)->first();

        if ($record) {
            $dataset = SancoDataset::where('name', $record->dataset_name)->first();

            $this->entity = [
                'id' => $record->entity_id,
                'caption' => $record->name,
                'schema' => $record->schema ?? '-',
                'datasets' => $record->dataset_name,
                'dataset_title' => $dataset?->title,
                'country' => $record->countries ?? '-',
                'birth_date' => $record->birth_date ?? '-',
                'aliases' => $record->aliases,
                'weak_aliases' => $record->weak_aliases,
                'birth_place' => $record->birth_place,
                'gender' => $record->gender,
                'nationality' => $record->nationality,
                'position' => $record->position,
                'notes' => $record->notes,
                'addresses' => $record->addresses,
                'identifiers' => $record->identifiers,
                'emails' => $record->emails,
                'first_seen' => $record->first_seen,
                'last_seen' => $record->last_seen,
                'last_change' => $record->last_change,
                'opensanctions_url' => "https://www.opensanctions.org/entities/{$record->entity_id}",
            ];

            activity('sanco_check')
                ->withProperties([
                    'entity_name' => $record->name,
                    'entity_id' => $record->entity_id,
                    'dataset' => $record->dataset_name,
                    'ip' => request()->ip(),
                ])
                ->log("Lihat detail: {$record->name}");
        }
    }
}
