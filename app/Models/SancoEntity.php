<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SancoEntity extends Model
{
    protected $fillable = [
        'entity_id',
        'dataset_name',
        'schema',
        'name',
        'aliases',
        'weak_aliases',
        'countries',
        'birth_date',
        'addresses',
        'identifiers',
        'emails',
        'first_seen',
        'last_seen',
        'last_change',
    ];
}
