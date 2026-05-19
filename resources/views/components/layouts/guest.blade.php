<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'FamsPay - Welcome' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body style="background-color: #10B981; min-height: 100vh; margin: 0; overflow: hidden;">

    <div style="min-height: 100vh; width: 100%; position: relative; display: flex; align-items: center; justify-content: center;">

        {{-- Vector blob kanan atas --}}
        <img src="{{ asset('images/Vector.png') }}" alt=""
            style="position: absolute; top: 0; right: 0; width: 50%; max-width: 900px; pointer-events: none;">

        {{-- Ellipse kiri bawah --}}
        <img src="{{ asset('images/blob.png') }}" alt=""
            style="position: absolute; bottom: 0; left: 0; width: 76%; max-width: 400px; pointer-events: none;">

        {{-- Auth Card --}}
        <div style="position: relative; z-index: 10; width: 100%; max-width: 480px; margin: 0 16px; background: white; border-radius: 24px; box-shadow: 0 25px 60px rgba(0,0,0,0.15);">
            {{ $slot }}
        </div>

    </div>
</body>
</html>
