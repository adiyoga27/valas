<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = ['code','name','country_code','flag','buy_rate','sell_rate','is_active'];

    use \Spatie\Activitylog\Traits\LogsActivity;

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn ($event) => match ($event) {
                'created' => "Membuat Mata Uang ({$this->code})",
                'updated' => "Mengubah Mata Uang ({$this->code})",
                'deleted' => "Menghapus Mata Uang ({$this->code})",
                default => "Mata Uang ({$this->code})",
            })
            ->useLogName('currency');
    }

    public function tapActivity(\Spatie\Activitylog\Models\Activity $activity, string $eventName)
    {
        $activity->properties = $activity->properties->merge([
            'ip' => request()->ip(),
        ]);
    }

    public function getDisplayNameAttribute()
    {
        return "{$this->country_code} - {$this->name}";
    }
}
