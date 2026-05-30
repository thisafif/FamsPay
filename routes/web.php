<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| 1. JALUR GUEST (Belum Login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Tampilan Halaman
    Route::get('/register', fn() => view('auth.register'))->name('register');
    Route::get('/login', fn() => view('auth.login'))->name('login');

    // Proses Submit Register Langsung ke BE
    Route::post('/register', function (Request $request) {
        $request->validate([
            'name' => 'required|string|min:2',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        try {
            $apiRegisterRequest = new \App\Http\Requests\Api\RegisterRequest();
            $apiRegisterRequest->replace([
                'full_name' => $request->name,
                'email'     => $request->email,
                'password'  => $request->password,
            ]);

            $authController = app(\App\Http\Controllers\Api\V1\AuthController::class);
            $registerResponse = $authController->register($apiRegisterRequest);

            if ($registerResponse->getStatusCode() === 201) {
                // Langsung Login-kan otomatis setelah sukses daftar
                $apiLoginRequest = new \App\Http\Requests\Api\LoginRequest();
                $apiLoginRequest->replace([
                    'email' => $request->email,
                    'password' => $request->password,
                    'device_name' => 'web_browser'
                ]);

                $loginResponse = $authController->login($apiLoginRequest);
                $loginData = $loginResponse->getData(true);

                if ($loginResponse->getStatusCode() === 200) {
                    Session::put('auth_token', $loginData['data']['token'] ?? null);
                    Session::put('user', $loginData['data']['user'] ?? null);
                    
                    // User baru mendaftar pasti belum punya keluarga, arahkan ke pilih keluarga
                    return redirect()->route('family.choose');
                }
            }
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal mendaftar: ' . $e->getMessage());
        }
        return back()->withInput()->with('error', 'Registrasi gagal.');
    });

    // Proses Submit Login Langsung ke BE
    Route::post('/login', function (Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $apiLoginRequest = new \App\Http\Requests\Api\LoginRequest();
            $apiLoginRequest->replace([
                'email' => $request->email,
                'password' => $request->password,
                'device_name' => 'web_browser'
            ]);

            $authController = app(\App\Http\Controllers\Api\V1\AuthController::class);
            $loginResponse = $authController->login($apiLoginRequest);
            $loginData = $loginResponse->getData(true);

            if ($loginResponse->getStatusCode() === 200) {
                $user = $loginData['data']['user'] ?? null;
                
                Session::put('auth_token', $loginData['data']['token'] ?? null);
                Session::put('user', $user);

                // CEK PINTAR: Jika sudah terikat ke grup keluarga, langsung ke dashboard. Jika belum, ke choose-family.
                if (!empty($user['family_id'])) {
                    return redirect()->route('dashboard');
                }
                
                return redirect()->route('family.choose');
            }
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Login gagal: ' . $e->getMessage());
        }
        return back()->withInput()->with('error', 'Kredensial tidak valid.');
    });
});

/*
|--------------------------------------------------------------------------
| 2. JALUR AUTH (Sudah Login / Pengecekan Session Token)
|--------------------------------------------------------------------------
*/
// Tampilan-tampilan Halaman Keluarga
Route::get('/choose-family', function() {
    if (!session('auth_token')) return redirect()->route('login');
    return view('auth.choose-family');
})->name('family.choose');

Route::get('/family/create', function() {
    if (!session('auth_token')) return redirect()->route('login');
    return view('auth.create-family');
})->name('family.create');

Route::get('/family/success', function() {
    if (!session('auth_token')) return redirect()->route('login');
    
    // Pengecekan ketat session kosong agar tidak crash saat di-refresh
    $joinCode = session('join_code');
    $joinCode = blank($joinCode) ? 'FAM67X' : $joinCode;
    
    $familyName = session('family_name');
    $familyName = blank($familyName) ? 'Keluarga Baru' : $familyName;
    
    return view('auth.family-success', compact('joinCode', 'familyName'));
})->name('family.success');

Route::get('/family/invite', function() {
    if (!session('auth_token')) return redirect()->route('login');
    
    // Pengecekan ketat session kosong agar tidak crash saat di-refresh
    $joinCode = session('join_code');
    $joinCode = blank($joinCode) ? 'FAM67X' : $joinCode;
    
    $familyName = session('family_name');
    $familyName = blank($familyName) ? 'Keluarga Baru' : $familyName;
    
    return view('auth.family-invite', compact('joinCode', 'familyName'));
})->name('family.invite');

Route::get('/family/join', function() {
    if (!session('auth_token')) return redirect()->route('login');
    return view('auth.family-join');
})->name('family.join');

Route::get('/family/join-success', function() {
    if (!session('auth_token')) return redirect()->route('login');
    return view('auth.family-join-success');
})->name('family.join-success');

Route::get('/dashboard', function(Request $request) {
    if (!session('auth_token')) return redirect()->route('login');

    $user      = session('user');
    $isAdmin   = ($user['role'] ?? 'member') === 'admin';
    $token     = session('auth_token');

    // ── Ambil data dashboard dari internal controller ──
    $userModel = new \App\Models\User();
    $userModel->forceFill($user ?? []);
    $userModel->exists = true;

    $dashboardController = app(\App\Http\Controllers\Api\V1\DashboardController::class);

    // Buat request palsu dengan user resolver & bearer token
    $fakeRequest = \Illuminate\Http\Request::create('/api/v1/dashboard/personal', 'GET');
    $fakeRequest->setUserResolver(fn() => $userModel);
    $fakeRequest->headers->set('Authorization', 'Bearer ' . $token);

    $personalData    = [];
    $familyData      = [];
    $membersData     = [];
    $isNewUser       = false;

    try {
        $personalResp = $dashboardController->personal($fakeRequest);
        $personalData = $personalResp->getData(true)['data'] ?? [];
    } catch (\Exception $e) {
        $personalData = [];
    }

    // Cek apakah user baru (tidak ada transaksi sama sekali)
    $isNewUser = empty($personalData['recent_transactions'])
        && ($personalData['wallet_balance'] ?? 0) === 0
        && ($personalData['total_savings'] ?? 0) === 0;

    if ($isAdmin) {
        try {
            $familyFakeReq = \Illuminate\Http\Request::create('/api/v1/dashboard/family', 'GET');
            $familyFakeReq->setUserResolver(fn() => $userModel);
            $familyData = $dashboardController->family($familyFakeReq)->getData(true)['data'] ?? [];
        } catch (\Exception $e) {
            $familyData = [];
        }

        // Ambil daftar anggota keluarga
        try {
            $familyController = app(\App\Http\Controllers\Api\V1\FamilyController::class);
            $membersFakeReq   = \Illuminate\Http\Request::create('/api/v1/families/members', 'GET');
            $membersFakeReq->setUserResolver(fn() => $userModel);
            $membersResp  = $familyController->members($membersFakeReq);
            $membersData  = $membersResp->getData(true)['data'] ?? [];
        } catch (\Exception $e) {
            $membersData = [];
        }
    }

    return view('dashboard', compact(
        'user', 'isAdmin', 'personalData', 'familyData', 'membersData', 'isNewUser'
    ));
})->name('dashboard');

// Proses Submit Buat Grup Keluarga Langsung ke BE
Route::post('/family/create', function (Request $request) {
    if (!session('auth_token')) return redirect()->route('login');
    $request->validate(['family_name' => 'required|string|min:3|max:50']);

    try {
        $userModel = session('user');
        $user = new \App\Models\User();
        $user->forceFill($userModel ?? []);
        $user->exists = true;

        $apiFamilyRequest = new \App\Http\Requests\Api\CreateFamilyRequest();
        $apiFamilyRequest->replace(['family_name' => $request->family_name]);
        $apiFamilyRequest->setUserResolver(fn() => $user);

        $familyController = app(\App\Http\Controllers\Api\V1\FamilyController::class);
        $familyResponse = $familyController->create($apiFamilyRequest);
        $familyData = $familyResponse->getData(true);

        if ($familyResponse->getStatusCode() === 201) {
            $data = $familyData['data'] ?? [];
            
            // Simpan informasi grup ke session
            session([
                'join_code' => $data['join_code'] ?? 'FAM67X',
                'family_name' => $data['name'] ?? $request->family_name
            ]);

            // Sinkronisasi status admin dan family_id baru ke session user lokal agar langsung sinkron
            $userModel['role'] = 'admin';
            $userModel['family_id'] = $data['id'] ?? 'has_family';
            session(['user' => $userModel]);

            return redirect()->route('family.success');
        }
        return back()->withInput()->with('error', $familyData['message'] ?? 'Gagal membuat grup.');
    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Kesalahan sistem: ' . $e->getMessage());
    }
})->name('family.create.post');

// Proses Submit Gabung Keluarga Lewat Input Kode
Route::post('/family/join', function (Request $request) {
    if (!session('auth_token')) return redirect()->route('login');
    $request->validate(['join_code' => 'required|string']);

    try {
        $userModel = session('user');
        $user = new \App\Models\User();
        $user->forceFill($userModel ?? []);
        $user->exists = true;

        $apiJoinRequest = new \App\Http\Requests\Api\JoinFamilyRequest();
        $apiJoinRequest->replace(['join_code' => $request->join_code]);
        $apiJoinRequest->setUserResolver(fn() => $user);

        $familyController = app(\App\Http\Controllers\Api\V1\FamilyController::class);
        $joinResponse = $familyController->join($apiJoinRequest);

        if ($joinResponse->getStatusCode() === 200) {
            // Update session user lokal agar tercatat sudah memiliki family_id
            $userModel['family_id'] = 'has_family';
            session(['user' => $userModel]);
            
            return redirect()->route('family.join-success');
        }
        return back()->withInput()->with('error', $joinResponse->getData(true)['message'] ?? 'Kode salah.');
    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Kesalahan sistem: ' . $e->getMessage());
    }
})->name('family.join.post');

// Link otomatis gabung keluarga (Direct Link) yang dipanggil oleh halaman Invite
Route::get('/family/join-direct/{code}', function ($code) {
    session(['direct_join_code' => $code]);
    return redirect()->route('family.join-success');
})->name('family.join_direct');

// Root Link Redirector Pintar
Route::get('/', function () {
    if (session('auth_token')) {
        $user = session('user');
        if (!empty($user['family_id'])) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('family.choose');
    }
    return redirect()->route('login');
});

// Logout Web
Route::post('/logout', function (Request $request) {
    try {
        $token = session('auth_token');
        if ($token) {
            $authController = app(\App\Http\Controllers\Api\V1\AuthController::class);
            $fakeReq = \Illuminate\Http\Request::create('/api/v1/logout', 'POST');
            $userArr = session('user');
            $userModel = new \App\Models\User();
            $userModel->forceFill($userArr ?? []);
            $userModel->exists = true;
            $fakeReq->setUserResolver(fn() => $userModel);
            $authController->logout($fakeReq);
        }
    } catch (\Exception $e) {}

    Session::flush();
    return redirect()->route('login');
})->name('logout');

Route::get('/transaksi', fn() => view('transaksi'))->name('transaksi');
Route::get('/anggota', fn() => view('kelola-anggota'))->name('anggota');
Route::get('/goals', fn() => view('goals'))->name('goals');
Route::get('/pengaturan', fn() => view('pengaturan'))->name('pengaturan');