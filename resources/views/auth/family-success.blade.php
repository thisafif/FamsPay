{{-- resources/views/auth/family-success.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FamsPay - Grup Berhasil Dibuat</title>
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
        {{-- Menggunakan asset ilustrasi pengaturan grup sesuai mockup --}}
        <img
            src="{{ asset('images/create-family-illustration.png') }}"
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

    {{-- ── PANEL KANAN: Konten Berhasil Rata Tengah ── --}}
    <div style="
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        background: white;
    ">
        <div style="width: 100%; max-width: 440px; text-align: center;">

            {{-- Lingkaran Hijau Centang Sukses (Besar & Center) --}}
            <div style="margin-bottom: 32px; display: flex; justify-content: center;">
                <div style="
                    width: 140px;
                    height: 140px;
                    background-color: #10B981;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.3);
                ">
                    {{-- Icon Centang Putih Tebal --}}
                    <svg style="width: 70px; height: 70px; color: white;" fill="none" stroke="currentColor" stroke-width="4" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>

            {{-- Judul Berhasil --}}
            <h1 style="font-size: 2rem; font-weight: 800; color: #1e293b; margin: 0 0 16px; line-height: 1.2;">
                Grup Berhasil Dibuat
            </h1>
            
            {{-- Deskripsi Undangan --}}
            <p style="font-size: 1rem; color: #64748b; margin: 0 0 40px; line-height: 1.6;">
                Uang jadi lebih teratur kalau dikelola bareng-bareng.<br>Ayo undang anggota keluarga lainnya sekarang.
            </p>

            {{-- Tombol Selanjutnya (Radius 16px) menuju Dashboard Utama --}}
            <a
                href="{{ route('family.invite') }}"
                style="
                    display: block;
                    width: 100%;
                    background: #10B981;
                    color: white;
                    font-weight: 700;
                    font-size: 1rem;
                    padding: 18px;
                    border-radius: 16px; {{-- Sudut melengkung kotak 16px --}}
                    text-align: center;
                    text-decoration: none;
                    box-sizing: border-box;
                    transition: background 0.2s;
                "
                onmouseover="this.style.background='#059669'"
                onmouseout="this.style.background='#10B981'"
            >
                Selanjutnya
            </a>

        </div>
    </div>

</body>
</html>