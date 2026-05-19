<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

final class ApiLogger
{
    /**
     * Log operasi yang gagal karena business rule violation (DomainException).
     */
    public static function domainError(
        string $operation,
        string $userId,
        string $message,
        array $context = []
    ): void {
        Log::warning("[$operation] Domain error for user $userId: $message", $context);
    }

    /**
     * Log operasi yang gagal karena resource tidak ditemukan.
     */
    public static function notFound(
        string $operation,
        string $userId,
        string $resourceId,
        array $context = []
    ): void {
        Log::info("[$operation] Resource not found for user $userId: $resourceId", $context);
    }

    /**
     * Log operasi yang gagal karena unauthorized access.
     */
    public static function unauthorized(
        string $operation,
        string $userId,
        string $message,
        array $context = []
    ): void {
        Log::warning("[$operation] Unauthorized access by user $userId: $message", $context);
    }

    /**
     * Log operasi yang berhasil dengan warning (misal: saldo negatif dikonfirmasi).
     */
    public static function warningConfirmed(
        string $operation,
        string $userId,
        array $warnings,
        array $context = []
    ): void {
        Log::info("[$operation] User $userId confirmed warnings: " . implode(', ', $warnings), $context);
    }
}
