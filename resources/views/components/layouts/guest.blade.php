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
<body style="background-color: #10B981; height: 100vh; margin: 0; overflow: hidden; display: flex; align-items: center; justify-content: center;">

    <div style="width: 100%; height: 100vh; position: relative; display: flex; align-items: center; justify-content: center;">

        {{-- Vector blob kanan atas --}}
        <img src="{{ asset('images/Vector.png') }}" alt=""
            style="position: absolute; top: 0; right: 0; width: 45%; max-width: 700px; pointer-events: none;">

        {{-- Ellipse kiri bawah --}}
        <img src="{{ asset('images/blob.png') }}" alt=""
            style="position: absolute; bottom: 0; left: 0; width: 65%; max-width: 320px; pointer-events: none;">

        {{-- Auth Card — compact, truly centered --}}
        <div style="position: relative; z-index: 10; width: 100%; max-width: 360px; margin: 0 16px; background: white; border-radius: 18px; box-shadow: 0 16px 40px rgba(0,0,0,0.16);">
            {{ $slot }}
        </div>

    </div>
</body>
</html>
