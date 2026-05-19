<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

final class Family extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'families';

    public $timestamps = true;

    /**
     * Field sesuai Project Context section 10.3:
     * - family_name (bukan 'name')
     * - created_by  (bukan 'created_by_user_id')
     */
    protected $fillable = [
        'family_name',
        'join_code',
        'created_by', // user_id dari admin yang membuat family
    ];

    // ── Relasi ──────────────────────────────────────────────

    /**
     * Anggota family dibaca dari users.family_id
     * (tidak di-embed, sesuai section 10.1)
     */
    public function members()
    {
        return $this->hasMany(User::class, 'family_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'family_id');
    }

    public function goals()
    {
        return $this->hasMany(Goal::class, 'family_id');
    }
}
