<?php

namespace App\Repositories;

use App\Models\Goal;
use Illuminate\Database\Eloquent\Collection;

final class GoalRepository
{
    /**
     * Buat goal baru.
     */
    public function create(array $data): Goal
    {
        return Goal::create($data);
    }

    /**
     * Ambil semua goal milik user tertentu, diurutkan terbaru dulu.
     */
    public function listByUserId(string $userId): Collection
    {
        return Goal::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Cari goal berdasarkan ID.
     */
    public function findById(string $id): ?Goal
    {
        return Goal::find($id);
    }

    /**
     * Update field goal yang sudah ada.
     */
    public function update(Goal $goal, array $data): Goal
    {
        $goal->forceFill($data)->save();

        return $goal->fresh();
    }
}
