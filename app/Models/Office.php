<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'logo'
    ];

    use \Spatie\Activitylog\Traits\LogsActivity;

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn ($event) => match ($event) {
                'created' => "Membuat Data Kantor",
                'updated' => "Mengubah Data Kantor",
                'deleted' => "Menghapus Data Kantor",
                default => "Data Kantor",
            })
            ->useLogName('office');
    }

    public function tapActivity(\Spatie\Activitylog\Models\Activity $activity, string $eventName)
    {
        $activity->properties = $activity->properties->merge([
            'ip' => request()->ip(),
        ]);
    }
}
