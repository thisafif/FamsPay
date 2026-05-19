<?php

namespace App\Enums;

final class TransactionCategory
{
    // ── Default categories (user-facing) ────────────────────

    /** Income categories */
    public const INCOME = [
        'Salary',
        'Bonus',
        'Investment',
        'Gift',
        'Other',
    ];

    /** Expense categories */
    public const EXPENSE = [
        'Food',
        'Transport',
        'Shopping',
        'Health',
        'Education',
        'Entertainment',
        'Bills',
        'Housing',
        'Savings',
        'Other',
    ];

    // ── System categories (dibuat otomatis oleh sistem) ──────

    /** Dibuat saat user alokasi dana ke goal */
    public const SAVINGS = 'Savings';

    /** Dibuat saat user withdraw dana dari goal */
    public const GOAL_WITHDRAWAL = 'Goal Withdrawal';

    // ── All known categories (untuk resolveCategory) ─────────

    public const ALL = [
        // Income
        'Salary', 'Bonus', 'Investment', 'Gift',
        // Expense
        'Food', 'Transport', 'Shopping', 'Health', 'Education',
        'Entertainment', 'Bills', 'Housing', 'Savings',
        // System
        'Goal Withdrawal',
        // Fallback
        'Other',
    ];

    /**
     * Petakan category_name ke kategori yang dikenal.
     * Jika null atau tidak dikenal, return 'Other'.
     */
    public static function resolve(?string $categoryName): string
    {
        if (!$categoryName) {
            return 'Other';
        }

        foreach (self::ALL as $known) {
            if (strtolower($known) === strtolower(trim($categoryName))) {
                return $known;
            }
        }

        return 'Other';
    }
}
