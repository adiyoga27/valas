<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SancoDataset;
use App\Models\SancoEntity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SancoController extends Controller
{
    public function datasets()
    {
        $datasets = SancoDataset::orderBy('name')->paginate(20);
        $totalEntities = SancoEntity::count();
        return view('admin.sanco.datasets', compact('datasets', 'totalEntities'));
    }

    public function check(Request $request)
    {
        $keyword = $request->get('keyword', '');
        $results = null;
        $searched = false;
        $error = null;
        $source = null;
        $totalEntities = SancoEntity::count();

        if ($request->filled('keyword') && strlen($keyword) >= 2) {
            $searched = true;
            try { $results = $this->searchLocal($keyword); $source = 'lokal'; } catch (\Exception $e) { $error = null; }
            if (empty($results)) {
                try { $results = $this->searchFuzzy($keyword); if (!empty($results)) $source = 'lokal (fuzzy)'; } catch (\Exception $e) { }
            }
            if (empty($results)) {
                $apiKey = config('services.opensanctions.api_key');
                if (filled($apiKey)) {
                    try { $apiResults = $this->searchApi($keyword, $apiKey); if (!empty($apiResults)) { $results = $apiResults; $source = 'API'; } } catch (\Exception $e) { }
                }
            }
        }

        return view('admin.sanco.check', compact('keyword', 'results', 'searched', 'error', 'source', 'totalEntities'));
    }

    protected function searchLocal(string $keyword): array
    {
        $results = SancoEntity::query()
            ->where('name', 'like', "%{$keyword}%")
            ->orWhere('aliases', 'like', "%{$keyword}%")
            ->limit(60)->get()->map(fn($e) => $this->formatResult($e))->all();
        return $this->mergeDuplicates($results);
    }

    protected function searchFuzzy(string $keyword): array
    {
        if (strlen($keyword) <= 3) return [];
        $prefix = substr($keyword, 0, 3);
        $entities = SancoEntity::where('name', 'like', "%{$prefix}%")->limit(200)->get();
        $scored = $entities->map(function($e) use ($keyword) {
            $lev = levenshtein(strtolower($keyword), strtolower($e->name));
            $maxLen = max(strlen($keyword), strlen($e->name));
            $score = 1 - ($lev / ($maxLen > 0 ? $maxLen : 1));
            return ['entity' => $e, 'score' => $score];
        })->filter(fn($i) => $i['score'] >= 0.6)->sortByDesc('score')->take(10)->map(fn($i) => $this->formatResult($i['entity']));
        return $scored->all();
    }

    protected function searchApi(string $keyword, string $apiKey): array
    {
        $response = Http::timeout(30)->retry(2, 1000)->withHeader('Authorization', 'ApiKey ' . $apiKey)
            ->get('https://api.opensanctions.org/search/sanctions', ['q' => $keyword, 'limit' => 50]);
        if (!$response->successful()) return [];
        $data = $response->json();
        return collect($data['results'] ?? [])->map(function($item) {
            $props = $item['properties'] ?? [];
            return ['id' => $item['id'] ?? '-', 'caption' => $item['caption'] ?? '-', 'schema' => $item['schema'] ?? '-',
                'datasets' => implode(', ', $item['datasets'] ?? []),
                'country' => implode(', ', $props['country'] ?? $props['nationality'] ?? []),
                'birth_date' => implode(', ', $props['birthDate'] ?? []),
                'detail_url' => route('sanco.entity.show', $item['id'] ?? ''),
                'opensanctions_url' => "https://www.opensanctions.org/entities/" . ($item['id'] ?? ''),
            ];
        })->values()->all();
    }

    protected function formatResult(SancoEntity $item): array
    {
        return ['id' => $item->entity_id, 'caption' => $item->name, 'schema' => $item->schema ?? '-',
            'datasets' => $item->dataset_title ?? $item->dataset_name,
            'country' => $item->countries ?? '-', 'birth_date' => $item->birth_date ?? '-',
            'aliases' => $item->aliases, 'birth_place' => $item->birth_place,
            'gender' => $item->gender, 'nationality' => $item->nationality,
            'detail_url' => route('sanco.entity.show', $item->entity_id),
            'opensanctions_url' => "https://www.opensanctions.org/entities/{$item->entity_id}",
        ];
    }

    protected function mergeDuplicates(array $results): array
    {
        $grouped = [];
        foreach ($results as $row) {
            $id = $row['id'];
            if (!isset($grouped[$id])) { $grouped[$id] = $row; continue; }
            $existingD = collect(explode(', ', $grouped[$id]['datasets']))->filter()->unique();
            $newD = collect(explode(', ', $row['datasets']))->filter()->unique();
            $grouped[$id]['datasets'] = $existingD->merge($newD)->unique()->sort()->implode(', ');
            if (strlen($row['caption']) > strlen($grouped[$id]['caption'])) $grouped[$id]['caption'] = $row['caption'];
        }
        return array_values($grouped);
    }
}
