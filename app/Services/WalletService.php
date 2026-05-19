<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;

final class WalletService
{
    /**
     * Terapkan efek income ke wallet_balance user.
     * Income menambah saldo.
     *
     * @return array{balance: int, warning: string|null}
     */
    public function applyIncome(User $user, int $amount): array
    {
        $newBalance = (int) $user->wallet_balance + $amount;

        $user->forceFill(['wallet_balance' => $newBalance])->save();

        return $this->result($newBalance);
    }

    /**
     * Terapkan efek expense ke wallet_balance user.
     * Expense mengurangi saldo — boleh negatif.
     *
     * @return array{balance: int, warning: string|null}
     */
    public function applyExpense(User $user, int $amount): array
    {
        $newBalance = (int) $user->wallet_balance - $amount;

        $user->forceFill(['wallet_balance' => $newBalance])->save();

        return $this->result($newBalance);
    }

    /**
     * Balikkan efek transaksi yang sudah ada (untuk soft delete).
     * Income → kurangi saldo, Expense → tambah saldo.
     *
     * @return array{balance: int, warning: string|null}
     */
    public function reverseTransaction(User $user, Transaction $transaction): array
    {
        $amount = (int) $transaction->amount;

        if ($transaction->type === 'income') {
            $newBalance = (int) $user->wallet_balance - $amount;
        } else {
            // expense
            $newBalance = (int) $user->wallet_balance + $amount;
        }

        $user->forceFill(['wallet_balance' => $newBalance])->save();

        return $this->result($newBalance);
    }

    /**
     * Handle edit transaksi — hitung delta antara amount lama dan baru.
     * Balikkan efek lama, terapkan efek baru.
     *
     * @return array{balance: int, warning: string|null}
     */
    public function applyEditDelta(
        User $user,
        Transaction $oldTransaction,
        string $newType,
        int $newAmount,
    ): array {
        // Balikkan efek transaksi lama
        $balance = (int) $user->wallet_balance;

        if ($oldTransaction->type === 'income') {
            $balance -= (int) $oldTransaction->amount;
        } else {
            $balance += (int) $oldTransaction->amount;
        }

        // Terapkan efek transaksi baru
        if ($newType === 'income') {
            $balance += $newAmount;
        } else {
            $balance -= $newAmount;
        }

        $user->forceFill(['wallet_balance' => $balance])->save();

        return $this->result($balance);
    }

    /**
     * Hitung projected balance setelah expense tanpa menyimpan ke DB.
     * Digunakan untuk preview sebelum transaksi dibuat.
     *
     * @return array{projected_balance: int, warning: string|null}
     */
    public function projectExpense(User $user, int $amount): array
    {
        $projected = (int) $user->wallet_balance - $amount;

        return [
            'projected_balance' => $projected,
            'warning'           => $projected < 0
                ? 'This transaction will result in a negative wallet balance.'
                : null,
        ];
    }

    // ── Private helpers ──────────────────────────────────────

    /**
     * Buat array hasil standar dengan warning jika saldo negatif.
     *
     * @return array{balance: int, warning: string|null}
     */
    private function result(int $balance): array
    {
        return [
            'balance' => $balance,
            'warning' => $balance < 0
                ? 'Wallet balance is negative.'
                : null,
        ];
    }
}
