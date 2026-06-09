<?php

namespace App\Http\Controllers;

use App\Models\SancoDataset;
use App\Models\SancoEntity;

class SancoEntityController extends Controller
{
    public function show(string $entityId)
    {
        $record = SancoEntity::where('entity_id', $entityId)->firstOrFail();
        $dataset = SancoDataset::where('name', $record->dataset_name)->first();
        $tags = collect([$dataset?->title ?? $record->dataset_name])->filter()->unique()->values();

        activity('sanco_check')
            ->withProperties([
                'entity_name' => $record->name,
                'entity_id' => $record->entity_id,
                'dataset' => $record->dataset_name,
                'ip' => request()->ip(),
            ])
            ->log("Lihat detail: {$record->name}");

        return view('sanco-detail', [
            'entity' => $record,
            'dataset' => $dataset,
            'tags' => $tags,
            'opensanctionsUrl' => "https://www.opensanctions.org/entities/{$record->entity_id}",
            'properties' => $record->properties ?? [],
        ]);
    }
}
