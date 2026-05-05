<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

final class Transaction extends Model
{
    use SoftDeletes;

    protected $connection = 'mongodb';
    protected $collection = 'transactions';

    public $timestamps = true;

    /**
     * MongoDB soft delete menggunakan is_deleted + deleted_at
     * sesuai skema dokumen Project Context section 10.4
     */
    protected $fillable = [
        'user_id',
        'family_id',
        'type',          // 'income' | 'expense'
        'amount',        // integer rupiah, bukan float
        'txn_date',
        'category_name',
        'note',
        'goal_id',       // nullable — hanya diisi jika goal-linked
        'is_system',     // true jika dibuat oleh sistem (goal allocation/withdraw)
        'is_deleted',
        'deleted_at',
        'deleted_by',    // nullable — user_id yang menghapus
    ];

    protected $casts = [
        'amount'     => 'integer',
        'txn_date'   => 'datetime',
        'is_system'  => 'boolean',
        'is_deleted' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Default values saat dokumen baru dibuat
     */
    protected $attributes = [
        'is_system'  => false,
        'is_deleted' => false,
        'goal_id'    => null,
        'deleted_at' => null,
        'deleted_by' => null,
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

    public function goal()
    {
        return $this->belongsTo(Goal::class, 'goal_id');
    }

    // ── Scope helpers ────────────────────────────────────────

    /**
     * Hanya transaksi yang belum dihapus (soft delete manual)
     */
    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

    /**
     * Filter berdasarkan periode bulan dari txn_date (format: YYYY-MM)
     */
    public function scopeInPeriod($query, string $period)
    {
        $start = \Carbon\Carbon::createFromFormat('Y-m', $period)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        return $query->whereBetween('txn_date', [$start, $end]);
    }
}
