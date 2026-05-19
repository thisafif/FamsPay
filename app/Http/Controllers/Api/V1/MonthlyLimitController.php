<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SetMonthlyLimitRequest;
use App\Http\Resources\MonthlyLimitResource;
use App\Services\MonthlyLimitService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

final class MonthlyLimitController extends Controller
{
    public function __construct(
        private readonly MonthlyLimitService $monthlyLimitService,
    ) {}

    /**
     * PUT /api/v1/admin/members/{userId}/monthly-limit
     * Hanya admin (dijaga middleware 'admin').
     */
    public function setLimit(SetMonthlyLimitRequest $request, string $userId): JsonResponse
    {
        /** @var \App\Models\User $admin */
        $admin = $request->user();

        try {
            $limit = $this->monthlyLimitService->setMonthlyLimit(
                admin: $admin,
                targetUserId: $userId,
                limitAmount: (int) $request->input('monthly_limit_base'),
                periodMonth: $request->input('period_month'),
            );
        } catch (\DomainException $e) {
            return ApiResponse::error($e->getMessage(), 409);
        } catch (\InvalidArgumentException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        }

        return ApiResponse::success(
            data: new MonthlyLimitResource($limit),
            message: 'Monthly limit set successfully.',
        );
    }
}
