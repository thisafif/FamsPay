<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\UserMonthlyLimit;
use App\Repositories\MonthlyLimitRepository;
use Carbon\Carbon;

final class MonthlyLimitService
{
    public function __construct(
        private readonly MonthlyLimitRepository $limits,
    ) {}

    // ── Admin: set limit ─────────────────────────────────────

    /**
     * Set atau update monthly limit untuk member tertentu.
     * Hanya admin yang boleh memanggil ini (dijaga di middleware & controller).
     *
     * @throws \DomainException
     * @throws \InvalidArgumentException
     */
    public function setMonthlyLimit(
        User $admin,
        string $targetUserId,
        int $limitAmount,
        ?string $periodMonth = null,
    ): UserMonthlyLimit {
        if (!$admin->family_id) {
            throw new \DomainException('You do not belong to any family.');
        }

        $target = User::find($targetUserId);
        if (!$target) {
            throw new \InvalidArgumentException('User not found.');
        }

        if ((string) $target->family_id !== (string) $admin->family_id) {
            throw new \DomainException('User is not a member of your family.');
        }

        $period = $periodMonth ?? now()->format('Y-m');

        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $period)) {
            throw new \InvalidArgumentException('period_month must be in YYYY-MM format.');
        }

        $existing = $this->limits->findByUserAndPeriod(
            userId: (string) $target->getKey(),
            period: $period,
        );

        if ($existing) {
            // Sesuaikan remaining_limit berdasarkan selisih perubahan limit
            $diff        = $limitAmount - $existing->monthly_limit_base;
            $newRemaining = $existing->remaining_limit + $diff;

            return $this->limits->update($existing, [
                'monthly_limit_base' => $limitAmount,
                'remaining_limit'    => $newRemaining,
            ]);
        }

        return $this->limits->create([
            'user_id'            => (string) $target->getKey(),
            'family_id'          => (string) $admin->family_id,
            'period_month'       => $period,
            'monthly_limit_base' => $limitAmount,
            'remaining_limit'    => $limitAmount,
        ]);
    }

    // ── Transaksi baru ───────────────────────────────────────

    /**
     * Terapkan efek expense ke remaining_limit periode txn_date.
     * Expense mengurangi remaining_limit — boleh negatif.
     * Jika record limit belum ada untuk periode tersebut, skip (no-op).
     *
     * @return array{limit: UserMonthlyLimit|null, warning: string|null}
     */
    public function applyExpense(User $user, int $amount, Carbon $txnDate): array
    {
        $period = $txnDate->format('Y-m');
        $record = $this->limits->findByUserAndPeriod((string) $user->getKey(), $period);

        if (!$record) {
            return ['limit' => null, 'warning' => null];
        }

        $newRemaining = $record->remaining_limit - $amount;

        $updated = $this->limits->update($record, [
            'remaining_limit'         => $newRemaining,
            'total_expense_in_period' => $record->total_expense_in_period + $amount,
        ]);

        return $this->result($updated);
    }

    /**
     * Terapkan efek income ke remaining_limit periode txn_date.
     * Income menambah remaining_limit.
     * Jika record limit belum ada untuk periode tersebut, skip (no-op).
     *
     * @return array{limit: UserMonthlyLimit|null, warning: string|null}
     */
    public function applyIncome(User $user, int $amount, Carbon $txnDate): array
    {
        $period = $txnDate->format('Y-m');
        $record = $this->limits->findByUserAndPeriod((string) $user->getKey(), $period);

        if (!$record) {
            return ['limit' => null, 'warning' => null];
        }

        $newRemaining = $record->remaining_limit + $amount;

        $updated = $this->limits->update($record, [
            'remaining_limit'        => $newRemaining,
            'total_income_in_period' => $record->total_income_in_period + $amount,
        ]);

        return $this->result($updated);
    }

    // ── Edit transaksi ───────────────────────────────────────

    /**
     * Handle edit transaksi — hanya memengaruhi periode bulan transaksi terkait.
     * Balikkan efek lama, terapkan efek baru.
     * Jika txn_date berubah, update kedua periode yang terdampak.
     *
     * @return array{limit: UserMonthlyLimit|null, warning: string|null}
     */
    public function applyEditDelta(
        User $user,
        Transaction $oldTransaction,
        string $newType,
        int $newAmount,
        Carbon $newTxnDate,
    ): array {
        $oldPeriod = Carbon::parse($oldTransaction->txn_date)->format('Y-m');
        $newPeriod = $newTxnDate->format('Y-m');

        // Balikkan efek di periode lama
        $this->reverseInPeriod($user, $oldTransaction->type, (int) $oldTransaction->amount, $oldPeriod);

        // Terapkan efek di periode baru
        $record = $this->limits->findByUserAndPeriod((string) $user->getKey(), $newPeriod);

        if (!$record) {
            return ['limit' => null, 'warning' => null];
        }

        if ($newType === 'expense') {
            $newRemaining = $record->remaining_limit - $newAmount;
            $updated = $this->limits->update($record, [
                'remaining_limit'         => $newRemaining,
                'total_expense_in_period' => $record->total_expense_in_period + $newAmount,
            ]);
        } else {
            $newRemaining = $record->remaining_limit + $newAmount;
            $updated = $this->limits->update($record, [
                'remaining_limit'        => $newRemaining,
                'total_income_in_period' => $record->total_income_in_period + $newAmount,
            ]);
        }

        return $this->result($updated);
    }

    // ── Soft delete transaksi ────────────────────────────────

    /**
     * Balikkan efek transaksi yang di-soft-delete.
     * Hanya memengaruhi periode bulan transaksi terkait (dari txn_date).
     *
     * @return array{limit: UserMonthlyLimit|null, warning: string|null}
     */
    public function reverseTransaction(User $user, Transaction $transaction): array
    {
        $period = Carbon::parse($transaction->txn_date)->format('Y-m');

        $record = $this->reverseInPeriod(
            user: $user,
            type: $transaction->type,
            amount: (int) $transaction->amount,
            period: $period,
        );

        if (!$record) {
            return ['limit' => null, 'warning' => null];
        }

        return $this->result($record);
    }

    // ── Projection (preview sebelum simpan) ──────────────────

    /**
     * Hitung projected remaining_limit setelah expense tanpa menyimpan ke DB.
     *
     * @return array{projected_remaining: int|null, warning: string|null}
     */
    public function projectExpense(User $user, int $amount, Carbon $txnDate): array
    {
        $period = $txnDate->format('Y-m');
        $record = $this->limits->findByUserAndPeriod((string) $user->getKey(), $period);

        if (!$record) {
            return ['projected_remaining' => null, 'warning' => null];
        }

        $projected = $record->remaining_limit - $amount;

        return [
            'projected_remaining' => $projected,
            'warning'             => $projected < 0
                ? 'This transaction will result in a negative remaining limit.'
                : null,
        ];
    }

    // ── Resolve active period ────────────────────────────────

    /**
     * Ambil record limit aktif (periode berjalan) untuk user tertentu.
     * Jika belum ada, return null.
     */
    public function getActivePeriodLimit(string $userId): ?UserMonthlyLimit
    {
        return $this->limits->findByUserAndPeriod(
            userId: $userId,
            period: now()->format('Y-m'),
        );
    }

    // ── Private helpers ──────────────────────────────────────

    /**
     * Balikkan efek transaksi di periode tertentu.
     * Income → kurangi remaining, Expense → tambah remaining.
     */
    private function reverseInPeriod(User $user, string $type, int $amount, string $period): ?UserMonthlyLimit
    {
        $record = $this->limits->findByUserAndPeriod((string) $user->getKey(), $period);

        if (!$record) {
            return null;
        }

        if ($type === 'income') {
            return $this->limits->update($record, [
                'remaining_limit'        => $record->remaining_limit - $amount,
                'total_income_in_period' => max(0, $record->total_income_in_period - $amount),
            ]);
        }

        // expense
        return $this->limits->update($record, [
            'remaining_limit'         => $record->remaining_limit + $amount,
            'total_expense_in_period' => max(0, $record->total_expense_in_period - $amount),
        ]);
    }

    /**
     * Buat array hasil standar dengan warning jika remaining_limit negatif.
     *
     * @return array{limit: UserMonthlyLimit, warning: string|null}
     */
    private function result(UserMonthlyLimit $limit): array
    {
        return [
            'limit'   => $limit,
            'warning' => $limit->remaining_limit < 0
                ? 'Remaining limit is negative.'
                : null,
        ];
    }
}
