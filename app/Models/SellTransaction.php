<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SellTransaction extends Model
{
     use LogsActivity;

   
    protected $fillable = [
        'transaction_code',
        'user_id',
        'customer_name',
        'passport_number',
        'customer_address',
        'customer_country',
        'customer_birthdate',
        'total_amount',
        'notes',
        'grand_total',
        'additional_amounts',
        'created_at',
    ];

      public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('buy_transaction');
    }
    public $casts = [
        'additional_amounts' => 'array',
        'created_at' => 'datetime',
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
