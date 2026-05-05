<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\FamilyController;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
  
    Route::middleware('auth.token')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::patch('/me', [AuthController::class, 'updateProfile']);

        Route::post('/families', [FamilyController::class, 'create']);
        Route::post('/families/join', [FamilyController::class, 'join']);
        Route::patch('/families', [FamilyController::class, 'updateName']);
        Route::get('/families/members', [FamilyController::class, 'members']);
        Route::patch('/families/members/{userId}/role', [FamilyController::class, 'updateMemberRole']);
        Route::delete('/families/members/{userId}', [FamilyController::class, 'removeMember']);
    });
});