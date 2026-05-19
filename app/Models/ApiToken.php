<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

final class ApiToken extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'api_tokens';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'device_name',
        'token_hash',
        'abilities',
        'expires_at',
        'last_used_at',
    ];

    protected $casts = [
        'abilities' => 'array',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];
}

