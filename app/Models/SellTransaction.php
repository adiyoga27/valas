<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellTransaction extends Model
{
    protected $fillable = [
        'transaction_code',
        'user_id',
        'customer_name',
        'total_amount',
        'notes',
        'grand_total',
        'additional_amounts',
    ];

    public $casts = [
        'additional_amounts' => 'array',
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
