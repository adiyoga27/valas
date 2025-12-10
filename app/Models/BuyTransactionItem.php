<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuyTransactionItem extends Model
{
    protected $fillable = [
        'buy_transaction_id','currency_id','currency_code',
        'currency_name','currency_flag','nominal_foreign',
        'buy_rate','qty','total'
    ];

    public function transaction()
    {
        return $this->belongsTo(BuyTransaction::class, 'buy_transaction_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
