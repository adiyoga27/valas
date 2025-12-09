<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = ['code','name','flag','buy_rate','sell_rate','is_active'];

    public function getDisplayNameAttribute()
    {
        return "{$this->code} - {$this->name}";
    }
}
