<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

final class UserMonthlyLimit extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'user_monthly_limits';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'family_id',
        'period_month',           // format: 'YYYY-MM', contoh: '2026-05'
        'monthly_limit_base',     // integer — limit dasar yang ditetapkan admin
        'remaining_limit',        // integer — cached field, sisa limit periode ini
        'total_income_in_period', // integer — cached field, total income bulan ini
        'total_expense_in_period',// integer — cached field, total expense bulan ini
    ];

    protected $casts = [
        'monthly_limit_base'      => 'integer',
        'remaining_limit'         => 'integer',
        'total_income_in_period'  => 'integer',
        'total_expense_in_period' => 'integer',
    ];

    /**
     * Default values saat record baru dibuat (awal periode)
     */
    protected $attributes = [
        'remaining_limit'         => 0,
        'total_income_in_period'  => 0,
        'total_expense_in_period' => 0,
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

    // ── Scope helpers ────────────────────────────────────────

    /**
     * Ambil record limit untuk user pada periode tertentu
     */
    public function scopeForPeriod($query, string $userId, string $period)
    {
        return $query->where('user_id', $userId)
                     ->where('period_month', $period);
    }
}
