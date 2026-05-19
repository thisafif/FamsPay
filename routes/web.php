<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ChooseFamilyController;
use App\Http\Controllers\FamilyWebController;

/*
|--------------------------------------------------------------------------
| Guest Routes (belum login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {

    // Register
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    // Login
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

});

/*
|--------------------------------------------------------------------------
| Auth Routes (sudah login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth.session')->group(function () {

    Route::get('/choose-family', [ChooseFamilyController::class, 'index'])->name('family.choose');
    Route::get('/family/create', [ChooseFamilyController::class, 'create'])->name('family.create');
    Route::post('/family/create', [ChooseFamilyController::class, 'store'])->name('family.store');
    Route::get('/family/success', [ChooseFamilyController::class, 'success'])->name('family.success');

    // 1. Jalur menuju Halaman Undang/Ajak Keluarga Gabung
    Route::get('/family/invite', [ChooseFamilyController::class, 'invite'])->name('family.invite');

    // 2. Jalur Link Otomatis Gabung (Direct Link) saat di-klik member lain
    Route::get('/family/join-direct/{code}', [ChooseFamilyController::class, 'autoJoin'])->name('family.join_direct');

    Route::get('/family/join', fn() => view('auth.join-family'))->name('family.join');
    Route::get('/dashboard', fn() => view('welcome'))->name('dashboard');

    Route::get('/family/join', [ChooseFamilyController::class, 'showJoinForm'])->name('family.join');
    Route::post('/family/join', [ChooseFamilyController::class, 'processJoin'])->name('family.join.process');
    Route::get('/family/join-success', [ChooseFamilyController::class, 'joinSuccess'])->name('family.join.success');
});

// Logout (bisa dari mana saja selama ada session)
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

// Root redirect
Route::get('/', function () {
    return session('auth_token')
        ? redirect()->route('family.choose')
        : redirect()->route('login');
});
