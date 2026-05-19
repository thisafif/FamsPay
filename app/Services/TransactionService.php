<?php

namespace App\Services;

use App\Enums\TransactionCategory;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\TransactionRepository;
use Carbon\Carbon;

final class TransactionService
{
    public function __construct(
        private readonly TransactionRepository $transactions,
        private readonly WalletService $wallet,
        private readonly MonthlyLimitService $monthlyLimit,
    ) {}

    // ── Show ─────────────────────────────────────────────────

    /**
     * Ambil detail transaksi berdasarkan ID.
     * - Member: hanya bisa lihat transaksi miliknya sendiri
     * - Admin: bisa lihat semua transaksi dalam family
     *
     * @throws \InvalidArgumentException jika transaksi tidak ditemukan
     * @throws \DomainException jika tidak punya akses
     */
    public function show(User $user, string $transactionId): Transaction
    {
        $transaction = $this->transactions->findActiveById($transactionId);

        if (!$transaction) {
            throw new \InvalidArgumentException('Transaction not found.');
        }

        // Admin bisa lihat semua transaksi dalam family
        if ($user->role === 'admin' && $user->family_id) {
            if ((string) $transaction->family_id === (string) $user->family_id) {
                return $transaction;
            }
        }

        // Member hanya bisa lihat transaksi miliknya
        if ((string) $transaction->user_id !== (string) $user->getKey()) {
            throw new \DomainException('You do not have access to this transaction.');
        }

        return $transaction;
    }

    // ── List ─────────────────────────────────────────────────

    /**
     * Ambil daftar transaksi.
     * - Member: hanya transaksi miliknya (scope by user_id)
     * - Admin: semua transaksi dalam family (scope by family_id)
     * Soft deleted tidak tampil. Default sort txn_date desc.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function list(
        User $user,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?string $type = null,
        ?string $category = null,
    ): \Illuminate\Database\Eloquent\Collection {
        // Admin scope by family_id, member scope by user_id
        if ($user->role === 'admin' && $user->family_id) {
            $scopeField = 'family_id';
            $scopeValue = (string) $user->family_id;
        } else {
            $scopeField = 'user_id';
            $scopeValue = (string) $user->getKey();
        }

        return $this->transactions->listActive([
            'scope_field' => $scopeField,
            'scope_value' => $scopeValue,
            'date_from'   => $dateFrom,
            'date_to'     => $dateTo,
            'type'        => $type,
            'category'    => $category,
        ]);
    }

    // ── Preview ──────────────────────────────────────────────
    /**
     * Hitung proyeksi wallet balance dan remaining limit
     * tanpa menyimpan transaksi ke database.
     *
     * @return array{
     *   projected_wallet_balance: int,
     *   projected_remaining_limit: int|null,
     *   category_name: string,
     *   warnings: string[],
     *   requires_confirmation: bool,
     * }
     */
    public function preview(
        User $user,
        string $type,
        int $amount,
        Carbon $txnDate,
        ?string $categoryName,
    ): array {
        $category = $this->resolveCategory($categoryName);
        $warnings = [];

        // Projected wallet balance
        if ($type === 'expense') {
            $walletProjection = $this->wallet->projectExpense($user, $amount);
        } else {
            $walletProjection = [
                'projected_balance' => (int) $user->wallet_balance + $amount,
                'warning'           => null,
            ];
        }

        if ($walletProjection['warning']) {
            $warnings[] = $walletProjection['warning'];
        }

        // Projected remaining limit (hanya untuk expense)
        $projectedRemaining = null;
        if ($type === 'expense') {
            $limitProjection = $this->monthlyLimit->projectExpense($user, $amount, $txnDate);
            $projectedRemaining = $limitProjection['projected_remaining'];

            if ($limitProjection['warning']) {
                $warnings[] = $limitProjection['warning'];
            }
        }

        return [
            'projected_wallet_balance'  => $walletProjection['projected_balance'],
            'projected_remaining_limit' => $projectedRemaining,
            'category_name'             => $category,
            'warnings'                  => $warnings,
            'requires_confirmation'     => count($warnings) > 0,
        ];
    }

    // ── Create ───────────────────────────────────────────────

    /**
     * Buat transaksi baru, update wallet balance dan remaining limit.
     * Jika ada warning dan confirm=false, lempar exception.
     *
     * @throws \DomainException jika ada warning dan user belum konfirmasi
     * @return array{transaction: Transaction, wallet_balance: int, warnings: string[]}
     */
    public function create(
        User $user,
        string $type,
        int $amount,
        Carbon $txnDate,
        ?string $categoryName,
        ?string $note,
        ?string $goalId,
        bool $confirm = false,
    ): array {
        $category = $this->resolveCategory($categoryName);

        // Cek warnings terlebih dahulu
        $preview = $this->preview($user, $type, $amount, $txnDate, $categoryName);

        // Jika ada warning dan belum dikonfirmasi, tolak
        if ($preview['requires_confirmation'] && !$confirm) {
            throw new \DomainException(
                implode(' ', $preview['warnings'])
            );
        }

        // Simpan transaksi
        $transaction = $this->transactions->create([
            'user_id'       => (string) $user->getKey(),
            'family_id'     => $user->family_id ? (string) $user->family_id : null,
            'type'          => $type,
            'amount'        => $amount,
            'txn_date'      => $txnDate,
            'category_name' => $category,
            'note'          => $note,
            'goal_id'       => $goalId,
            'is_system'     => false,
            'is_deleted'    => false,
        ]);

        // Update wallet balance
        if ($type === 'income') {
            $walletResult = $this->wallet->applyIncome($user, $amount);
            $this->monthlyLimit->applyIncome($user, $amount, $txnDate);
        } else {
            $walletResult = $this->wallet->applyExpense($user, $amount);
            $this->monthlyLimit->applyExpense($user, $amount, $txnDate);
        }

        return [
            'transaction'    => $transaction,
            'wallet_balance' => $walletResult['balance'],
            'warnings'       => $preview['warnings'],
        ];
    }

    // ── Delete (soft delete) ─────────────────────────────────

    /**
     * Soft delete transaksi — set is_deleted, deleted_at, deleted_by.
     * Reverse efek wallet dan monthly limit.
     * Goal-linked dan system transaction tidak bisa dihapus dari modul ini.
     *
     * @throws \InvalidArgumentException jika transaksi tidak ditemukan
     * @throws \DomainException jika tidak boleh dihapus
     *
     * @return array{wallet_balance: int}
     */
    public function delete(User $user, string $transactionId): array
    {
        $transaction = $this->transactions->findActiveById($transactionId);

        if (!$transaction) {
            throw new \InvalidArgumentException('Transaction not found.');
        }

        // Validasi ownership
        if ((string) $transaction->user_id !== (string) $user->getKey()) {
            throw new \DomainException('You do not own this transaction.');
        }

        // Goal-linked tidak bisa dihapus dari modul transaksi
        if ($transaction->goal_id) {
            throw new \DomainException('Goal-linked transactions cannot be deleted from the transaction module.');
        }

        // System transaction tidak bisa dihapus
        if ($transaction->is_system) {
            throw new \DomainException('System transactions cannot be deleted.');
        }

        // Reverse efek wallet
        $walletResult = $this->wallet->reverseTransaction($user, $transaction);

        // Refresh user setelah wallet update
        $user->refresh();

        // Reverse efek monthly limit
        $this->monthlyLimit->reverseTransaction($user, $transaction);

        // Soft delete — set field manual
        $this->transactions->update($transaction, [
            'is_deleted' => true,
            'deleted_at' => now(),
            'deleted_by' => (string) $user->getKey(),
        ]);

        return [
            'wallet_balance' => $walletResult['balance'],
        ];
    }

    // ── Edit ─────────────────────────────────────────────────

    /**
     * Preview efek edit transaksi — hitung proyeksi wallet dan remaining limit
     * setelah reverse lama + apply baru, tanpa menyimpan ke DB.
     *
     * @throws \InvalidArgumentException jika transaksi tidak ditemukan
     * @throws \DomainException jika transaksi tidak boleh diedit
     *
     * @return array{
     *   projected_wallet_balance: int,
     *   projected_remaining_limit: int|null,
     *   category_name: string,
     *   warnings: string[],
     *   requires_confirmation: bool,
     * }
     */
    public function previewEdit(
        User $user,
        string $transactionId,
        string $newType,
        int $newAmount,
        Carbon $newTxnDate,
        ?string $categoryName,
    ): array {
        $transaction = $this->transactions->findActiveById($transactionId);

        if (!$transaction) {
            throw new \InvalidArgumentException('Transaction not found.');
        }

        $this->assertEditable($user, $transaction);

        $category = $this->resolveCategory($categoryName);
        $warnings = [];

        // Simulasi wallet setelah reverse lama + apply baru
        $simulatedBalance = (int) $user->wallet_balance;

        // Reverse efek lama
        if ($transaction->type === 'income') {
            $simulatedBalance -= (int) $transaction->amount;
        } else {
            $simulatedBalance += (int) $transaction->amount;
        }

        // Apply efek baru
        if ($newType === 'income') {
            $simulatedBalance += $newAmount;
        } else {
            $simulatedBalance -= $newAmount;
        }

        if ($simulatedBalance < 0) {
            $warnings[] = 'This edit will result in a negative wallet balance.';
        }

        // Simulasi remaining limit (hanya untuk expense baru)
        $projectedRemaining = null;
        if ($newType === 'expense') {
            $oldPeriod = Carbon::parse($transaction->txn_date)->format('Y-m');
            $newPeriod = $newTxnDate->format('Y-m');

            $limitResult = $this->monthlyLimit->projectExpense($user, $newAmount, $newTxnDate);
            $projectedRemaining = $limitResult['projected_remaining'];

            // Jika periode sama, tambahkan kembali efek lama sebelum hitung proyeksi
            if ($oldPeriod === $newPeriod && $transaction->type === 'expense' && $projectedRemaining !== null) {
                $projectedRemaining += (int) $transaction->amount;
                $projectedRemaining -= $newAmount;
            }

            if ($projectedRemaining !== null && $projectedRemaining < 0) {
                $warnings[] = 'This edit will result in a negative remaining limit.';
            }
        }

        return [
            'projected_wallet_balance'  => $simulatedBalance,
            'projected_remaining_limit' => $projectedRemaining,
            'category_name'             => $category,
            'warnings'                  => $warnings,
            'requires_confirmation'     => count($warnings) > 0,
        ];
    }

    /**
     * Update transaksi — reverse efek lama, apply efek baru.
     * Goal-linked dan system transaction tidak bisa diedit.
     * Jika ada warning dan confirm=false, lempar exception.
     *
     * @throws \InvalidArgumentException jika transaksi tidak ditemukan
     * @throws \DomainException jika tidak boleh diedit atau ada warning tanpa konfirmasi
     *
     * @return array{transaction: Transaction, wallet_balance: int, warnings: string[]}
     */
    public function update(
        User $user,
        string $transactionId,
        string $newType,
        int $newAmount,
        Carbon $newTxnDate,
        ?string $categoryName,
        ?string $note,
        bool $confirm = false,
    ): array {
        $transaction = $this->transactions->findActiveById($transactionId);

        if (!$transaction) {
            throw new \InvalidArgumentException('Transaction not found.');
        }

        $this->assertEditable($user, $transaction);

        // Cek warnings
        $preview = $this->previewEdit($user, $transactionId, $newType, $newAmount, $newTxnDate, $categoryName);

        if ($preview['requires_confirmation'] && !$confirm) {
            throw new \DomainException(implode(' ', $preview['warnings']));
        }

        $category = $this->resolveCategory($categoryName);

        // Reverse efek lama pada wallet dan monthly limit
        $walletResult = $this->wallet->applyEditDelta($user, $transaction, $newType, $newAmount);
        $this->monthlyLimit->applyEditDelta($user, $transaction, $newType, $newAmount, $newTxnDate);

        // Refresh user setelah wallet update
        $user->refresh();

        // Update data transaksi
        $updated = $this->transactions->update($transaction, [
            'type'          => $newType,
            'amount'        => $newAmount,
            'txn_date'      => $newTxnDate,
            'category_name' => $category,
            'note'          => $note,
        ]);

        return [
            'transaction'    => $updated,
            'wallet_balance' => $walletResult['balance'],
            'warnings'       => $preview['warnings'],
        ];
    }

    // ── Private helpers ──────────────────────────────────────

    /**
     * Pastikan transaksi boleh diedit oleh user.
     *
     * @throws \DomainException
     */
    private function assertEditable(User $user, Transaction $transaction): void
    {
        // Hanya pemilik yang boleh edit
        if ((string) $transaction->user_id !== (string) $user->getKey()) {
            throw new \DomainException('You do not own this transaction.');
        }

        // Goal-linked transaction tidak bisa diedit dari modul transaksi
        if ($transaction->goal_id) {
            throw new \DomainException('Goal-linked transactions cannot be edited from the transaction module.');
        }

        // System transaction tidak bisa diedit
        if ($transaction->is_system) {
            throw new \DomainException('System transactions cannot be edited.');
        }
    }

    /**
     * Petakan category_name ke kategori yang dikenal.
     * Jika null atau tidak dikenal, return 'Other'.
     */
    private function resolveCategory(?string $categoryName): string
    {
        return TransactionCategory::resolve($categoryName);
    }
}
