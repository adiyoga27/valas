<?php

namespace App\Filament\Pages;

use App\Filament\Resources\SancoDatasets\SancoDatasetResource;
use App\Models\SancoEntity;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Http;

class CekNamaSanco extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static string|\UnitEnum|null $navigationGroup = 'PEP & DTTOT';

    protected static ?string $navigationLabel = 'Cek Nama';

    protected static ?string $title = 'Cek Nama PEP & DTTOT';

    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.pages.cek-nama-sanco';

    public ?string $keyword = '';

    public ?array $results = null;

    public ?bool $searched = false;

    public ?string $error = null;

    public ?string $source = null;

    public function getTotalEntities(): int
    {
        try {
            return SancoEntity::count();
        } catch (\Exception) {
            return 0;
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('keyword')
                    ->label('Nama / Kata Kunci')
                    ->placeholder('Masukkan nama orang atau perusahaan...')
                    ->required()
                    ->minLength(2)
                    ->helperText('Minimal 2 karakter. Gunakan nama lengkap untuk hasil lebih akurat.'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import_data')
                ->label('Import Data')
                ->icon('heroicon-o-cloud-arrow-down')
                ->color('gray')
                ->url(fn() => SancoDatasetResource::getUrl('index'))
                ->visible(fn() => $this->getTotalEntities() === 0),

            Action::make('search')
                ->label('Cari')
                ->icon('heroicon-o-magnifying-glass')
                ->color('primary')
                ->action(fn() => $this->search()),

            Action::make('back')
                ->label('Dataset')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn() => SancoDatasetResource::getUrl('index')),
        ];
    }

    public function search(): void
    {
        if (blank($this->keyword) || strlen($this->keyword) < 2) {
            $this->notify('warning', 'Masukkan minimal 2 karakter.');
            return;
        }

        $this->error = null;
        $this->searched = true;
        $this->results = null;
        $this->source = null;

        $this->searchLocal();

        if ($this->error === null && empty($this->results)) {
            $apiKey = config('services.opensanctions.api_key');
            if (filled($apiKey)) {
                $this->searchApi();
            }
        }
    }

    protected function searchLocal(): void
    {
        try {
            $keyword = trim($this->keyword);

            if (SancoEntity::count() === 0) {
                return;
            }

            $results = $this->doSearch($keyword);
            $this->results = $this->mergeDuplicates($results->all());

            if (empty($this->results)) {
                $results = $this->doFuzzySearch($keyword);
                $this->results = $this->mergeDuplicates($results->all());
                if (!empty($this->results)) {
                    $this->source = 'lokal (fuzzy)';
                }
            } else {
                $this->source = 'lokal';
            }

            if (empty($this->results)) {
                $this->notify('warning', 'Nama tidak ditemukan. Coba gunakan nama lengkap.');
            }

        } catch (\Exception $e) {
            $this->error = null;
        }
    }

    protected function mergeDuplicates(array $results): array
    {
        $grouped = [];

        foreach ($results as $row) {
            $id = $row['id'];
            if (!isset($grouped[$id])) {
                $grouped[$id] = $row;
            } else {
                $existing = $grouped[$id];
                // Merge datasets
                $existingTags = collect(explode(', ', $existing['datasets']))->filter()->unique();
                $newTags = collect(explode(', ', $row['datasets']))->filter()->unique();
                $grouped[$id]['datasets'] = $existingTags->merge($newTags)->unique()->sort()->implode(', ');

                // Keep the longest/richest name
                if (strlen($row['caption']) > strlen($existing['caption'])) {
                    $grouped[$id]['caption'] = $row['caption'];
                }
                // Merge aliases
                if (!empty($row['aliases'])) {
                    $grouped[$id]['aliases'] = trim(($existing['aliases'] ?? '') . '; ' . $row['aliases'], '; ');
                }
                if (!empty($row['weak_aliases'])) {
                    $grouped[$id]['weak_aliases'] = trim(($existing['weak_aliases'] ?? '') . '; ' . $row['weak_aliases'], '; ');
                }
                // Keep the most complete data
                foreach (['birth_date', 'birth_place', 'gender', 'nationality', 'country'] as $f) {
                    if (($existing[$f] ?? '-') === '-' && ($row[$f] ?? '-') !== '-') {
                        $grouped[$id][$f] = $row[$f];
                    }
                }
            }
        }

        return array_values($grouped);
    }

    protected function doFuzzySearch(string $keyword): \Illuminate\Support\Collection
    {
        if (strlen($keyword) <= 3) {
            return collect();
        }

        $prefix = substr($keyword, 0, 3);
        $keyTrigrams = $this->getTrigrams($keyword);

        $candidates = SancoEntity::query()
            ->where('name', 'like', "%{$prefix}%")
            ->limit(200)
            ->get();

        $scored = $candidates->map(function ($entity) use ($keyword, $keyTrigrams) {
            $name = strtolower($entity->name);
            $words = array_values(array_filter(explode(' ', $name), fn($w) => strlen($w) >= 2));

            $bestWordScore = 0;
            $totalWordScore = 0;
            $matchedWords = 0;

            foreach ($words as $word) {
                $word = trim($word);
                $ws = $this->wordSimilarity($keyword, $word, $keyTrigrams);
                $totalWordScore += $ws;
                $bestWordScore = max($bestWordScore, $ws);
                if ($ws > 0.3) $matchedWords++;
            }

            // Full name similarity
            $fullNameScore = $this->wordSimilarity($keyword, $name, $keyTrigrams);

            // Composite: best matching word (40%) + full name (30%) + avg word match (20%) + multi-word bonus (10%)
            $avgWord = count($words) > 0 ? $totalWordScore / count($words) : 0;
            $multiBonus = min($matchedWords / max(count($words), 1), 1.0);
            $score = ($bestWordScore * 0.40) + ($fullNameScore * 0.30) + ($avgWord * 0.20) + ($multiBonus * 0.10);

            // Dataset boost
            $ds = $entity->dataset_name;
            if (str_contains($ds, 'pep')) $score += 0.04;
            if (str_contains($ds, 'sanction') || str_contains($ds, 'ofac') || str_contains($ds, 'fsf')) $score += 0.02;

            return ['entity' => $entity, 'score' => $score];
        })
        ->filter(fn($item) => $item['score'] >= 0.50)
        ->sortByDesc('score')
        ->take(10)
        ->map(fn($item) => $this->formatResult($item['entity']));

        return $scored;
    }

    protected function wordSimilarity(string $keyword, string $word, array $keyTrigrams): float
    {
        $maxLen = max(strlen($keyword), strlen($word));
        if ($maxLen === 0) return 0;

        $lev = levenshtein($keyword, $word);
        $levScore = 1 - ($lev / $maxLen);

        $wordTrigrams = $this->getTrigrams($word);
        $overlap = count(array_intersect($keyTrigrams, $wordTrigrams));
        $total = max(count($keyTrigrams), count($wordTrigrams), 1);
        $triScore = $overlap / $total;

        // Prefix bonus: if word starts with keyword prefix, boost
        $prefixBonus = 0;
        if (str_starts_with($word, $keyword)) {
            $prefixBonus = 0.15;
        } elseif (strlen($keyword) >= 4 && str_starts_with($word, substr($keyword, 0, 4))) {
            $prefixBonus = 0.10;
        } elseif (strlen($keyword) >= 3 && str_starts_with($word, substr($keyword, 0, 3))) {
            $prefixBonus = 0.05;
        }

        return ($levScore * 0.3) + ($triScore * 0.7) + $prefixBonus;
    }

    protected function getTrigrams(string $str): array
    {
        $str = strtolower(preg_replace('/[^a-z]/', '', $str));
        $len = strlen($str);
        $trigrams = [];

        for ($i = 0; $i <= $len - 3; $i++) {
            $trigrams[] = substr($str, $i, 3);
        }

        return array_unique($trigrams);
    }

    protected function doSearch(string $keyword): \Illuminate\Support\Collection
    {
        $results = SancoEntity::query()
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('aliases', 'like', "%{$keyword}%")
                  ->orWhere('weak_aliases', 'like', "%{$keyword}%");

                foreach (explode(' ', $keyword) as $word) {
                    if (strlen($word) >= 2) {
                        $q->orWhere('name', 'like', "%{$word}%")
                          ->orWhere('aliases', 'like', "%{$word}%")
                          ->orWhere('weak_aliases', 'like', "%{$word}%");
                    }
                }

                if (strlen($keyword) > 3) {
                    $escaped = addcslashes($keyword, '+-><()~*\"@');
                    $q->orWhereRaw(
                        "MATCH(name, aliases) AGAINST(? IN BOOLEAN MODE)",
                        ["+{$escaped}*"]
                    );
                }
            })
            ->limit(60)
            ->get()
            ->map(fn($item) => $this->formatResult($item));

        return $results;
    }

    protected function formatResult(SancoEntity $item): array
    {
        $dataset = \App\Models\SancoDataset::where('name', $item->dataset_name)->first();

        return [
            'id' => $item->entity_id,
            'caption' => $item->name,
            'schema' => $item->schema ?? '-',
            'datasets' => $dataset?->title ?? $item->dataset_name,
            'dataset_name' => $item->dataset_name,
            'country' => $item->countries ?? '-',
            'birth_date' => $item->birth_date ?? '-',
            'aliases' => $item->aliases,
            'weak_aliases' => $item->weak_aliases,
            'birth_place' => $item->birth_place,
            'gender' => $item->gender,
            'nationality' => $item->nationality,
            'position' => $item->position,
            'notes' => $item->notes,
            'addresses' => $item->addresses,
            'identifiers' => $item->identifiers,
            'emails' => $item->emails,
            'first_seen' => $item->first_seen,
            'last_seen' => $item->last_seen,
            'last_change' => $item->last_change,
            'opensanctions_url' => "https://www.opensanctions.org/entities/{$item->entity_id}",
            'detail_url' => route('sanco.entity.show', $item->entity_id),
        ];
    }

    protected function searchApi(): void
    {
        $apiKey = config('services.opensanctions.api_key');

        if (blank($apiKey)) {
            return;
        }

        try {
            $response = Http::timeout(30)
                ->retry(2, 1000)
                ->withHeader('Authorization', 'ApiKey ' . $apiKey)
                ->get('https://api.opensanctions.org/search/sanctions', [
                    'q' => $this->keyword,
                    'limit' => 50,
                ]);

            if ($response->status() === 401 || $response->status() === 403) {
                return;
            }

            if (!$response->successful()) {
                return;
            }

            $data = $response->json();

            $apiResults = collect($data['results'] ?? [])
                ->map(function ($item) {
                    $props = $item['properties'] ?? [];
                    return [
                        'id' => $item['id'] ?? '-',
                        'caption' => $item['caption'] ?? '-',
                        'schema' => $item['schema'] ?? '-',
                        'datasets' => implode(', ', $item['datasets'] ?? []),
                        'country' => implode(', ', $props['country'] ?? $props['nationality'] ?? []),
                        'birth_date' => implode(', ', $props['birthDate'] ?? []),
                    ];
                })
                ->values()
                ->all();

            if (!empty($apiResults)) {
                $this->results = array_merge($this->results ?? [], $apiResults);
                $this->source = 'lokal + API';
            }

        } catch (\Exception) {
        }
    }
}
