<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Support\ApiResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.token' => \App\Http\Middleware\AuthenticateApiToken::class,
            'admin'      => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $e, $request) {
            if (!($request->is('api/*') || $request->expectsJson())) {
                return null;
            }

            if ($e instanceof ValidationException) {
                return ApiResponse::error(
                    message: $e->getMessage() ?: 'Validation failed',
                    status: 422,
                    errors: $e->errors(),
                );
            }

            if ($e instanceof AuthenticationException) {
                return ApiResponse::error(
                    message: 'Unauthenticated',
                    status: 401,
                );
            }

            if ($e instanceof AuthorizationException) {
                return ApiResponse::error(
                    message: $e->getMessage() ?: 'Forbidden',
                    status: 403,
                );
            }

            if ($e instanceof NotFoundHttpException) {
                return ApiResponse::error(
                    message: 'Route not found',
                    status: 404,
                );
            }

            if ($e instanceof MethodNotAllowedHttpException) {
                return ApiResponse::error(
                    message: 'Method not allowed',
                    status: 405,
                );
            }

            if ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();
                $message = $e->getMessage() ?: match ($status) {
                    400 => 'Bad request',
                    401 => 'Unauthenticated',
                    403 => 'Forbidden',
                    404 => 'Not found',
                    default => 'Request failed',
                };

                return ApiResponse::error(
                    message: $message,
                    status: $status,
                );
            }

            $isDebug = (bool) config('app.debug');

            return ApiResponse::error(
                message: $isDebug ? ($e->getMessage() ?: 'Server error') : 'Server error',
                status: 500,
                errors: $isDebug ? [
                    'exception' => class_basename($e),
                ] : [],
            );
        });
    })->create();
