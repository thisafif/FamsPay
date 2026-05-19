<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FamsPay - Ajak Keluarga Gabung</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; }
    </style>
</head>
<body style="min-height:100vh; display:flex; overflow:hidden; background-color: #f8fafc;">

    {{-- KIRI: Banner Ilustrasi (Tetap 35% sesuai kode aslimu) --}}
    <div style="width: 35%; min-height: 100vh; position: relative; overflow: hidden; background: #10B981;">
        <img src="{{ asset('images/invite-family-illustration.png') }}" alt="" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; pointer-events: none;">
    </div>

    {{-- KANAN: Konten Utama --}}
    <div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px; background: white;">
        <div style="width: 100%; max-width: 460px; text-align: center;">

            <div style="margin-bottom: 4px; display: flex; justify-content: center;">
                <img src="{{ asset('images/invite-book-logo.png') }}" alt="Invite" style="width: 200px; height: auto;">
            </div>

            <h1 style="font-size: 2rem; font-weight: 800; color: #1e293b; margin: 0 0 0px;">Ajak Keluarga Gabung.</h1>
            <p style="font-size: 0.95rem; color: #64748b; margin: 0 0 32px; line-height: 1.5;">
                Pilih cara yang paling mudah untuk membagikan akses grup <strong style="color:#1e293b;">"{{ $familyName }}"</strong>.
            </p>

            <span style="font-size: 0.85rem; color: #64748b; display: block; margin-bottom: 8px; font-weight: 600;">Kode Bergabung</span>
            
            {{-- Kotak Kode Bergabung --}}
            <div style="display: flex; align-items: center; justify-content: center; gap: 16px; border: 2px dashed #94a3b8; border-radius: 16px; padding: 16px; margin-bottom: 24px; background: #f8fafc;">
                <span id="joinCodeText" style="font-size: 1.8rem; font-weight: 800; letter-spacing: 6px; color: #1e293b;">{{ $joinCode }}</span>
                <button onclick="copyText('joinCodeText')" style="background: none; border: none; cursor: pointer; color: #1e293b; display: flex; align-items: center;">
                    <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3a1 1 0 011-1h10a1 1 0 011 1v10a1 1 0 01-1 1h-4M3 7h10a1 1 0 011 1v10a1 1 0 01-1 1H3a1 1 0 01-1-1V8a1 1 0 011-1z"/></svg>
                </button>
            </div>

            <div style="display: flex; align-items: center; margin-bottom: 24px;">
                <div style="flex: 1; height: 1px; background: #e2e8f0;"></div>
                <span style="padding: 0 16px; color: #94a3b8; font-size: 0.85rem; font-weight: 600;">atau</span>
                <div style="flex: 1; height: 1px; background: #e2e8f0;"></div>
            </div>

            {{-- Opsi Direct Link --}}
            @php $directLink = route('family.join_direct', $joinCode); @endphp
            <div style="display: flex; align-items: center; gap: 12px; background: #E6F4EA; border-radius: 16px; padding: 14px 16px; margin-bottom: 16px; text-align: left;">
                <div style="background: #10B981; padding: 8px; border-radius: 10px; color: white; display: flex; align-items: center;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <span style="font-size: 0.75rem; color: #137333; font-weight: 700; display: block;">Direct Link</span>
                    <span id="directLinkText" style="font-size: 0.85rem; color: #1e293b; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $directLink }}</span>
                </div>
                <button onclick="copyText('directLinkText')" style="background: none; border: none; cursor: pointer; color: #137333;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3a1 1 0 011-1h10a1 1 0 011 1v10a1 1 0 01-1 1h-4M3 7h10a1 1 0 011 1v10a1 1 0 01-1 1H3a1 1 0 01-1-1V8a1 1 0 011-1z"/></svg>
                </button>
            </div>

            {{-- Baris Tombol Sosial Media (Dinamis sesuai nama grup) --}}
            <div style="display: flex; gap: 12px; margin-bottom: 32px;">
                <a href="https://wa.me/?text=Yuk%20gabung%20ke%20grup%20FamsPay%20%22{{ urlencode($familyName) }}%22%20menggunakan%20link%20ini:%20{{ urlencode($directLink) }}" target="_blank" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; border: 1px solid #cbd5e1; padding: 12px; border-radius: 12px; color: #334155; text-decoration: none; font-size: 0.9rem; font-weight: 600;">
                    WhatsApp
                </a>
                <button onclick="shareLink()" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; border: 1px solid #cbd5e1; padding: 12px; border-radius: 12px; color: #334155; background: white; font-size: 0.9rem; font-weight: 600; cursor: pointer;">
                    Lainnya
                </button>
            </div>

            <a href="{{ route('dashboard') }}" style="display: block; width: 100%; background: #10B981; color: white; font-weight: 700; font-size: 1rem; padding: 18px; border-radius: 16px; text-align: center; text-decoration: none; box-sizing: border-box;">
                Selanjutnya
            </a>

        </div>
    </div>

    <script>
        function copyText(elementId) {
            var text = document.getElementById(elementId).innerText;
            navigator.clipboard.writeText(text);
            alert("Salin berhasil!");
        }
        function shareLink() {
            if (navigator.share) {
                navigator.share({ 
                    title: 'FamsPay Invite', 
                    text: 'Yuk gabung ke grup FamsPay "{{ $familyName }}"',
                    url: '{{ $directLink }}' 
                });
            } else {
                copyText('directLinkText');
            }
        }
    </script>
</body>
</html>