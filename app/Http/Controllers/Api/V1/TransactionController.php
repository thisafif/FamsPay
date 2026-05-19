<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateTransactionRequest;
use App\Http\Requests\Api\PreviewTransactionRequest;
use App\Http\Requests\Api\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Services\TransactionService;
use App\Support\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService,
    ) {}

    /**
     * DELETE /api/v1/transactions/{id}
     * Soft delete transaksi milik user.
     * Reverse efek wallet dan monthly limit.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        try {
            $result = $this->transactionService->delete(
                user: $user,
                transactionId: $id,
            );
        } catch (\InvalidArgumentException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (\DomainException $e) {
            return ApiResponse::error($e->getMessage(), 403);
        }

        return ApiResponse::success(
            data: ['wallet_balance' => $result['wallet_balance']],
            message: 'Transaction deleted successfully.',
        );
    }

    /**
     * GET /api/v1/transactions/{id}
     * Detail transaksi.
     * Member: hanya miliknya. Admin: semua dalam family.
     * Response menyertakan is_system, goal_id, dan read_only flag.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        try {
            $transaction = $this->transactionService->show(
                user: $user,
                transactionId: $id,
            );
        } catch (\InvalidArgumentException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (\DomainException $e) {
            return ApiResponse::error($e->getMessage(), 403);
        }

        return ApiResponse::success(
            data: new TransactionResource($transaction),
            message: 'Transaction retrieved.',
        );
    }

    /**
     * GET /api/v1/transactions
     * Member: hanya transaksi miliknya.
     * Admin: semua transaksi dalam family.
     * Filter: date_from, date_to, type, category.
     * Sort: txn_date desc (default).
     */
    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $transactions = $this->transactionService->list(
            user: $user,
            dateFrom: $request->query('date_from'),
            dateTo: $request->query('date_to'),
            type: $request->query('type'),
            category: $request->query('category'),
        );

        return ApiResponse::success(
            data: TransactionResource::collection($transactions),
            message: 'Transactions retrieved.',
        );
    }

    /**
     * POST /api/v1/transactions/preview
     * Hitung proyeksi wallet dan remaining limit tanpa menyimpan.
     */
    public function preview(PreviewTransactionRequest $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user    = $request->user();
        $txnDate = Carbon::parse($request->input('txn_date'));

        $result = $this->transactionService->preview(
            user: $user,
            type: $request->input('type'),
            amount: (int) $request->input('amount'),
            txnDate: $txnDate,
            categoryName: $request->input('category_name'),
        );

        return ApiResponse::success(
            data: $result,
            message: 'Transaction preview calculated.',
        );
    }

    /**
     * POST /api/v1/transactions
     * Buat transaksi baru.
     * Jika ada warning, user harus kirim confirm=true untuk melanjutkan.
     */
    public function store(CreateTransactionRequest $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user    = $request->user();
        $txnDate = Carbon::parse($request->input('txn_date'));
        $confirm = (bool) $request->input('confirm', false);

        try {
            $result = $this->transactionService->create(
                user: $user,
                type: $request->input('type'),
                amount: (int) $request->input('amount'),
                txnDate: $txnDate,
                categoryName: $request->input('category_name'),
                note: $request->input('note'),
                goalId: $request->input('goal_id'),
                confirm: $confirm,
            );
        } catch (\DomainException $e) {
            // Ada warning dan belum dikonfirmasi
            return ApiResponse::error(
                message: 'Transaction requires confirmation.',
                status: 422,
                errors: ['warnings' => [$e->getMessage()]],
            );
        }

        return ApiResponse::success(
            data: [
                'transaction'    => new TransactionResource($result['transaction']),
                'wallet_balance' => $result['wallet_balance'],
                'warnings'       => $result['warnings'],
            ],
            message: 'Transaction created successfully.',
            status: 201,
        );
    }

    /**
     * PUT /api/v1/transactions/{id}/preview
     * Preview efek edit transaksi tanpa menyimpan.
     */
    public function editPreview(UpdateTransactionRequest $request, string $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user    = $request->user();
        $txnDate = Carbon::parse($request->input('txn_date'));

        try {
            $result = $this->transactionService->previewEdit(
                user: $user,
                transactionId: $id,
                newType: $request->input('type'),
                newAmount: (int) $request->input('amount'),
                newTxnDate: $txnDate,
                categoryName: $request->input('category_name'),
            );
        } catch (\InvalidArgumentException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (\DomainException $e) {
            return ApiResponse::error($e->getMessage(), 403);
        }

        return ApiResponse::success(
            data: $result,
            message: 'Edit preview calculated.',
        );
    }

    /**
     * PUT /api/v1/transactions/{id}
     * Update transaksi milik user.
     * Jika ada warning, user harus kirim confirm=true untuk melanjutkan.
     */
    public function edit(UpdateTransactionRequest $request, string $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user    = $request->user();
        $txnDate = Carbon::parse($request->input('txn_date'));
        $confirm = (bool) $request->input('confirm', false);

        try {
            $result = $this->transactionService->update(
                user: $user,
                transactionId: $id,
                newType: $request->input('type'),
                newAmount: (int) $request->input('amount'),
                newTxnDate: $txnDate,
                categoryName: $request->input('category_name'),
                note: $request->input('note'),
                confirm: $confirm,
            );
        } catch (\InvalidArgumentException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (\DomainException $e) {
            // Bisa: tidak punya akses, goal-linked, system, atau butuh konfirmasi
            $status = str_contains($e->getMessage(), 'confirmation') ||
                      str_contains($e->getMessage(), 'negative') ? 422 : 403;

            return ApiResponse::error(
                message: $status === 422 ? 'Transaction requires confirmation.' : $e->getMessage(),
                status: $status,
                errors: $status === 422 ? ['warnings' => [$e->getMessage()]] : [],
            );
        }

        return ApiResponse::success(
            data: [
                'transaction'    => new TransactionResource($result['transaction']),
                'wallet_balance' => $result['wallet_balance'],
                'warnings'       => $result['warnings'],
            ],
            message: 'Transaction updated successfully.',
        );
    }
}
