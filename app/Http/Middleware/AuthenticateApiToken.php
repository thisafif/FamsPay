<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use App\Models\User;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AuthenticateApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = (string) $request->header('Authorization', '');

        if (!str_starts_with($header, 'Bearer ')) {
            return ApiResponse::error('Unauthenticated', 401);
        }

        $plainToken = trim(substr($header, 7));
        if ($plainToken === '') {
            return ApiResponse::error('Unauthenticated', 401);
        }

        $tokenHash = hash('sha256', $plainToken);
        $token = ApiToken::where('token_hash', $tokenHash)->first();

        if (!$token) {
            return ApiResponse::error('Unauthenticated', 401);
        }

        if ($token->expires_at && now()->greaterThan($token->expires_at)) {
            $token->delete();
            return ApiResponse::error('Unauthenticated', 401);
        }

        $user = User::find($token->user_id);
        if (!$user) {
            $token->delete();
            return ApiResponse::error('Unauthenticated', 401);
        }

        $token->forceFill(['last_used_at' => now()])->save();

        // Set user ke request (mirip auth)
        $request->setUserResolver(fn () => $user);
        $request->attributes->set('api_token', $token);

        return $next($request);
    }
}

