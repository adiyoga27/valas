<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuyTransaction extends Model
{
    protected $fillable = [
        'transaction_code','user_id','customer_name','total_amount', 'notes'
    ];

   public function items()
{
    return $this->hasMany(BuyTransactionItem::class);
}

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
