<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use MongoDB\Laravel\Auth\User as Authenticatable; // Auth user untuk MongoDB

class User extends Authenticatable // Gunakan Authenticatable
{
    use Notifiable; 

    protected $connection = 'mongodb';
    protected $collection = 'users';

    public $timestamps = true;

    protected $fillable = [
        'full_name', 'email', 'password_hash', 'avatar_url', 
        'family_id', 'role', 'wallet_balance'
    ];

    // Penting: Sembunyikan password agar tidak terekspos di API Response
    protected $hidden = [
        'password_hash',
    ];

    public function family() {
        return $this->belongsTo(Family::class, 'family_id');
    }
}