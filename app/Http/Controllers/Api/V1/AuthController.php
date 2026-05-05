<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\ApiToken;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'full_name' => $request->string('full_name')->toString(),
            'email' => $request->string('email')->lower()->toString(),
            'password_hash' => Hash::make($request->string('password')->toString()),
            'role' => 'member',
            'wallet_balance' => 0,
            'family_id' => null,
        ]);

        return ApiResponse::success(
            data: new UserResource($user),
            message: 'User registered successfully',
            status: 201,
        );
    }

    public function login(LoginRequest $request)
    {
        $email = $request->string('email')->lower()->toString();
        $password = $request->string('password')->toString();

        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, (string) $user->password_hash)) {
            return ApiResponse::error('Invalid credentials', 401);
        }

        $deviceName = $request->string('device_name')->toString() ?: 'api';
        $plainToken = Str::random(64);

        ApiToken::create([
            'user_id' => (string) $user->getKey(),
            'name' => $deviceName,
            'token_hash' => hash('sha256', $plainToken),
            'abilities' => ['*'],
            'expires_at' => null,
            'last_used_at' => now(),
        ]);

        return ApiResponse::success(
            data: [
                'token' => $plainToken,
                'user' => new UserResource($user),
            ],
            message: 'Login success',
        );
    }

    public function logout()
    {
        $user = request()->user();

        if (!$user) {
            return ApiResponse::error('Unauthenticated', 401);
        }

        /** @var \App\Models\ApiToken|null $token */
        $token = request()->attributes->get('api_token');
        $token?->delete();

        return ApiResponse::success(message: 'Logged out');
    }

    public function me()
    {
        $user = request()->user();

        if (!$user) {
            return ApiResponse::error('Unauthenticated', 401);
        }

        return ApiResponse::success(data: new UserResource($user), message: 'OK');
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $data = $request->safe()->only(['full_name', 'email', 'avatar_url']);
        if (array_key_exists('password', $request->validated())) {
            $data['password_hash'] = Hash::make($request->string('password')->toString());
        }

        // Normalisasi email
        if (isset($data['email'])) {
            $data['email'] = strtolower((string) $data['email']);
        }

        $user->fill($data);
        $user->save();

        return ApiResponse::success(
            data: new UserResource($user->fresh()),
            message: 'Profile updated',
        );
    }
}

