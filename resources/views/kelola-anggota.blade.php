<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Anggota — FamsPay</title>
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

        /* Wallet card */
        .wallet-physical {
            border-radius: 18px; position: relative; overflow: visible;
            aspect-ratio: 5 / 3; width: 100%;
        }
        .wallet-physical .card-stack-1, .wallet-physical .card-stack-2 {
            position: absolute; left: 12px; right: 12px; height: 14px;
            border-radius: 12px 12px 0 0; z-index: 0;
        }
        .wallet-physical .card-stack-1 { top: -10px; }
        .wallet-physical .card-stack-2 { top: -5px; }
        .wallet-physical .wallet-body {
            position: absolute; inset: 0; z-index: 1; border-radius: 18px;
            padding: 16px 20px 14px; display: flex; flex-direction: column; justify-content: space-between;
        }
        .wallet-body .wallet-slot {
            background: rgba(0,0,0,0.15); border-radius: 8px; height: 26px; flex: 1;
            display: flex; align-items: center; padding: 0 10px;
        }
        .dompet-darkred .card-stack-1 { background: #7f1d1d; }
        .dompet-darkred .card-stack-2 { background: #991b1b; }
        .dompet-darkred .wallet-body  { background: linear-gradient(145deg, #059669 0%, #10B981 100%); }
        .dompet-purple .card-stack-1 { background: #6b21a8; }
        .dompet-purple .card-stack-2 { background: #7e22ce; }
        .dompet-purple .wallet-body  { background: linear-gradient(145deg, #059669 0%, #10B981 100%); }
        .dompet-blue .card-stack-1 { background: #1d4ed8; }
        .dompet-blue .card-stack-2 { background: #2563eb; }
        .dompet-blue .wallet-body  { background: linear-gradient(145deg, #059669 0%, #10B981 100%); }
        .dompet-lime .card-stack-1 { background: #4d7c0f; }
        .dompet-lime .card-stack-2 { background: #65a30d; }
        .dompet-lime .wallet-body  { background: linear-gradient(145deg, #059669 0%, #10B981 100%); }
        .dompet-teal .card-stack-1 { background: #0f766e; }
        .dompet-teal .card-stack-2 { background: #0d9488; }
        .dompet-teal .wallet-body  { background: linear-gradient(145deg, #059669 0%, #10B981 100%); }
        .dompet-rose .card-stack-1 { background: #9f1239; }
        .dompet-rose .card-stack-2 { background: #be123c; }
        .dompet-rose .wallet-body  { background: linear-gradient(145deg, #059669 0%, #10B981 100%); }

        /* Modal */
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(15,23,42,.5);
            backdrop-filter: blur(4px); z-index: 9000;
            display: flex; align-items: center; justify-content: center;
            opacity: 0; pointer-events: none; transition: opacity .2s ease;
        }
        .modal-overlay.open { opacity: 1; pointer-events: all; }
        .modal-box {
            background: #fff; border-radius: 20px; padding: 28px; width: 420px; max-width: 90vw;
            transform: translateY(16px) scale(.97); transition: transform .25s cubic-bezier(.4,0,.2,1);
            box-shadow: 0 24px 64px rgba(0,0,0,.18);
        }
        .modal-overlay.open .modal-box { transform: translateY(0) scale(1); }
        .detail-panel { display: none; }
        .detail-panel.open { display: block; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased">

@php
    $user     = session('user');
    $isAdmin  = ($user['role'] ?? 'member') === 'admin';
    $initials = strtoupper(substr($user['full_name'] ?? 'U', 0, 1));
    if (!$isAdmin) { return redirect()->route('dashboard'); }
@endphp

{{-- SIDEBAR --}}
<aside id="app-sidebar">
    <div class="sidebar-circle-lg"></div>
    <div class="sidebar-circle-sm"></div>
    <div class="sidebar-circle-xs"></div>
    <div class="px-4 py-5 relative z-10 flex items-center gap-2.5">
        <svg width="32" height="32" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3.18726 29.2301C2.82289 28.5724 1.99769 26.8745 2.17452 24.6019C2.37546 22.0003 3.76061 20.2945 4.23751 19.757C8.91273 16.0272 13.5853 12.2973 18.2605 8.56743C18.8848 8.30006 20.5619 7.6851 22.6919 8.08081C24.7763 8.4685 26.0999 9.60752 26.5928 10.0781C30.25 14.1048 33.9071 18.1314 37.5642 22.1581C41.1892 26.0403 44.8115 29.9226 48.4364 33.8049C49.1813 34.214 50.3414 34.73 51.839 34.9359C54.261 35.2701 56.1901 34.607 57.093 34.2273C54.5531 36.1899 52.6026 37.9652 51.2067 39.1336C44.8865 44.4277 38.668 49.7243 32.546 55.0183C32.313 55.2109 30.1455 56.9381 27.3591 56.2349C25.2264 55.6975 24.1387 54.0906 23.8922 53.7055C16.9906 45.5453 10.0889 37.385 3.18726 29.2248V29.2301Z" fill="#03EC65"/>
            <path opacity="0.58" d="M35.9406 51.8687C35.8334 50.465 35.5468 48.3714 34.6974 45.9918C33.5936 42.8956 32.0959 40.8796 29.6284 37.6043C28.2245 35.7407 26.2365 33.2808 23.627 30.5483C23.2358 30.0483 22.2793 28.682 22.1587 26.6874C22.0141 24.3158 23.1474 22.6367 23.493 22.1607C28.1629 17.9442 32.83 13.7304 37.4999 9.51394C38.0518 9.07277 40.3908 7.31078 43.7692 7.51666C46.7137 7.6958 48.6481 9.26261 49.2375 9.77864L58.9764 20.0298C59.314 20.3372 62.1352 22.9949 61.7789 27.0858C61.4708 30.6178 59.0166 32.6445 58.5772 32.9947C51.0326 39.2861 43.4879 45.5747 35.9433 51.866L35.9406 51.8687Z" fill="#038599"/>
            <circle cx="9.01722" cy="25.5885" r="3.48" fill="white"/>
        </svg>
        <span class="text-lg font-extrabold text-emerald-900 tracking-tight">Famspay</span>
    </div>
    <nav class="flex-1 px-3 py-2 space-y-0.5 relative z-10 overflow-y-auto">
        <a href="{{ route('dashboard') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-2xl text-sm font-semibold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
        <a href="{{ route('anggota') }}" class="nav-item active flex items-center gap-3 px-3 py-2.5 rounded-2xl text-sm font-semibold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Kelola Anggota
        </a>
        <a href="{{ route('transaksi') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-2xl text-sm font-semibold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            Transaksi
        </a>
        <a href="{{ route('goals') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-2xl text-sm font-semibold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Goals
        </a>
        <a href="{{ route('pengaturan') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-2xl text-sm font-semibold">
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
                <button type="submit" class="text-emerald-700 hover:text-rose-600 transition-colors" title="Keluar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- MAIN --}}
<div id="main-wrapper" class="flex-1 flex flex-col min-h-screen">
    <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-slate-100 px-7 py-4 flex justify-between items-center">
        <div>
            <h1 class="text-base font-bold text-slate-800" id="page-title">Kelola Anggota Keluarga</h1>
            <p class="text-xs text-slate-400" id="page-sub">Atur hak akses, limit pengeluaran, dan undang anggota baru.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="showInviteModal()" class="flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors shadow-sm shadow-emerald-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Undang Anggota
            </button>
            <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5">
                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center text-white font-bold text-xs">{{ $initials }}</div>
                <div>
                    <p class="text-xs font-semibold text-slate-700 leading-tight">{{ $user['full_name'] ?? 'Pengguna' }}</p>
                    <p class="text-[10px] text-slate-400 leading-tight">{{ $user['email'] ?? '' }}</p>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 px-7 py-6 space-y-5">
        {{-- Join Code Banner --}}
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-5 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-white text-sm mb-1">Kode Bergabung Keluarga (Join Code)</h3>
                <p class="text-white/70 text-xs">Bagikan kode ini kepada anggota keluarga agar mereka bisa bergabung ke grup.</p>
            </div>
            <div id="join-code-display" class="flex items-center gap-2">
                <div class="flex gap-1.5" id="code-chars"></div>
                <button onclick="copyJoinCode()" id="copy-btn" class="w-9 h-9 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center transition-colors" title="Salin kode">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </button>
            </div>
        </div>

        {{-- List View --}}
        <div id="panel-list">
            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <h3 class="font-bold text-slate-700 text-sm mb-5">Daftar Anggota & Pengaturan Limit</h3>
                <div id="members-grid" class="grid grid-cols-2 gap-6">
                    <div class="col-span-2 py-12 text-center text-slate-400 text-sm">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-10 h-10 bg-slate-100 rounded-2xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <span>Memuat anggota...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Panel --}}
        <div id="panel-detail" class="detail-panel">
            <div class="bg-white rounded-2xl border border-slate-100 p-6">
                <div class="flex items-center gap-3 mb-6">
                    <button onclick="backToList()" class="w-9 h-9 bg-slate-100 hover:bg-slate-200 rounded-xl flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </button>
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Profil Keuangan Anggota</h2>
                        <p class="text-xs text-slate-400">Kembali ke daftar anggota</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    {{-- Left: Wallet card --}}
                    <div>
                        <div id="detail-wallet-card" class="wallet-physical dompet-blue mt-3">
                            <div class="card-stack-1"></div>
                            <div class="card-stack-2"></div>
                            <div class="wallet-body text-white" style="padding:16px 20px 14px;">
                                <div style="position:absolute;top:14px;right:14px;width:28px;height:28px;background:rgba(255,255,255,0.25);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H7M17 7v10"/></svg>
                                </div>
                                <div class="flex items-center gap-2 mb-2">
                                    <div id="detail-avatar" class="w-9 h-9 rounded-full bg-white/30 flex items-center justify-center text-white font-bold text-sm"></div>
                                    <div>
                                        <p class="text-white/60 text-[9px] uppercase tracking-wider">Dompet Milik:</p>
                                        <p id="detail-name" class="font-extrabold text-sm text-white pr-8"></p>
                                    </div>
                                </div>
                                <span id="detail-role-badge" class="inline-block bg-white/20 text-white text-[10px] font-semibold px-2 py-0.5 rounded-full mb-3">Member</span>
                                <div class="flex gap-2">
                                    <div class="wallet-slot flex-1"><span id="detail-limit" class="text-white text-[11px] font-bold"></span></div>
                                    <div class="wallet-slot flex-1"><span id="detail-balance" class="text-white text-[11px] font-bold"></span></div>
                                </div>
                            </div>
                        </div>

                        {{-- Limit usage --}}
                        <div class="mt-5 bg-slate-50 rounded-2xl p-4">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-xs font-semibold text-slate-600">Pemakaian Limit</p>
                                <p id="detail-limit-pct" class="text-xs font-bold text-emerald-600">0%</p>
                            </div>
                            <div class="h-2 bg-slate-200 rounded-full overflow-hidden mb-2">
                                <div id="detail-limit-bar" class="h-full bg-emerald-500 rounded-full transition-all duration-700" style="width:0%"></div>
                            </div>
                            <div class="flex justify-between text-[10px] text-slate-400">
                                <span id="detail-used">Rp 0</span>
                                <span id="detail-limit-max">Rp 0</span>
                            </div>
                            <button onclick="openLimitModal()" class="mt-3 w-full py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold rounded-xl transition-colors">
                                Ubah Limit
                            </button>
                        </div>
                    </div>

                    {{-- Right: Transactions + Budget --}}
                    <div class="space-y-5">
                        <div class="bg-white rounded-2xl border border-slate-100 p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-slate-700 text-xs">Transaksi Terbaru</h4>
                            </div>
                            <div class="overflow-hidden rounded-xl border border-slate-100">
                                <table class="w-full text-xs">
                                    <thead>
                                        <tr class="bg-emerald-50">
                                            <th class="text-left text-emerald-700 font-semibold px-3 py-2">Pembayaran</th>
                                            <th class="text-left text-emerald-700 font-semibold px-2 py-2">Kategori</th>
                                            <th class="text-right text-emerald-700 font-semibold px-3 py-2">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detail-txns" class="divide-y divide-slate-50">
                                        <tr><td colspan="3" class="px-3 py-4 text-center text-slate-400">Memuat...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Money Flow mini chart --}}
                        <div class="bg-white rounded-2xl border border-slate-100 p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-slate-700 text-xs">Money Flow</h4>
                                <div class="flex items-center gap-3 text-[10px] text-slate-400">
                                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>Pemasukan</span>
                                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-200 inline-block"></span>Pengeluaran</span>
                                </div>
                            </div>
                            <div class="flex items-end gap-1 h-20" id="detail-flow-chart">
                                <div class="flex-1 flex items-end justify-center gap-0.5 h-full" id="detail-bars"></div>
                            </div>
                            <div class="flex justify-between mt-1" id="detail-day-labels"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

{{-- Modal Set Limit --}}
<div id="modal-limit" class="modal-overlay" onclick="if(event.target===this)closeLimitModal()">
    <div class="modal-box" style="width:360px">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base font-bold text-slate-800">Ubah Limit Bulanan</h2>
            <button onclick="closeLimitModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <p id="modal-limit-member" class="text-xs text-slate-500 mb-4"></p>
        <div id="modal-limit-error" class="hidden mb-4 bg-rose-50 border border-rose-200 rounded-xl px-4 py-3 text-xs text-rose-600"></div>
        <div class="space-y-4">
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Limit Bulanan (Rp)</label>
                <input id="limit-amount" type="number" min="1" placeholder="5000000" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Periode (YYYY-MM)</label>
                <input id="limit-period" type="month" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
            </div>
        </div>
        <div class="flex gap-3 mt-6">
            <button onclick="closeLimitModal()" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">Batal</button>
            <button onclick="submitLimit()" id="btn-save-limit" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white bg-emerald-500 hover:bg-emerald-600 transition-colors">Simpan</button>
        </div>
    </div>
</div>

{{-- Modal Invite --}}
<div id="modal-invite" class="modal-overlay" onclick="if(event.target===this)document.getElementById('modal-invite').classList.remove('open')">
    <div class="modal-box" style="width:380px">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base font-bold text-slate-800">Undang Anggota</h2>
            <button onclick="document.getElementById('modal-invite').classList.remove('open')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="bg-emerald-50 rounded-2xl p-5 text-center mb-5">
            <p class="text-xs text-slate-500 mb-3">Bagikan kode ini kepada anggota keluarga:</p>
            <div class="flex justify-center gap-2 mb-3" id="invite-code-chars"></div>
            <button onclick="copyJoinCode()" class="text-xs text-emerald-600 font-semibold hover:text-emerald-700">Salin Kode</button>
        </div>
        <p class="text-xs text-slate-400 text-center">Anggota bisa bergabung melalui halaman <strong>Join Family</strong> dan memasukkan kode ini.</p>
    </div>
</div>

<script>
const TOKEN   = '{{ session("auth_token") }}';
const HEADERS = { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + TOKEN, 'Accept': 'application/json' };

const DOMPET_COLORS = ['dompet-darkred','dompet-purple','dompet-blue','dompet-lime','dompet-teal','dompet-rose'];
let members = [];
let currentMember = null;
let joinCode = '';

function fmtRp(n) { return 'Rp' + Math.abs(n || 0).toLocaleString('id-ID'); }
function fmtDate(d) { if(!d) return '-'; return new Date(d).toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric'}); }

// Load family data (join code)
async function loadFamily() {
    try {
        const res  = await fetch('/api/v1/families/me', { headers: HEADERS });
        const data = await res.json();
        joinCode = data.data?.join_code || '';
        renderJoinCode(joinCode, 'code-chars');
        renderJoinCode(joinCode, 'invite-code-chars');
    } catch(e) {}
}

function renderJoinCode(code, targetId) {
    const el = document.getElementById(targetId);
    if (!el || !code) return;
    el.innerHTML = code.split('').map(c =>
        `<div class="w-9 h-11 bg-white/20 rounded-xl flex items-center justify-center text-white font-extrabold text-base">${c}</div>`
    ).join('');
}

function copyJoinCode() {
    if (!joinCode) return;
    navigator.clipboard.writeText(joinCode).then(() => {
        const btn = document.getElementById('copy-btn');
        if (btn) { btn.title = 'Tersalin!'; setTimeout(() => btn.title = 'Salin kode', 2000); }
    });
}

// Load members
async function loadMembers() {
    try {
        const res  = await fetch('/api/v1/families/members', { headers: HEADERS });
        const data = await res.json();
        members = data.data || [];
        renderMembers();
    } catch(e) {
        document.getElementById('members-grid').innerHTML = '<div class="col-span-2 text-center text-rose-400 text-sm py-8">Gagal memuat anggota.</div>';
    }
}

function renderMembers() {
    const grid = document.getElementById('members-grid');
    if (!members.length) {
        grid.innerHTML = '<div class="col-span-2 py-12 text-center text-slate-400 text-sm">Belum ada anggota keluarga.</div>';
        return;
    }

    grid.innerHTML = members.map((m, idx) => {
        const color = DOMPET_COLORS[idx % DOMPET_COLORS.length];
        const limitBase = m.monthly_limit_base || 5000000;
        const usedLimit = m.current_month_expense || 0;
        const pct = limitBase > 0 ? Math.min(Math.round(usedLimit / limitBase * 100), 100) : 0;
        const pctClass = pct >= 90 ? 'text-rose-500 bg-rose-50 border border-rose-200' : pct >= 70 ? 'text-amber-600 bg-amber-50 border border-amber-200' : 'text-emerald-600 bg-emerald-50 border border-emerald-200';

        return `
        <div class="cursor-pointer group">
            <div class="wallet-physical ${color} mt-3" onclick="openDetail(${idx})">
                <div class="card-stack-1"></div>
                <div class="card-stack-2"></div>
                <div class="wallet-body text-white" style="padding:14px 16px 12px;">
                    <div style="position:absolute;top:10px;right:10px;width:26px;height:26px;background:rgba(255,255,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;" class="group-hover:bg-white/30 transition-colors">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H7M17 7v10"/></svg>
                    </div>
                    <p class="text-white/60 text-[8px] uppercase tracking-wider mb-0.5">Dompet Milik:</p>
                    <p class="font-extrabold text-sm text-white pr-8 leading-tight mb-1">${(m.full_name || '-').slice(0,22)}</p>
                    <div class="flex gap-2 mt-1">
                        <div class="wallet-slot" style="height:24px;flex:1;"><span class="text-white text-[10px] font-bold">${fmtRp(limitBase)}</span></div>
                        <div class="wallet-slot" style="height:24px;flex:1;"><span class="text-white text-[10px] font-bold">${fmtRp(m.wallet_balance || 0)}</span></div>
                    </div>
                </div>
            </div>
            <div class="mt-2 flex items-center justify-between px-1">
                <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold ${pctClass}">Limit ${pct}% Terpakai</span>
                <button onclick="openDetail(${idx})" class="text-[10px] text-emerald-600 font-semibold hover:text-emerald-700">Lihat Detail →</button>
            </div>
        </div>`;
    }).join('');
}

// Open detail
async function openDetail(idx) {
    currentMember = members[idx];
    const m = currentMember;

    // Update page header
    document.getElementById('page-title').textContent = 'Profil Keuangan Anggota';
    document.getElementById('page-sub').textContent = 'Kembali ke daftar anggota';

    // Populate wallet card
    document.getElementById('detail-name').textContent = m.full_name || '-';
    document.getElementById('detail-avatar').textContent = (m.full_name || 'A').charAt(0).toUpperCase();
    document.getElementById('detail-role-badge').textContent = m.role === 'admin' ? 'Admin' : 'Member';
    document.getElementById('detail-limit').textContent = fmtRp(m.monthly_limit_base || 5000000);
    document.getElementById('detail-balance').textContent = fmtRp(m.wallet_balance || 0);

    // Limit bar
    const limitBase = m.monthly_limit_base || 5000000;
    const usedLimit = m.current_month_expense || 0;
    const pct = limitBase > 0 ? Math.min(Math.round(usedLimit / limitBase * 100), 100) : 0;
    document.getElementById('detail-limit-pct').textContent = pct + '%';
    document.getElementById('detail-limit-bar').style.width = pct + '%';
    document.getElementById('detail-limit-bar').className = `h-full rounded-full transition-all duration-700 ${pct >= 90 ? 'bg-rose-500' : pct >= 70 ? 'bg-amber-500' : 'bg-emerald-500'}`;
    document.getElementById('detail-used').textContent = fmtRp(usedLimit);
    document.getElementById('detail-limit-max').textContent = fmtRp(limitBase);

    // Dompet card color
    const card = document.getElementById('detail-wallet-card');
    DOMPET_COLORS.forEach(c => card.classList.remove(c));
    card.classList.add(DOMPET_COLORS[idx % DOMPET_COLORS.length]);

    // Switch panel
    document.getElementById('panel-list').style.display = 'none';
    document.getElementById('panel-detail').classList.add('open');

    // Load transactions for this member (admin can see all in family)
    await loadMemberTransactions(m.id);
}

async function loadMemberTransactions(userId) {
    try {
        // Admin calls /api/v1/transactions which returns all family txns, filter by user
        const res  = await fetch('/api/v1/transactions', { headers: HEADERS });
        const data = await res.json();
        const all  = (data.data || []).filter(t => t.user_id === userId || true); // show all, note BE scopes per role
        const recent = all.slice(0, 5);
        const days = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
        const flowData = new Array(7).fill(0).map(() => ({ inc: 0, exp: 0 }));
        all.forEach(t => {
            const d = new Date(t.txn_date); const dow = (d.getDay() + 6) % 7;
            if (t.type === 'income') flowData[dow].inc += t.amount;
            else flowData[dow].exp += t.amount;
        });
        const maxVal = Math.max(...flowData.flatMap(d => [d.inc, d.exp]), 1);

        // Render transactions
        const tbody = document.getElementById('detail-txns');
        if (!recent.length) { tbody.innerHTML = '<tr><td colspan="3" class="px-3 py-4 text-center text-slate-400">Belum ada transaksi</td></tr>'; }
        else {
            tbody.innerHTML = recent.map(t => {
                const isIncome = t.type === 'income';
                return `<tr class="hover:bg-slate-50"><td class="px-3 py-2.5">
                    <p class="font-semibold text-slate-700">${(t.note || t.category_name || '-').slice(0,20)}</p>
                    <p class="text-slate-400 text-[10px]">${fmtDate(t.txn_date)}</p>
                </td>
                <td class="px-2 py-2.5 text-emerald-600">${t.category_name || '-'}</td>
                <td class="px-3 py-2.5 text-right font-bold ${isIncome ? 'text-emerald-600' : 'text-rose-500'}">${isIncome ? '+' : '-'}${fmtRp(t.amount)}</td></tr>`;
            }).join('');
        }

        // Render mini chart
        const barsEl = document.getElementById('detail-bars');
        const labelsEl = document.getElementById('detail-day-labels');
        barsEl.innerHTML = flowData.map((d, i) => {
            const incH = Math.round((d.inc / maxVal) * 64);
            const expH = Math.round((d.exp / maxVal) * 64);
            return `<div class="flex-1 flex items-end gap-0.5 justify-center" style="height:64px;">
                <div class="w-2 bg-emerald-500 rounded-t" style="height:${incH || 2}px"></div>
                <div class="w-2 bg-emerald-200 rounded-t" style="height:${expH || 2}px"></div>
            </div>`;
        }).join('');
        labelsEl.innerHTML = days.map(d => `<span class="flex-1 text-center text-[9px] text-slate-400">${d}</span>`).join('');

    } catch(e) {}
}

function backToList() {
    document.getElementById('panel-list').style.display = '';
    document.getElementById('panel-detail').classList.remove('open');
    document.getElementById('page-title').textContent = 'Kelola Anggota Keluarga';
    document.getElementById('page-sub').textContent = 'Atur hak akses, limit pengeluaran, dan undang anggota baru.';
    currentMember = null;
}

// Limit modal
function openLimitModal() {
    if (!currentMember) return;
    document.getElementById('modal-limit-member').textContent = 'Anggota: ' + (currentMember.full_name || '-');
    document.getElementById('limit-amount').value = currentMember.monthly_limit_base || 5000000;
    // Set current period
    const now = new Date();
    document.getElementById('limit-period').value = now.toISOString().slice(0,7);
    document.getElementById('modal-limit-error').classList.add('hidden');
    document.getElementById('modal-limit').classList.add('open');
}
function closeLimitModal() { document.getElementById('modal-limit').classList.remove('open'); }

async function submitLimit() {
    const amount = parseInt(document.getElementById('limit-amount').value);
    const period = document.getElementById('limit-period').value;
    const errEl  = document.getElementById('modal-limit-error');
    errEl.classList.add('hidden');
    if (!amount || amount < 1) { errEl.textContent = 'Jumlah limit harus lebih dari 0.'; errEl.classList.remove('hidden'); return; }
    if (!period) { errEl.textContent = 'Periode wajib diisi.'; errEl.classList.remove('hidden'); return; }

    const btn = document.getElementById('btn-save-limit');
    btn.textContent = 'Menyimpan...'; btn.disabled = true;

    try {
        const res  = await fetch(`/api/v1/admin/members/${currentMember.id}/monthly-limit`, {
            method: 'PUT', headers: HEADERS,
            body: JSON.stringify({ monthly_limit_base: amount, period_month: period })
        });
        const data = await res.json();
        if (!res.ok) { errEl.textContent = data.message || 'Gagal menyimpan.'; errEl.classList.remove('hidden'); btn.textContent = 'Simpan'; btn.disabled = false; return; }

        // Update local
        currentMember.monthly_limit_base = amount;
        const mi = members.findIndex(m => m.id === currentMember.id);
        if (mi !== -1) members[mi].monthly_limit_base = amount;

        // Refresh detail
        openDetail(mi !== -1 ? mi : 0);
        closeLimitModal();
    } catch(e) { errEl.textContent = 'Koneksi gagal.'; errEl.classList.remove('hidden'); btn.textContent = 'Simpan'; btn.disabled = false; }
}

function showInviteModal() { document.getElementById('modal-invite').classList.add('open'); }

// Init
loadFamily();
loadMembers();
</script>
</body>
</html>
