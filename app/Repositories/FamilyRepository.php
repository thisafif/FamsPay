<?php

namespace App\Repositories;

use App\Models\Family;

final class FamilyRepository
{
    public function findByJoinCode(string $joinCode): ?Family
    {
        return Family::where('join_code', $joinCode)->first();
    }

    public function findById(string $id): ?Family
    {
        return Family::find($id);
    }

    public function getMembersByFamilyId(string $familyId): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\User::where('family_id', $familyId)
            ->orderBy('role', 'asc') // admin muncul duluan (a < m)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function existsJoinCode(string $joinCode): bool
    {
        return Family::where('join_code', $joinCode)->exists();
    }

    public function create(array $data): Family
    {
        return Family::create($data);
    }

    public function countAdminsByFamilyId(string $familyId): int
    {
        return \App\Models\User::where('family_id', $familyId)
            ->where('role', 'admin')
            ->count();
    }
}

