<?php

namespace App\Repositories;

use App\Models\Transaction;

final class TransactionRepository
{
    /**
     * Buat transaksi baru.
     */
    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    /**
     * Cari transaksi aktif (belum dihapus) berdasarkan ID.
     */
    public function findActiveById(string $id): ?Transaction
    {
        return Transaction::where('_id', $id)
            ->where('is_deleted', false)
            ->first();
    }

    /**
     * Ambil daftar transaksi dengan filter dan sorting.
     *
     * @param  array{
     *   scope_field: string,
     *   scope_value: string,
     *   date_from?: string|null,
     *   date_to?: string|null,
     *   type?: string|null,
     *   category?: string|null,
     * } $filters
     */
    public function listActive(array $filters): \Illuminate\Database\Eloquent\Collection
    {
        $query = Transaction::where('is_deleted', false)
            ->where($filters['scope_field'], $filters['scope_value']);

        if (!empty($filters['date_from'])) {
            $query->where('txn_date', '>=', \Carbon\Carbon::parse($filters['date_from'])->startOfDay());
        }

        if (!empty($filters['date_to'])) {
            $query->where('txn_date', '<=', \Carbon\Carbon::parse($filters['date_to'])->endOfDay());
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['category'])) {
            $query->where('category_name', $filters['category']);
        }

        return $query->orderBy('txn_date', 'desc')->get();
    }

    /**
     * Update field transaksi yang sudah ada.
     */
    public function update(Transaction $transaction, array $data): Transaction
    {
        $transaction->forceFill($data)->save();

        return $transaction->fresh();
    }
}
