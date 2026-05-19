<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'FamsPay' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        #app-sidebar {
            background: linear-gradient(180deg, #A7F3D0 0%, #6EE7B7 25%, #34D399 60%, #10B981 100%);
            overflow: hidden;
        }

        .sidebar-circle-lg {
            position: absolute; bottom: -80px; left: -70px;
            width: 220px; height: 220px;
            background: rgba(255,255,255,0.18); border-radius: 50%;
            pointer-events: none;
        }
        .sidebar-circle-sm {
            position: absolute; bottom: 60px; right: -20px;
            width: 90px; height: 90px;
            background: rgba(255,255,255,0.15); border-radius: 50%;
            pointer-events: none;
        }
        .sidebar-circle-xs {
            position: absolute; bottom: 130px; right: 40px;
            width: 40px; height: 40px;
            background: rgba(255,255,255,0.12); border-radius: 50%;
            pointer-events: none;
        }

        .nav-item { color: #065F46; }
        .nav-item.active {
            background-color: #059669;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(5,150,105,0.35);
        }
        .nav-item:not(.active):hover {
            background-color: rgba(255,255,255,0.30);
            color: #022c22;
        }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #d1fae5; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased">
    <div class="flex min-h-screen">

        <aside id="app-sidebar" class="fixed top-0 left-0 h-screen w-64 z-30 flex flex-col shadow-xl relative">
            <div class="sidebar-circle-lg"></div>
            <div class="sidebar-circle-sm"></div>
            <div class="sidebar-circle-xs"></div>

            {{-- Brand --}}
            <div class="px-6 py-6 flex items-center gap-3 relative z-10">
                <div class="w-9 h-9 flex-shrink-0">
                    <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="40" height="40" rx="10" fill="rgba(255,255,255,0.3)"/>
                        <path d="M22 8C18.686 8 16 10.686 16 14V19L8 27V32H32V14C32 10.686 29.314 8 26 8H22Z" fill="#34D399"/>
                        <path d="M16 19L8 27V32H26V20C26 17.791 24.209 16 22 16H18C16.895 16 16 16.895 16 18V19Z" fill="#065F46"/>
                        <circle cx="24" cy="14" r="2" fill="white" opacity="0.9"/>
                    </svg>
                </div>
                <span class="text-xl font-extrabold text-emerald-900 tracking-tight">Famspay</span>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-4 py-2 space-y-1 relative z-10">
                <a href="#" class="nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                <a href="#" class="nav-item active flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Kelola Anggota
                </a>
                <a href="#" class="nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Transaksi
                </a>
                <a href="#" class="nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Goals
                </a>
                <a href="#" class="nav-item flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Pengaturan
                </a>
            </nav>

            {{-- User Profile --}}
            <div class="px-4 py-5 relative z-10">
                <div class="flex items-center gap-3 px-2">
                    <div class="w-9 h-9 rounded-full bg-white/30 flex items-center justify-center text-emerald-900 font-bold text-sm flex-shrink-0 border border-white/40">
                        U
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-emerald-900 truncate">User</p>
                        <p class="text-xs text-emerald-700 truncate">user@famspay.id</p>
                    </div>
                    <button class="text-emerald-700 hover:text-rose-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col ml-64">
            <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-slate-100 px-8 py-4 flex justify-between items-center">
                <div>
                    <h1 class="text-lg font-bold text-slate-800">{{ $pageTitle ?? 'Dashboard' }}</h1>
                    <p class="text-xs text-slate-400">{{ $pageSubtitle ?? 'Kelola keuangan keluarga Anda' }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <button class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-emerald-50 flex items-center justify-center text-slate-500 hover:text-emerald-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                    <button class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-emerald-50 flex items-center justify-center text-slate-500 hover:text-emerald-600 transition-colors relative">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full"></span>
                    </button>
                </div>
            </header>

            <main class="flex-1 px-8 py-7">
                {{ $slot }}
            </main>

            <footer class="px-8 py-4 border-t border-slate-100">
                <p class="text-slate-400 text-xs">© 2026 FamsPay • Smart Family Finance</p>
            </footer>
        </div>
    </div>
</body>
</html>
