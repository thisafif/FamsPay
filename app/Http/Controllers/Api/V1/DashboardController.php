<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {}

    /**
     * GET /api/v1/dashboard/personal
     * Personal dashboard untuk user yang sedang login.
     * Scope by user_id — tidak ada data user lain.
     */
    public function personal(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user   = $request->user();
        $data   = $this->dashboardService->getPersonalDashboard($user);

        return ApiResponse::success(
            data: $data,
            message: 'Personal dashboard retrieved.',
        );
    }

    /**
     * GET /api/v1/dashboard/family
     * Family dashboard — hanya untuk admin.
     * Scope by family_id. Detail goals anggota lain tidak dibuka.
     */
    public function family(Request $request): JsonResponse
    {
        /** @var \App\Models\User $admin */
        $admin = $request->user();

        try {
            $data = $this->dashboardService->getFamilyDashboard($admin);
        } catch (\DomainException $e) {
            return ApiResponse::error($e->getMessage(), 403);
        }

        return ApiResponse::success(
            data: $data,
            message: 'Family dashboard retrieved.',
        );
    }
}
