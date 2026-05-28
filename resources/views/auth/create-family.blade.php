{{-- resources/views/auth/create-family.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FamsPay - Buat Grup Keluarga</title>
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

    {{-- ── PANEL KANAN: Konten Form Putih ── --}}
    <div style="
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        background: white;
    ">
        <div style="width: 100%; max-width: 440px; text-align: center;">

            {{-- Logo Rumah Merah Muda (Diperbesar & Ditengah) --}}
            <div style="margin-bottom: 24px; display: flex; justify-content: center;">
                <img
                    src="{{ asset('images/create-family-logo.png') }}"
                    alt="FamsPay House"
                    style="width: 200px; height: auto; object-fit: contain;"
                >
            </div>

            {{-- Heading --}}
            <h1 style="font-size: 2.2rem; font-weight: 800; color: #1e293b; margin: 0 0 12px; line-height: 1.2;">
                Halo, Captain!
            </h1>
            
            {{-- Sub-heading --}}
            <p style="font-size: 1rem; color: #64748b; margin: 0 0 36px; line-height: 1.6;">
                Silahkan buat nama untuk group<br>keluarga anda.
            </p>

            {{-- Flash Error Handling jika proses API gagal --}}
            @if ($errors->any() || session('error'))
                <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:12px;padding:12px 16px;margin-bottom:20px;text-align:left;">
                    <span style="font-size:0.85rem;color:#DC2626;font-weight:500;">
                        {{ session('error') ?? $errors->first() }}
                    </span>
                </div>
            @endif

            {{-- Form Pengiriman Data --}}
            <form action="{{ route('family.create.post') }}" method="POST">
                @csrf

                {{-- Input Teks Nama Keluarga dengan Icon Rumah --}}
                <div style="
                    display:flex;
                    align-items:center;
                    gap:12px;
                    background:#F1F5F9;
                    border-radius:16px;
                    padding:16px;
                    margin-bottom:24px;
                    border:2px solid {{ $errors->has('family_name') ? '#FECACA' : 'transparent' }};
                ">
                    {{-- Icon Rumah Kecil di Samping Input Teks --}}
                    <svg style="width:22px;height:22px;color:#94a3b8;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    
                    {{-- name="family_name" agar langsung dibaca oleh CreateFamilyRequest milik API --}}
                    <input 
                        type="text" 
                        name="family_name" 
                        placeholder="Nama Keluarga" 
                        value="{{ old('family_name') }}" 
                        required
                        style="flex:1;background:transparent;border:none;outline:none;font-size:0.95rem;color:#334155;font-family:inherit;"
                    >
                </div>

                {{-- Tombol Kirim / Buat Group (Radius 16px) --}}
                <button
                    type="submit"
                    style="
                        width: 100%;
                        background: #10B981;
                        color: white;
                        font-weight: 700;
                        font-size: 1rem;
                        padding: 18px;
                        border-radius: 16px; {{-- Mempertahankan kelengkungan sudut kotak 16px --}}
                        border: none;
                        cursor: pointer;
                        font-family: inherit;
                        transition: background 0.2s;
                    "
                    onmouseover="this.style.background='#059669'"
                    onmouseout="this.style.background='#10B981'"
                >
                    Buat Group
                </button>
            </form>

        </div>
    </div>

</body>
</html>