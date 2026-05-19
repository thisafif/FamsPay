<?php

namespace App\Services;

use App\Models\Goal;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\GoalRepository;
use App\Repositories\TransactionRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

final class GoalService
{
    public function __construct(
        private readonly GoalRepository $goals,
        private readonly TransactionRepository $transactions,
        private readonly WalletService $wallet,
        private readonly MonthlyLimitService $monthlyLimit,
    ) {}

    // ── Create goal ──────────────────────────────────────────

    /**
     * Buat goal pribadi untuk user.
     * Goal bersifat personal — hanya milik user yang membuat.
     *
     * @throws \DomainException
     */
    public function create(User $user, string $title, int $targetAmount): Goal
    {
        if ($targetAmount <= 0) {
            throw new \DomainException('Target amount must be greater than 0.');
        }

        return $this->goals->create([
            'user_id'        => (string) $user->getKey(),
            'family_id'      => $user->family_id ? (string) $user->family_id : null,
            'title'          => $title,
            'target_amount'  => $targetAmount,
            'current_amount' => 0,
            'status'         => 'active',
        ]);
    }

    // ── List goals ───────────────────────────────────────────

    /**
     * Ambil semua goal milik user yang sedang login.
     * Default hanya tampilkan active dan completed — archived dikecualikan.
     * Jika include_archived=true, tampilkan semua status.
     */
    public function listForUser(User $user, bool $includeArchived = false): Collection
    {
        $query = Goal::where('user_id', (string) $user->getKey());

        if (!$includeArchived) {
            $query->where('status', '!=', 'archived');
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    // ── Show goal ────────────────────────────────────────────

    /**
     * Ambil detail goal berdasarkan ID.
     * Hanya pemilik yang bisa melihat — goals bersifat privat.
     *
     * @throws \InvalidArgumentException jika goal tidak ditemukan
     * @throws \DomainException jika bukan pemilik
     */
    public function show(User $user, string $goalId): Goal
    {
        $goal = $this->goals->findById($goalId);

        if (!$goal) {
            throw new \InvalidArgumentException('Goal not found.');
        }

        if ((string) $goal->user_id !== (string) $user->getKey()) {
            throw new \DomainException('You do not have access to this goal.');
        }

        return $goal;
    }

    // ── Archive ──────────────────────────────────────────────
    /**
     * Archive goal — ubah status menjadi 'archived'.
     * Data historis (transaksi goal-linked) tetap tersimpan, tidak dihapus.
     *
     * @throws \InvalidArgumentException jika goal tidak ditemukan
     * @throws \DomainException jika bukan pemilik goal
     */
    public function archive(User $user, string $goalId): Goal
    {
        $goal = $this->goals->findById($goalId);

        if (!$goal) {
            throw new \InvalidArgumentException('Goal not found.');
        }

        if ((string) $goal->user_id !== (string) $user->getKey()) {
            throw new \DomainException('You do not own this goal.');
        }

        return $this->goals->update($goal, ['status' => 'archived']);
    }

    // ── Withdraw ─────────────────────────────────────────────

    /**
     * Withdraw dana dari goal — buat system income transaction.
     * 1. Validasi ownership, amount > 0, amount <= current_amount
     * 2. Buat system income transaction (category=Goal Withdrawal, is_system=true)
     * 3. Tambah wallet balance
     * 4. Tambah remaining limit
     * 5. Kurangi current_amount goal
     *
     * @throws \InvalidArgumentException jika goal tidak ditemukan
     * @throws \DomainException jika tidak boleh withdraw
     *
     * @return array{
     *   goal: Goal,
     *   transaction: \App\Models\Transaction,
     *   wallet_balance: int,
     * }
     */
    public function withdraw(User $user, string $goalId, int $amount): array
    {
        $goal = $this->goals->findById($goalId);

        if (!$goal) {
            throw new \InvalidArgumentException('Goal not found.');
        }

        // Validasi ownership
        if ((string) $goal->user_id !== (string) $user->getKey()) {
            throw new \DomainException('You do not own this goal.');
        }

        // Validasi amount > 0
        if ($amount <= 0) {
            throw new \DomainException('Amount must be greater than 0.');
        }

        // Validasi amount <= current_amount
        if ($amount > (int) $goal->current_amount) {
            throw new \DomainException('Withdrawal amount cannot exceed current goal amount.');
        }

        $txnDate = now();

        // 1. Buat system income transaction
        $transaction = $this->transactions->create([
            'user_id'       => (string) $user->getKey(),
            'family_id'     => $user->family_id ? (string) $user->family_id : null,
            'type'          => 'income',
            'amount'        => $amount,
            'txn_date'      => $txnDate,
            'category_name' => 'Goal Withdrawal',
            'note'          => "Withdrawal from goal: {$goal->title}",
            'goal_id'       => (string) $goal->getKey(),
            'is_system'     => true,
            'is_deleted'    => false,
        ]);

        // 2. Tambah wallet balance
        $walletResult = $this->wallet->applyIncome($user, $amount);
        $user->refresh();

        // 3. Tambah remaining limit
        $this->monthlyLimit->applyIncome($user, $amount, $txnDate);

        // 4. Kurangi current_amount goal
        $newCurrentAmount = max(0, (int) $goal->current_amount - $amount);
        $updatedGoal = $this->goals->update($goal, [
            'current_amount' => $newCurrentAmount,
        ]);

        return [
            'goal'           => $updatedGoal,
            'transaction'    => $transaction,
            'wallet_balance' => $walletResult['balance'],
        ];
    }

    // ── Preview allocate ─────────────────────────────────────

    /**
     * Preview efek alokasi dana ke goal tanpa menyimpan ke DB.
     * Hitung projected wallet balance dan remaining limit.
     *
     * @throws \InvalidArgumentException jika goal tidak ditemukan
     * @throws \DomainException jika goal tidak boleh dialokasikan
     *
     * @return array{
     *   projected_wallet_balance: int,
     *   projected_remaining_limit: int|null,
     *   warnings: string[],
     *   requires_confirmation: bool,
     * }
     */
    public function previewAllocate(User $user, string $goalId, int $amount): array
    {
        $goal = $this->goals->findById($goalId);

        if (!$goal) {
            throw new \InvalidArgumentException('Goal not found.');
        }

        $this->assertAllocatable($user, $goal, $amount);

        $warnings = [];
        $txnDate  = now();

        // Projected wallet
        $walletProjection = $this->wallet->projectExpense($user, $amount);
        if ($walletProjection['warning']) {
            $warnings[] = $walletProjection['warning'];
        }

        // Projected remaining limit
        $limitProjection    = $this->monthlyLimit->projectExpense($user, $amount, $txnDate);
        $projectedRemaining = $limitProjection['projected_remaining'];
        if ($limitProjection['warning']) {
            $warnings[] = $limitProjection['warning'];
        }

        return [
            'projected_wallet_balance'  => $walletProjection['projected_balance'],
            'projected_remaining_limit' => $projectedRemaining,
            'warnings'                  => $warnings,
            'requires_confirmation'     => count($warnings) > 0,
        ];
    }

    // ── Allocate ─────────────────────────────────────────────

    /**
     * Alokasikan dana ke goal:
     * 1. Buat system expense transaction (category=Savings, is_system=true, goal_id set)
     * 2. Kurangi wallet balance
     * 3. Kurangi remaining limit
     * 4. Tambah current_amount goal
     * 5. Auto-complete goal jika target tercapai
     *
     * @throws \InvalidArgumentException jika goal tidak ditemukan
     * @throws \DomainException jika tidak boleh dialokasikan atau butuh konfirmasi
     *
     * @return array{
     *   goal: Goal,
     *   transaction: Transaction,
     *   wallet_balance: int,
     *   warnings: string[],
     * }
     */
    public function allocate(User $user, string $goalId, int $amount, bool $confirm = false): array
    {
        $goal = $this->goals->findById($goalId);

        if (!$goal) {
            throw new \InvalidArgumentException('Goal not found.');
        }

        $this->assertAllocatable($user, $goal, $amount);

        // Cek warnings
        $preview = $this->previewAllocate($user, $goalId, $amount);

        if ($preview['requires_confirmation'] && !$confirm) {
            throw new \DomainException(implode(' ', $preview['warnings']));
        }

        $txnDate = now();

        // 1. Buat system expense transaction
        $transaction = $this->transactions->create([
            'user_id'       => (string) $user->getKey(),
            'family_id'     => $user->family_id ? (string) $user->family_id : null,
            'type'          => 'expense',
            'amount'        => $amount,
            'txn_date'      => $txnDate,
            'category_name' => 'Savings',
            'note'          => "Allocation to goal: {$goal->title}",
            'goal_id'       => (string) $goal->getKey(),
            'is_system'     => true,
            'is_deleted'    => false,
        ]);

        // 2. Kurangi wallet balance
        $walletResult = $this->wallet->applyExpense($user, $amount);
        $user->refresh();

        // 3. Kurangi remaining limit
        $this->monthlyLimit->applyExpense($user, $amount, $txnDate);

        // 4. Tambah current_amount goal
        $newCurrentAmount = (int) $goal->current_amount + $amount;
        $updatedGoal = $this->goals->update($goal, [
            'current_amount' => $newCurrentAmount,
        ]);

        // 5. Auto-complete jika target tercapai
        $updatedGoal->checkAndCompleteIfReached();
        $updatedGoal->refresh();

        return [
            'goal'           => $updatedGoal,
            'transaction'    => $transaction,
            'wallet_balance' => $walletResult['balance'],
            'warnings'       => $preview['warnings'],
        ];
    }

    // ── Private helpers ──────────────────────────────────────

    /**
     * Validasi goal bisa dialokasikan oleh user.
     *
     * @throws \DomainException
     */
    private function assertAllocatable(User $user, Goal $goal, int $amount): void
    {
        // Hanya pemilik goal yang boleh alokasi
        if ((string) $goal->user_id !== (string) $user->getKey()) {
            throw new \DomainException('You do not own this goal.');
        }

        // Goal harus active
        if ($goal->status !== 'active') {
            throw new \DomainException('Only active goals can receive allocations.');
        }

        // Amount harus > 0
        if ($amount <= 0) {
            throw new \DomainException('Amount must be greater than 0.');
        }
    }
}
