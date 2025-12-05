<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model
{
    protected $fillable = [
        'currency_id','buy_rate','sell_rate',
        'effective_date','status','created_by'
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
