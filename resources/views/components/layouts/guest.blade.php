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

        .blob-bg {
            background-color: #34D399;
            background-image:
                radial-gradient(ellipse 80% 60% at 10% 10%, #10B981 0%, transparent 60%),
                radial-gradient(ellipse 70% 70% at 90% 90%, #059669 0%, transparent 55%),
                radial-gradient(ellipse 50% 50% at 80% 10%, #6EE7B7 0%, transparent 50%),
                radial-gradient(ellipse 60% 60% at 5% 85%, #047857 0%, transparent 50%);
        }

        /* Organic blob shapes */
        .blob-1 {
            position: absolute;
            width: 500px; height: 500px;
            background: rgba(16, 185, 129, 0.45);
            border-radius: 60% 40% 70% 30% / 50% 60% 40% 50%;
            top: -120px; left: -140px;
            animation: morph 10s ease-in-out infinite;
        }
        .blob-2 {
            position: absolute;
            width: 420px; height: 420px;
            background: rgba(4, 120, 87, 0.4);
            border-radius: 40% 60% 30% 70% / 60% 40% 60% 40%;
            bottom: -100px; right: -120px;
            animation: morph 12s ease-in-out infinite reverse;
        }
        .blob-3 {
            position: absolute;
            width: 300px; height: 300px;
            background: rgba(110, 231, 183, 0.3);
            border-radius: 70% 30% 50% 50% / 40% 60% 40% 60%;
            bottom: 60px; left: -60px;
            animation: morph 8s ease-in-out infinite 2s;
        }
        .blob-4 {
            position: absolute;
            width: 260px; height: 260px;
            background: rgba(5, 150, 105, 0.35);
            border-radius: 30% 70% 60% 40% / 50% 30% 70% 50%;
            top: 80px; right: -40px;
            animation: morph 9s ease-in-out infinite 1s reverse;
        }

        @keyframes morph {
            0%, 100% { border-radius: 60% 40% 70% 30% / 50% 60% 40% 50%; }
            33%       { border-radius: 40% 60% 30% 70% / 60% 40% 60% 40%; }
            66%       { border-radius: 70% 30% 50% 50% / 40% 70% 30% 60%; }
        }
    </style>
</head>
<body class="blob-bg antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center p-6 relative overflow-hidden">

        {{-- Animated blobs --}}
        <div class="blob-1"></div>
        <div class="blob-2"></div>
        <div class="blob-3"></div>
        <div class="blob-4"></div>

        {{-- Auth Card --}}
        <div class="w-full sm:max-w-md relative z-10 bg-white shadow-2xl overflow-hidden rounded-3xl">
            {{ $slot }}
        </div>

    </div>
</body>
</html>
