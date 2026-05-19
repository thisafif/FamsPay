{{-- resources/views/auth/choose-family.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FamsPay - Pilih Keluarga</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; }
    </style>
</head>
<body style="min-height:100vh; display:flex; overflow:hidden; background-color: #f8fafc;">

    {{-- ── PANEL KIRI: Background Ilustrasi ── --}}
    <div style="
        width: 35%; 
        min-height: 100vh;
        position: relative;
        overflow: hidden;
        background: #10B981;
    ">
        <img
            src="{{ asset('images/image-cfg1.png') }}"
            alt=""
            style="
                position: absolute;
                top: 0; left: 0;
                width: 100%; height: 100%;
                object-fit: cover;
                pointer-events: none;
            "
        >
    </div>

    {{-- ── PANEL KANAN: Konten Putih ── --}}
    <div style="
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        background: white;
    ">
        <div style="width: 100%; max-width: 480px; text-align: center;">

            {{-- Logo Diperbesar dan Ditengah --}}
            <div style="margin-bottom: 32px; display: flex; justify-content: center;">
                <img
                    src="{{ asset('images/choose-family-logo.png') }}"
                    alt="FamsPay"
                    style="width: 180px; height: auto; object-fit: contain;"
                >
            </div>

            {{-- Judul Besar & Tengah --}}
            <h1 style="font-size: 1.9rem; font-weight: 800; color: #1e293b; margin: 0 0 16px; line-height: 1.2;">
                Selamat datang di FamsPay!
            </h1>
            
            {{-- Sub-judul Tengah --}}
            <p style="font-size: 1.1rem; color: #64748b; margin: 0 0 40px; line-height: 1.6;">
                Aplikasi Pencatatan Keuangan<br>Keluarga Andalan Anda.
            </p>

            {{-- Flash error --}}
            @if (session('error'))
                <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:12px;padding:12px 16px;margin-bottom:20px;">
                    <span style="font-size:0.85rem;color:#DC2626;font-weight:500;">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Tombol Buat Grup (Hijau - Radius 16px) --}}
            <a
                href="{{ route('family.create') }}"
                style="
                    display: block;
                    width: 100%;
                    background: #10B981;
                    color: white;
                    font-weight: 700;
                    font-size: 0.95rem;
                    padding: 18px;
                    border-radius: 16px; {{-- Kembali ke radius 16px --}}
                    text-align: center;
                    text-decoration: none;
                    box-sizing: border-box;
                    margin-bottom: 20px;
                    transition: all 0.2s;
                "
                onmouseover="this.style.background='#059669'"
                onmouseout="this.style.background='#10B981'"
            >
                Saya ingin membuat grup family baru
            </a>

            {{-- Divider "atau" --}}
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:20px;">
                <div style="flex:1; height:1px; background:#e2e8f0;"></div>
                <span style="font-size:0.85rem; color:#94a3b8; font-weight: 500;">atau</span>
                <div style="flex:1; height:1px; background:#e2e8f0;"></div>
            </div>

            {{-- Tombol Gabung Grup (Abu-abu - Radius 16px) --}}
            <a
                href="{{ route('family.join') }}"
                style="
                    display: block;
                    width: 100%;
                    background: #F1F5F9;
                    color: #334155;
                    font-weight: 700;
                    font-size: 0.95rem;
                    padding: 18px;
                    border-radius: 16px; {{-- Kembali ke radius 16px --}}
                    text-align: center;
                    text-decoration: none;
                    box-sizing: border-box;
                    transition: all 0.2s;
                "
                onmouseover="this.style.background='#e2e8f0'"
                onmouseout="this.style.background='#F1F5F9'"
            >
                Saya ingin bergabung ke grup family
            </a>

        </div>
    </div>

</body>
</html>