# Setup Notes — Daftarkan Middleware

Tambahkan middleware `AuthSession` ke `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'auth.session' => \App\Http\Middleware\AuthSession::class,
    ]);
})
```

Tambahkan API URL di `.env`:
```
API_URL=http://your-api-url.com/api
```

Tambahkan di `config/app.php`:
```php
'api_url' => env('API_URL', 'http://localhost:8000/api'),
```
