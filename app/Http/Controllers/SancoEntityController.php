<?php

namespace App\Http\Controllers;

use App\Models\SancoDataset;
use App\Models\SancoEntity;

class SancoEntityController extends Controller
{
    public function show(string $entityId)
    {
        $records = SancoEntity::where('entity_id', $entityId)->get();
        
        if ($records->isEmpty()) {
            abort(404);
        }

        $datasetNames = $records->pluck('dataset_name')->filter()->unique();
        $datasets = SancoDataset::whereIn('name', $datasetNames)->get()->keyBy('name');

        $tags = $records->map(function($record) use ($datasets) {
            $ds = $datasets->get($record->dataset_name);
            return $ds?->title ?? $record->dataset_name;
        })->filter()->unique()->values();

        $baseRecord = $records->first();
        
        $mergedProperties = [];
        $mergedAliases = collect();
        $mergedWeakAliases = collect();
        $mergedCountries = collect();
        $mergedAddresses = collect();
        
        $simpleFields = [
            'name', 'schema', 'birth_date', 'gender', 'birth_place', 
            'nationality', 'position', 'notes', 'emails', 'identifiers', 
            'first_seen', 'last_change', 'last_seen'
        ];

        foreach ($records as $r) {
            foreach ($simpleFields as $f) {
                if (empty($baseRecord->$f) && !empty($r->$f)) {
                    $baseRecord->$f = $r->$f;
                }
            }

            if ($r->aliases) {
                $mergedAliases = $mergedAliases->merge(explode(';', $r->aliases));
            }
            if ($r->weak_aliases) {
                $mergedWeakAliases = $mergedWeakAliases->merge(explode(';', $r->weak_aliases));
            }
            if ($r->countries) {
                $mergedCountries = $mergedCountries->merge(explode(',', $r->countries));
            }
            if ($r->addresses) {
                $mergedAddresses = $mergedAddresses->merge(explode(';', $r->addresses));
            }

            if (is_array($r->properties)) {
                foreach ($r->properties as $k => $v) {
                    if (!isset($mergedProperties[$k])) {
                        $mergedProperties[$k] = $v;
                    } else {
                        $existing = is_array($mergedProperties[$k]) ? $mergedProperties[$k] : [$mergedProperties[$k]];
                        $new = is_array($v) ? $v : [$v];
                        $mergedProperties[$k] = collect($existing)->merge($new)->unique()->values()->toArray();
                    }
                }
            }
        }

        $baseRecord->aliases = $mergedAliases->map(fn($x) => trim($x))->filter()->unique()->implode('; ');
        $baseRecord->weak_aliases = $mergedWeakAliases->map(fn($x) => trim($x))->filter()->unique()->implode('; ');
        $baseRecord->countries = $mergedCountries->map(fn($x) => trim($x))->filter()->unique()->implode(', ');
        $baseRecord->addresses = $mergedAddresses->map(fn($x) => trim($x))->filter()->unique()->implode('; ');

        activity('sanco_check')
            ->withProperties([
                'entity_name' => $baseRecord->name,
                'entity_id' => $baseRecord->entity_id,
                'datasets' => $datasetNames->implode(', '),
                'ip' => request()->ip(),
            ])
            ->log("Lihat detail: {$baseRecord->name}");

        return view('sanco-detail', [
            'entity' => $baseRecord,
            'tags' => $tags,
            'dataset_titles' => $tags->implode(', '),
            'opensanctionsUrl' => "https://www.opensanctions.org/entities/{$baseRecord->entity_id}",
            'properties' => $mergedProperties,
        ]);
    }
}
