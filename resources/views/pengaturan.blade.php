<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan — FamsPay</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        html, body { height: 100%; margin: 0; }
        #app-sidebar {
            background: linear-gradient(180deg, #A7F3D0 0%, #6EE7B7 25%, #34D399 60%, #10B981 100%);
            position: fixed; top: 0; left: 0; width: 200px; height: 100vh;
            display: flex; flex-direction: column; overflow: hidden; z-index: 30;
            box-shadow: 4px 0 20px rgba(0,0,0,.08);
        }
        .sidebar-circle-lg { position: absolute; bottom: -80px; left: -70px; width: 220px; height: 220px; background: rgba(255,255,255,0.18); border-radius: 50%; pointer-events: none; }
        .sidebar-circle-sm { position: absolute; bottom: 60px; right: -20px; width: 90px; height: 90px; background: rgba(255,255,255,0.15); border-radius: 50%; pointer-events: none; }
        .sidebar-circle-xs { position: absolute; bottom: 130px; right: 40px; width: 40px; height: 40px; background: rgba(255,255,255,0.12); border-radius: 50%; pointer-events: none; }
        .nav-item { color: #065F46; transition: all .15s; }
        .nav-item.active { background-color: #059669; color: #fff; box-shadow: 0 4px 12px rgba(5,150,105,.35); }
        .nav-item:not(.active):hover { background-color: rgba(255,255,255,.30); color: #022c22; }
        #main-wrapper { margin-left: 200px; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #d1fae5; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased">

@php
    $user     = session('user');
    $isAdmin  = ($user['role'] ?? 'member') === 'admin';
    $initials = strtoupper(substr($user['full_name'] ?? 'U', 0, 1));
    $fullName = $user['full_name'] ?? '';
    $nameParts = explode(' ', $fullName, 2);
    $firstName = $nameParts[0] ?? '';
    $lastName  = $nameParts[1] ?? '';
@endphp

<aside id="app-sidebar">
    <div class="sidebar-circle-lg"></div>
    <div class="sidebar-circle-sm"></div>
    <div class="sidebar-circle-xs"></div>
    <div class="px-4 py-5 relative z-10 flex items-center gap-2.5">
        <svg width="32" height="32" viewBox="0 0 64 64" fill="none"><path d="M3.18726 29.2301C2.82289 28.5724 1.99769 26.8745 2.17452 24.6019C2.37546 22.0003 3.76061 20.2945 4.23751 19.757C8.91273 16.0272 13.5853 12.2973 18.2605 8.56743C18.8848 8.30006 20.5619 7.6851 22.6919 8.08081C24.7763 8.4685 26.0999 9.60752 26.5928 10.0781C30.25 14.1048 33.9071 18.1314 37.5642 22.1581C41.1892 26.0403 44.8115 29.9226 48.4364 33.8049C49.1813 34.214 50.3414 34.73 51.839 34.9359C54.261 35.2701 56.1901 34.607 57.093 34.2273C54.5531 36.1899 52.6026 37.9652 51.2067 39.1336C44.8865 44.4277 38.668 49.7243 32.546 55.0183C32.313 55.2109 30.1455 56.9381 27.3591 56.2349C25.2264 55.6975 24.1387 54.0906 23.8922 53.7055C16.9906 45.5453 10.0889 37.385 3.18726 29.2248V29.2301Z" fill="#03EC65"/><path opacity="0.58" d="M35.9406 51.8687C35.8334 50.465 35.5468 48.3714 34.6974 45.9918C33.5936 42.8956 32.0959 40.8796 29.6284 37.6043C28.2245 35.7407 26.2365 33.2808 23.627 30.5483C23.2358 30.0483 22.2793 28.682 22.1587 26.6874C22.0141 24.3158 23.1474 22.6367 23.493 22.1607C28.1629 17.9442 32.83 13.7304 37.4999 9.51394C38.0518 9.07277 40.3908 7.31078 43.7692 7.51666C46.7137 7.6958 48.6481 9.26261 49.2375 9.77864L58.9764 20.0298C59.314 20.3372 62.1352 22.9949 61.7789 27.0858C61.4708 30.6178 59.0166 32.6445 58.5772 32.9947C51.0326 39.2861 43.4879 45.5747 35.9433 51.866L35.9406 51.8687Z" fill="#038599"/><circle cx="9.01722" cy="25.5885" r="3.48" fill="white"/></svg>
        <span class="text-lg font-extrabold text-emerald-900 tracking-tight">Famspay</span>
    </div>
    <nav class="flex-1 px-3 py-2 space-y-0.5 relative z-10 overflow-y-auto">
        <a href="{{ route('dashboard') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-2xl text-sm font-semibold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
        @if($isAdmin)
        <a href="{{ route('anggota') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-2xl text-sm font-semibold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Kelola Anggota
        </a>
        @endif
        <a href="{{ route('transaksi') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-2xl text-sm font-semibold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            Transaksi
        </a>
        <a href="{{ route('goals') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-2xl text-sm font-semibold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Goals
        </a>
        <a href="{{ route('pengaturan') }}" class="nav-item active flex items-center gap-3 px-3 py-2.5 rounded-2xl text-sm font-semibold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Pengaturan
        </a>
    </nav>
    <div class="px-3 py-4 relative z-10 flex-shrink-0 border-t border-white/20">
        <div class="flex items-center gap-2.5 px-2">
            <div class="w-8 h-8 rounded-full bg-white/30 flex items-center justify-center text-emerald-900 font-bold text-xs flex-shrink-0 border border-white/40">{{ $initials }}</div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-emerald-900 truncate">{{ $user['full_name'] ?? 'Pengguna' }}</p>
                <p class="text-[10px] text-emerald-700 truncate">{{ $user['email'] ?? '' }}</p>
            </div>
            <form method="POST" action="/logout" class="inline">@csrf
                <button type="submit" class="text-emerald-700 hover:text-rose-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>

<div id="main-wrapper" class="flex-1 flex flex-col min-h-screen">
    <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-slate-100 px-7 py-4 flex justify-between items-center">
        <div>
            <h1 class="text-base font-bold text-slate-800">Pengaturan</h1>
            <p class="text-xs text-slate-400">Personalisasi pengalaman Anda.</p>
        </div>
        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5">
            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center text-white font-bold text-xs">{{ $initials }}</div>
            <div>
                <p class="text-xs font-semibold text-slate-700 leading-tight">{{ $user['full_name'] ?? 'Pengguna' }}</p>
                <p class="text-[10px] text-slate-400 leading-tight">{{ $user['email'] ?? '' }}</p>
            </div>
        </div>
    </header>

    <main class="flex-1 px-7 py-6">
        <div class="bg-white rounded-2xl border border-slate-100 p-6 max-w-2xl">

            {{-- Alert --}}
            <div id="alert-success" class="hidden mb-5 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 text-xs text-emerald-700 font-medium flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Perubahan berhasil disimpan.
            </div>
            <div id="alert-error" class="hidden mb-5 bg-rose-50 border border-rose-200 rounded-xl px-4 py-3 text-xs text-rose-600 font-medium"></div>

            {{-- Profile section label --}}
            <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-4">Profil Saya</p>

            {{-- Avatar --}}
            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-100">
                <div class="relative">
                    <div id="avatar-preview" class="w-16 h-16 rounded-full bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center text-white font-bold text-xl overflow-hidden">
                        @if(!empty($user['avatar_url']))
                            <img src="{{ $user['avatar_url'] }}" alt="avatar" class="w-full h-full object-cover">
                        @else
                            {{ $initials }}
                        @endif
                    </div>
                    <label for="avatar-input" class="absolute -bottom-1 -right-1 w-6 h-6 bg-emerald-500 hover:bg-emerald-600 rounded-full flex items-center justify-center cursor-pointer transition-colors shadow-sm">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </label>
                    <input id="avatar-input" type="file" accept="image/*" class="hidden" onchange="previewAvatar(event)">
                </div>
                <div>
                    <p class="font-semibold text-slate-800 text-sm">{{ $user['full_name'] ?? 'Pengguna' }}</p>
                    <p class="text-xs text-slate-400">{{ $user['email'] ?? '' }}</p>
                    <span class="inline-block mt-1 bg-slate-100 text-slate-500 text-[10px] font-semibold px-2 py-0.5 rounded-full">{{ ucfirst($user['role'] ?? 'member') }}</span>
                </div>
            </div>

            {{-- Form fields --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Nama Depan</label>
                    <input id="field-firstname" type="text" value="{{ $firstName }}" placeholder="Siti" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 bg-slate-50">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Nama Belakang</label>
                    <input id="field-lastname" type="text" value="{{ $lastName }}" placeholder="Nayla" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 bg-slate-50">
                </div>
            </div>
            <div class="mb-4">
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Email</label>
                <input id="field-email" type="email" value="{{ $user['email'] ?? '' }}" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 bg-slate-50">
            </div>
            <div class="mb-6">
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Password Baru <span class="text-slate-400 font-normal">(kosongkan jika tidak ingin mengubah)</span></label>
                <input id="field-password" type="password" placeholder="••••••••" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 bg-slate-50">
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3">
                <form method="POST" action="/logout" class="inline">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-rose-500 hover:bg-rose-600 transition-colors">
                        Keluar dari Akun
                    </button>
                </form>
                <button onclick="saveProfile()" id="btn-save" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-emerald-500 hover:bg-emerald-600 transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </main>
</div>

<script>
const TOKEN   = '{{ session("auth_token") }}';
const HEADERS = { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + TOKEN, 'Accept': 'application/json' };

let newAvatarUrl = null;

function previewAvatar(event) {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('avatar-preview');
        preview.innerHTML = `<img src="${e.target.result}" alt="avatar" class="w-full h-full object-cover rounded-full">`;
        // Store as data URL — BE accepts avatar_url as a URL string; in a real app you'd upload to storage first.
        // For now we store it as a data URL which will be sent to BE.
        newAvatarUrl = e.target.result;
    };
    reader.readAsDataURL(file);
}

async function saveProfile() {
    const firstName = document.getElementById('field-firstname').value.trim();
    const lastName  = document.getElementById('field-lastname').value.trim();
    const email     = document.getElementById('field-email').value.trim();
    const password  = document.getElementById('field-password').value;
    const errEl     = document.getElementById('alert-error');
    const okEl      = document.getElementById('alert-success');
    errEl.classList.add('hidden'); okEl.classList.add('hidden');

    if (!firstName) { errEl.textContent = 'Nama depan wajib diisi.'; errEl.classList.remove('hidden'); return; }
    if (!email) { errEl.textContent = 'Email wajib diisi.'; errEl.classList.remove('hidden'); return; }

    const payload = { full_name: (firstName + ' ' + lastName).trim(), email };
    if (newAvatarUrl) payload.avatar_url = newAvatarUrl;
    if (password) payload.password = password;

    const btn = document.getElementById('btn-save');
    btn.textContent = 'Menyimpan...'; btn.disabled = true;

    try {
        const res  = await fetch('/api/v1/auth/me/update', { method: 'PUT', headers: HEADERS, body: JSON.stringify(payload) });
        const data = await res.json();

        if (!res.ok) {
            const msgs = data.errors ? Object.values(data.errors).flat().join(', ') : (data.message || 'Gagal menyimpan.');
            errEl.textContent = msgs; errEl.classList.remove('hidden');
            btn.textContent = 'Simpan Perubahan'; btn.disabled = false;
            return;
        }

        // Update session di server via endpoint khusus (pakai route web agar session bisa di-update)
        await fetch('/profile/update-session', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' },
            body: JSON.stringify({ full_name: payload.full_name, email: payload.email, avatar_url: payload.avatar_url || null })
        }).catch(() => {});

        okEl.classList.remove('hidden');
        document.getElementById('field-password').value = '';
        newAvatarUrl = null;
        btn.textContent = 'Simpan Perubahan'; btn.disabled = false;

        // Scroll to top to show success
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch(e) {
        errEl.textContent = 'Koneksi gagal. Coba lagi.'; errEl.classList.remove('hidden');
        btn.textContent = 'Simpan Perubahan'; btn.disabled = false;
    }
}
</script>
</body>
</html>
