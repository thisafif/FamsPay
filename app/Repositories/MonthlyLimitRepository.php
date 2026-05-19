<?php

namespace App\Repositories;

use App\Models\UserMonthlyLimit;

final class MonthlyLimitRepository
{
    /**
     * Cari record limit berdasarkan user_id dan period_month.
     */
    public function findByUserAndPeriod(string $userId, string $period): ?UserMonthlyLimit
    {
        return UserMonthlyLimit::where('user_id', $userId)
            ->where('period_month', $period)
            ->first();
    }

    /**
     * Buat record limit baru.
     */
    public function create(array $data): UserMonthlyLimit
    {
        return UserMonthlyLimit::create($data);
    }

    /**
     * Update record limit yang sudah ada.
     */
    public function update(UserMonthlyLimit $limit, array $data): UserMonthlyLimit
    {
        $limit->forceFill($data)->save();

        return $limit->fresh();
    }
}
