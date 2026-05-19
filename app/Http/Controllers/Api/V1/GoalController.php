<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AllocateGoalRequest;
use App\Http\Requests\Api\CreateGoalRequest;
use App\Http\Requests\Api\WithdrawGoalRequest;
use App\Http\Resources\GoalResource;
use App\Http\Resources\TransactionResource;
use App\Services\GoalService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GoalController extends Controller
{
    public function __construct(
        private readonly GoalService $goalService,
    ) {}

    /**
     * POST /api/v1/goals
     * Buat goal pribadi untuk user yang sedang login.
     */
    public function store(CreateGoalRequest $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        try {
            $goal = $this->goalService->create(
                user: $user,
                title: $request->string('title')->toString(),
                targetAmount: (int) $request->input('target_amount'),
            );
        } catch (\DomainException $e) {
            return ApiResponse::error($e->getMessage(), 422);
        }

        return ApiResponse::success(
            data: new GoalResource($goal),
            message: 'Goal created successfully.',
            status: 201,
        );
    }

    /**
     * GET /api/v1/goals
     * Ambil semua goal milik user yang sedang login.
     * Default: archived dikecualikan. Kirim ?include_archived=true untuk tampilkan semua.
     */
    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user           = $request->user();
        $includeArchived = filter_var($request->query('include_archived', false), FILTER_VALIDATE_BOOLEAN);
        $goals          = $this->goalService->listForUser($user, $includeArchived);

        return ApiResponse::success(
            data: GoalResource::collection($goals),
            message: 'Goals retrieved.',
        );
    }

    /**
     * GET /api/v1/goals/{id}
     * Detail goal milik user yang sedang login.
     * Goals bersifat privat — hanya pemilik yang bisa akses.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        try {
            $goal = $this->goalService->show(user: $user, goalId: $id);
        } catch (\InvalidArgumentException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (\DomainException $e) {
            return ApiResponse::error($e->getMessage(), 403);
        }

        return ApiResponse::success(
            data: new GoalResource($goal),
            message: 'Goal retrieved.',
        );
    }

    /**
     * PUT /api/v1/goals/{id}/archive
     * Archive goal — status berubah menjadi 'archived'.
     * Transaksi goal-linked tetap tersimpan (tidak dihapus).
     */
    public function archive(Request $request, string $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        try {
            $goal = $this->goalService->archive(
                user: $user,
                goalId: $id,
            );
        } catch (\InvalidArgumentException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (\DomainException $e) {
            return ApiResponse::error($e->getMessage(), 403);
        }

        return ApiResponse::success(
            data: new GoalResource($goal),
            message: 'Goal archived successfully.',
        );
    }

    /**
     * POST /api/v1/goals/{id}/allocate/preview
     * Preview efek alokasi dana ke goal tanpa menyimpan.
     */
    public function allocatePreview(AllocateGoalRequest $request, string $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        try {
            $result = $this->goalService->previewAllocate(
                user: $user,
                goalId: $id,
                amount: (int) $request->input('amount'),
            );
        } catch (\InvalidArgumentException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (\DomainException $e) {
            return ApiResponse::error($e->getMessage(), 403);
        }

        return ApiResponse::success(
            data: $result,
            message: 'Allocation preview calculated.',
        );
    }

    /**
     * POST /api/v1/goals/{id}/allocate
     * Alokasikan dana ke goal — buat system expense transaction.
     * Jika ada warning, user harus kirim confirm=true untuk melanjutkan.
     */
    public function allocate(AllocateGoalRequest $request, string $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user    = $request->user();
        $confirm = (bool) $request->input('confirm', false);

        try {
            $result = $this->goalService->allocate(
                user: $user,
                goalId: $id,
                amount: (int) $request->input('amount'),
                confirm: $confirm,
            );
        } catch (\InvalidArgumentException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (\DomainException $e) {
            $isWarning = str_contains(strtolower($e->getMessage()), 'negative')
                || str_contains(strtolower($e->getMessage()), 'wallet')
                || str_contains(strtolower($e->getMessage()), 'limit');

            if ($isWarning) {
                return ApiResponse::error(
                    message: 'Allocation requires confirmation.',
                    status: 422,
                    errors: ['warnings' => [$e->getMessage()]],
                );
            }

            return ApiResponse::error($e->getMessage(), 403);
        }

        return ApiResponse::success(
            data: [
                'goal'           => new GoalResource($result['goal']),
                'transaction'    => new TransactionResource($result['transaction']),
                'wallet_balance' => $result['wallet_balance'],
                'warnings'       => $result['warnings'],
            ],
            message: 'Allocation successful.',
            status: 201,
        );
    }

    /**
     * POST /api/v1/goals/{id}/withdraw
     * Withdraw dana dari goal — buat system income transaction.
     */
    public function withdraw(WithdrawGoalRequest $request, string $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        try {
            $result = $this->goalService->withdraw(
                user: $user,
                goalId: $id,
                amount: (int) $request->input('amount'),
            );
        } catch (\InvalidArgumentException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        } catch (\DomainException $e) {
            return ApiResponse::error($e->getMessage(), 422);
        }

        return ApiResponse::success(
            data: [
                'goal'           => new GoalResource($result['goal']),
                'transaction'    => new TransactionResource($result['transaction']),
                'wallet_balance' => $result['wallet_balance'],
            ],
            message: 'Withdrawal successful.',
            status: 201,
        );
    }
}
