<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TransactionCategory;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

final class CategoryController extends Controller
{
    /**
     * GET /api/v1/categories
     * Kembalikan daftar kategori transaksi yang tersedia.
     */
    public function index(): JsonResponse
    {
        return ApiResponse::success(
            data: [
                'income'  => TransactionCategory::INCOME,
                'expense' => TransactionCategory::EXPENSE,
                'system'  => [
                    TransactionCategory::SAVINGS,
                    TransactionCategory::GOAL_WITHDRAWAL,
                ],
            ],
            message: 'Categories retrieved.',
        );
    }
}
