<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

final class Goal extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'goals';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'family_id',
        'title',
        'target_amount',   // integer rupiah
        'current_amount',  // integer rupiah — cached field, diupdate setiap alokasi/withdraw
        'status',          // 'active' | 'completed' | 'archived'
    ];

    protected $casts = [
        'target_amount'  => 'integer',
        'current_amount' => 'integer',
    ];

    /**
     * Default values saat goal baru dibuat
     */
    protected $attributes = [
        'current_amount' => 0,
        'status'         => 'active',
    ];

    // ── Relasi ──────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function family()
    {
        return $this->belongsTo(Family::class, 'family_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'goal_id');
    }

    // ── Scope helpers ────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // ── Business logic helpers ───────────────────────────────

    /**
     * Cek apakah goal sudah mencapai target.
     * Jika ya, status otomatis menjadi 'completed' (section 4.6)
     */
    public function checkAndCompleteIfReached(): void
    {
        if ($this->current_amount >= $this->target_amount) {
            $this->status = 'completed';
            $this->save();
        }
    }
}
