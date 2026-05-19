<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FamsPay - Sukses Bergabung</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; }
    </style>
</head>
<body style="min-height:100vh; display:flex; overflow:hidden; background-color: #f8fafc;">

    {{-- KIRI: Banner Ilustrasi (Konsisten 35%) --}}
    <div style="width: 35%; min-height: 100vh; position: relative; overflow: hidden; background: #10B981;">
        <img src="{{ asset('images/invite-family-illustration.png') }}" alt="" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; pointer-events: none;">
    </div>

    {{-- KANAN: Notifikasi Sukses --}}
    <div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px; background: white;">
        <div style="width: 100%; max-width: 460px; text-align: center;">

            {{-- Lingkaran Centang Hijau Besar --}}
            <div style="margin-bottom: 28px; display: flex; justify-content: center;">
                <div style="width: 110px; height: 110px; background: #10B981; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);">
                    <svg style="width: 52px; height: 52px;" fill="none" stroke="currentColor" stroke-width="4.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>

            <h1 style="font-size: 2rem; font-weight: 800; color: #1e293b; margin: 0 0 12px;">Selamat Bergabung</h1>
            <p style="font-size: 0.95rem; color: #64748b; margin: 0 0 36px; line-height: 1.6; padding: 0 10px;">
                Uang jadi lebih teratur kalau dikelola bareng-bareng.<br>Ayo undang anggota keluarga lainnya sekarang.
            </p>

            <a href="{{ route('dashboard') }}" style="display: block; width: 100%; background: #10B981; color: white; font-weight: 700; font-size: 1rem; padding: 18px; border-radius: 16px; text-align: center; text-decoration: none; box-sizing: border-box; transition: background 0.2s;" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10B981'">
                Selanjutnya
            </a>

        </div>
    </div>

</body>
</html>