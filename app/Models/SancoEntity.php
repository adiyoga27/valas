<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SancoEntity extends Model
{
    protected $fillable = [
        'entity_id', 'dataset_name', 'dataset_title', 'schema', 'name',
        'aliases', 'weak_aliases', 'countries', 'birth_date',
        'addresses', 'identifiers', 'emails',
        'birth_place', 'gender', 'nationality', 'position', 'notes', 'properties',
        'first_seen', 'last_seen', 'last_change',
    ];

    protected $casts = [
        'properties' => 'array',
    ];
}
