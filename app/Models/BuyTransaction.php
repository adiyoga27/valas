<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BuyTransaction extends Model
{
     use LogsActivity;

   
    protected $fillable = [
        'transaction_code',
        'user_id',
        'customer_name',
        'total_amount',
        'notes',
        'grand_total',
        'additional_amounts'
    ];

     public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn ($event) => match ($event) {
                'created' => 'Create Pembelian Valas',
                'updated' => 'Update Pembelian Valas',
                'deleted' => 'Delete Pembelian Valas',
                default => $event,
            })
            ->useLogName('buy_transaction');
    }
    
    public $casts = [
        'additional_amounts' => 'array',
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
