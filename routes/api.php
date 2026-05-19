<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\FamilyController;
use App\Http\Controllers\Api\V1\GoalController;
use App\Http\Controllers\Api\V1\MonthlyLimitController;
use App\Http\Controllers\Api\V1\TransactionController;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth.token')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::patch('/me', [AuthController::class, 'updateProfile']);

        // Categories — accessible by all authenticated members
        Route::get('/categories', [CategoryController::class, 'index']);

        // Dashboard — personal
        Route::get('/dashboard/personal', [DashboardController::class, 'personal']);

        // Family — accessible by all authenticated members
        Route::post('/families', [FamilyController::class, 'create']);
        Route::post('/families/join', [FamilyController::class, 'join']);
        Route::get('/families/me', [FamilyController::class, 'show']);

        // Transactions — accessible by all authenticated members
        Route::get('/transactions', [TransactionController::class, 'index']);
        Route::post('/transactions/preview', [TransactionController::class, 'preview']);
        Route::post('/transactions', [TransactionController::class, 'store']);
        Route::get('/transactions/{id}', [TransactionController::class, 'show']);
        Route::put('/transactions/{id}/preview', [TransactionController::class, 'editPreview']);
        Route::put('/transactions/{id}', [TransactionController::class, 'edit']);
        Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);

        // Goals — scope by logged-in user only
        Route::get('/goals', [GoalController::class, 'index']);
        Route::post('/goals', [GoalController::class, 'store']);
        Route::get('/goals/{id}', [GoalController::class, 'show']);
        Route::put('/goals/{id}/archive', [GoalController::class, 'archive']);
        Route::post('/goals/{id}/allocate/preview', [GoalController::class, 'allocatePreview']);
        Route::post('/goals/{id}/allocate', [GoalController::class, 'allocate']);
        Route::post('/goals/{id}/withdraw', [GoalController::class, 'withdraw']);

        // Family dashboard & management — admin only
        Route::middleware('admin')->group(function () {
            Route::get('/dashboard/family', [DashboardController::class, 'family']);
            Route::get('/families/members', [FamilyController::class, 'members']);
            Route::put('/families/{id}', [FamilyController::class, 'updateName']);
            Route::put('/families/members/{userId}/role', [FamilyController::class, 'updateMemberRole']);
            Route::delete('/families/members/{userId}', [FamilyController::class, 'removeMember']);

            // Monthly Limit — admin only
            Route::put('/admin/members/{userId}/monthly-limit', [MonthlyLimitController::class, 'setLimit']);
        });
    });
});
