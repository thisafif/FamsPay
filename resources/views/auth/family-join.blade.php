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

    {{-- KIRI: Banner Ilustrasi (Konsisten 35% sesuai layout aslimu) --}}
    <div style="width: 35%; min-height: 100vh; position: relative; overflow: hidden; background: #10B981;">
        <img src="{{ asset('images/join-family-illustration.png') }}" alt="" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; pointer-events: none;">
    </div>

    {{-- KANAN: Konten Form Utama --}}
    <div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px; background: white;">
        <div style="width: 100%; max-width: 460px; text-align: center;">

            {{-- Ilustrasi Dokumen/Kunci Tengah --}}
            <div style="margin-bottom: 20px; display: flex; justify-content: center;">
                <img src="{{ asset('images/join-family-logo.png') }}" alt="Join" style="width: 160px; height: auto;">
            </div>

            <h1 style="font-size: 2rem; font-weight: 800; color: #1e293b; margin: 0 0 8px;">Halo, Crew!</h1>
            <p style="font-size: 0.95rem; color: #64748b; margin: 0 0 32px; line-height: 1.5;">
                Silahkan masukkan kode untuk bergabung dengan group keluarga anda.
            </p>

            {{-- Alert Notifikasi Jika Kode Salah / Error dari API --}}
            @if(session('error'))
                <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 0.85rem; font-weight: 600; text-align: left;">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Form Pengiriman Kode Bergabung --}}
            <form action="{{ route('family.join.process') }}" method="POST">
                @csrf
                
                <div style="position: relative; margin-bottom: 24px; text-align: left;">
                    <span style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; display: flex; align-items: center;">
                        {{-- Icon Rumah Kecil --}}
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </span>
                    <input type="text" name="join_code" value="{{ old('join_code') }}" placeholder="Isi kode untuk bergabung" required
                           style="width: 100%; padding: 16px 16px 16px 48px; border: 1px solid #e2e8f0; background-color: #f1f5f9; border-radius: 14px; font-size: 0.95rem; font-weight: 500; color: #1e293b; outline: none; box-sizing: border-box; transition: border-color 0.2s;"
                           onfocus="this.style.borderColor='#10B981'; this.style.backgroundColor='white';"
                           onblur="this.style.borderColor='#e2e8f0'; this.style.backgroundColor='#f1f5f9';">
                </div>

                <button type="submit" style="width: 100%; background: #10B981; color: white; border: none; font-weight: 700; font-size: 1rem; padding: 18px; border-radius: 16px; text-align: center; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10B981'">
                    Bergabung
                </button>
            </form>

        </div>
    </div>

</body>
</html>