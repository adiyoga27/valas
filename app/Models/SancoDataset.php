<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SancoDataset extends Model
{
    protected $fillable = [
        'name',
        'title',
        'summary',
        'description',
        'url',
        'updated_at_source',
        'last_export',
        'entity_count',
        'thing_count',
        'version',
        'tags',
        'publisher_name',
        'publisher_url',
        'publisher_acronym',
        'publisher_country',
        'publisher_country_label',
        'publisher_official',
        'publisher_description',
        'coverage_start',
        'coverage_frequency',
        'resources',
    ];

    protected $casts = [
        'tags' => 'array',
        'resources' => 'array',
        'publisher_official' => 'boolean',
        'entity_count' => 'integer',
        'thing_count' => 'integer',
    ];

    use \Spatie\Activitylog\Traits\LogsActivity;

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logOnly(['name', 'title', 'entity_count'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn($event) => match ($event) {
                'created' => "Menambah Dataset Sanco",
                'updated' => "Mengubah Dataset Sanco",
                'deleted' => "Menghapus Dataset Sanco",
                default => "Dataset Sanco",
            })
            ->useLogName('sanco_dataset');
    }

    public function tapActivity(\Spatie\Activitylog\Models\Activity $activity, string $eventName)
    {
        $activity->properties = $activity->properties->merge([
            'ip' => request()->ip(),
        ]);
    }
}
