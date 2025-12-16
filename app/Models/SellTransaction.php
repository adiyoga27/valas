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
        'total_amount',
        'notes',
        'grand_total',
        'additional_amounts',
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
