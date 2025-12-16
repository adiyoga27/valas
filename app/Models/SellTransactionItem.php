<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellTransactionItem extends Model
{
    protected $fillable = [
        'sell_transaction_id',
        'currency_id',
        'currency_code',
        'currency_name',
        'currency_flag',
        'nominal_foreign',
        'sell_rate',
        'qty',
        'total',
    ];

    public function transaction()
    {
        return $this->belongsTo(SellTransaction::class, 'sell_transaction_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
