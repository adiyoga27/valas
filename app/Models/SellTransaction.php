<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellTransaction extends Model
{
    protected $fillable = [
        'transaction_code','user_id','customer_name','total_amount'
    ];

    public function items()
    {
        return $this->hasMany(SellTransactionItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
