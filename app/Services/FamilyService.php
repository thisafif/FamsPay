<?php

namespace App\Services;

use App\Models\Family;
use App\Models\User;
use App\Repositories\FamilyRepository;
use Illuminate\Support\Str;

final class FamilyService
{
    public function __construct(
        private readonly FamilyRepository $families,
    ) {}

    public function getFamily(User $user): Family
    {
        if (!$user->family_id) {
            throw new \DomainException('You do not belong to any family.');
        }

        $family = $this->families->findById((string) $user->family_id);

        if (!$family) {
            throw new \DomainException('Family not found.');
        }

        return $family;
    }

    public function createFamily(User $user, string $name): Family
    {
        if ($user->family_id) {
            throw new \DomainException('User already belongs to a family.');
        }

        $joinCode = $this->generateUniqueJoinCode();

        $family = $this->families->create([
            'family_name' => $name,
            'join_code'   => $joinCode,
            'created_by'  => (string) $user->getKey(),
        ]);

        $user->forceFill([
            'family_id' => (string) $family->getKey(),
            'role' => 'admin',
        ])->save();

        return $family;
    }

    public function joinFamily(User $user, string $joinCode): Family
    {
        if ($user->family_id) {
            throw new \DomainException('User already belongs to a family.');
        }

        $joinCode = strtoupper(trim($joinCode));
        $family = $this->families->findByJoinCode($joinCode);

        if (!$family) {
            throw new \InvalidArgumentException('Invalid join code.');
        }

        $user->forceFill([
            'family_id' => (string) $family->getKey(),
            'role' => 'member',
        ])->save();

        return $family;
    }

    public function updateFamilyName(User $admin, string $familyName): Family
    {
        if (!$admin->family_id) {
            throw new \DomainException('You do not belong to any family.');
        }

        $family = $this->families->findById((string) $admin->family_id);

        if (!$family) {
            throw new \DomainException('Family not found.');
        }

        $family->forceFill(['family_name' => $familyName])->save();

        return $family->fresh();
    }

    public function getFamilyMembers(User $user): \Illuminate\Database\Eloquent\Collection
    {
        if (!$user->family_id) {
            throw new \DomainException('You do not belong to any family.');
        }

        return $this->families->getMembersByFamilyId((string) $user->family_id);
    }

    public function removeMember(User $admin, string $targetUserId): void
    {
        if (!$admin->family_id) {
            throw new \DomainException('You do not belong to any family.');
        }

        $target = User::find($targetUserId);

        if (!$target) {
            throw new \InvalidArgumentException('User not found.');
        }

        // Target harus anggota family yang sama
        if ((string) $target->family_id !== (string) $admin->family_id) {
            throw new \DomainException('User is not a member of your family.');
        }

        // Admin tidak bisa menghapus dirinya sendiri
        if ((string) $target->getKey() === (string) $admin->getKey()) {
            throw new \DomainException('You cannot remove yourself from the family.');
        }

        // Cegah menghapus admin terakhir
        if ($target->role === 'admin') {
            $adminCount = $this->families->countAdminsByFamilyId((string) $admin->family_id);
            if ($adminCount <= 1) {
                throw new \DomainException('Cannot remove the last admin of the family.');
            }
        }

        // Lepas user dari family, reset role ke member
        $target->forceFill([
            'family_id' => null,
            'role'      => 'member',
        ])->save();
    }

    public function updateMemberRole(User $admin, string $targetUserId, string $newRole): User
    {
        // Admin harus punya family
        if (!$admin->family_id) {
            throw new \DomainException('You do not belong to any family.');
        }

        // Cari target user
        $target = User::find($targetUserId);

        if (!$target) {
            throw new \InvalidArgumentException('User not found.');
        }

        // Target harus anggota family yang sama
        if ((string) $target->family_id !== (string) $admin->family_id) {
            throw new \DomainException('User is not a member of your family.');
        }

        // Admin tidak bisa mengubah role dirinya sendiri
        if ((string) $target->getKey() === (string) $admin->getKey()) {
            throw new \DomainException('You cannot change your own role.');
        }

        // Cegah downgrade admin terakhir ke member
        if ($target->role === 'admin' && $newRole === 'member') {
            $adminCount = $this->families->countAdminsByFamilyId((string) $admin->family_id);
            if ($adminCount <= 1) {
                throw new \DomainException('Cannot demote the last admin of the family.');
            }
        }

        $target->forceFill(['role' => $newRole])->save();

        return $target->fresh();
    }

    private function generateUniqueJoinCode(): string
    {
        // 8 char alnum uppercase, retry beberapa kali untuk menghindari collision
        for ($i = 0; $i < 10; $i++) {
            $code = Str::upper(Str::random(8));
            if (!$this->families->existsJoinCode($code)) {
                return $code;
            }
        }

        throw new \RuntimeException('Failed generating unique join code.');
    }
}

