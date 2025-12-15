<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = ['code','name','country_code','flag','buy_rate','sell_rate','is_active'];

    public function getDisplayNameAttribute()
    {
        return "{$this->country_code} - {$this->name}";
    }
}
