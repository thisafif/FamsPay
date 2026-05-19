<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateFamilyRequest;
use App\Http\Requests\Api\JoinFamilyRequest;
use App\Http\Requests\Api\UpdateFamilyRequest;
use App\Http\Requests\Api\UpdateMemberRoleRequest;
use App\Http\Resources\FamilyResource;
use App\Http\Resources\UserResource;
use App\Services\FamilyService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class FamilyController extends Controller
{
    public function __construct(
        private readonly FamilyService $familyService,
    ) {}

    public function create(CreateFamilyRequest $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        try {
            $family = $this->familyService->createFamily(
                user: $user,
                name: $request->string('family_name')->toString(),
            );
        } catch (\DomainException $e) {
            return ApiResponse::error($e->getMessage(), 409);
        }

        return ApiResponse::success(
            data: new FamilyResource($family),
            message: 'Family created',
            status: 201,
        );
    }

    public function join(JoinFamilyRequest $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        try {
            $family = $this->familyService->joinFamily(
                user: $user,
                joinCode: $request->string('join_code')->toString(),
            );
        } catch (\DomainException $e) {
            return ApiResponse::error($e->getMessage(), 409);
        } catch (\InvalidArgumentException $e) {
            return ApiResponse::error($e->getMessage(), 422);
        }

        return ApiResponse::success(
            data: new FamilyResource($family),
            message: 'Joined family',
        );
    }

    /**
     * GET /api/v1/families/me
     * Menampilkan data family milik user yang sedang login.
     */
    public function show(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        try {
            $family = $this->familyService->getFamily($user);
        } catch (\DomainException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        }

        return ApiResponse::success(
            data: new FamilyResource($family),
            message: 'Family retrieved.',
        );
    }

    /**
     * PUT /api/v1/families/{id}
     * Hanya admin (dijaga middleware 'admin').
     */
    public function updateName(UpdateFamilyRequest $request, string $id): JsonResponse
    {
        /** @var \App\Models\User $admin */
        $admin = $request->user();

        try {
            $family = $this->familyService->updateFamilyName(
                admin: $admin,
                familyName: $request->string('family_name')->toString(),
            );
        } catch (\DomainException $e) {
            return ApiResponse::error($e->getMessage(), 409);
        }

        return ApiResponse::success(
            data: new FamilyResource($family),
            message: 'Family name updated.',
        );
    }

    /**
     * GET /api/v1/families/members
     * Semua member family bisa mengakses.
     */
    public function members(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        try {
            $members = $this->familyService->getFamilyMembers($user);
        } catch (\DomainException $e) {
            return ApiResponse::error($e->getMessage(), 409);
        }

        return ApiResponse::success(
            data: UserResource::collection($members),
            message: 'Family members retrieved.',
        );
    }

    /**
     * DELETE /api/v1/families/members/{userId}
     * Hanya admin (dijaga middleware 'admin').
     */
    public function removeMember(Request $request, string $userId): JsonResponse
    {
        /** @var \App\Models\User $admin */
        $admin = $request->user();

        try {
            $this->familyService->removeMember(
                admin: $admin,
                targetUserId: $userId,
            );
        } catch (\DomainException $e) {
            return ApiResponse::error($e->getMessage(), 409);
        } catch (\InvalidArgumentException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        }

        return ApiResponse::success(message: 'Member removed from family.');
    }

    /**
     * PUT /api/v1/families/members/{userId}/role
     * Hanya admin (dijaga middleware 'admin').
     */
    public function updateMemberRole(UpdateMemberRoleRequest $request, string $userId): JsonResponse
    {
        /** @var \App\Models\User $admin */
        $admin = $request->user();

        try {
            $updatedUser = $this->familyService->updateMemberRole(
                admin: $admin,
                targetUserId: $userId,
                newRole: $request->string('role')->toString(),
            );
        } catch (\DomainException $e) {
            return ApiResponse::error($e->getMessage(), 409);
        } catch (\InvalidArgumentException $e) {
            return ApiResponse::error($e->getMessage(), 404);
        }

        return ApiResponse::success(
            data: new UserResource($updatedUser),
            message: "Role updated to {$updatedUser->role}.",
        );
    }
}
