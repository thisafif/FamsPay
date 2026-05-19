{{--
    Komponen navbar ini berdiri sendiri dan bisa dipakai di luar app layout.
    Untuk app layout, header sudah diintegrasikan langsung di layouts/app.blade.php.
    Komponen ini tetap tersedia untuk keperluan testing atau halaman tanpa sidebar.
--}}

<nav class="bg-white border-b border-slate-100 px-6 py-4 shadow-sm">
    <div class="max-w-7xl mx-auto flex justify-between items-center">

        {{-- Brand --}}
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 flex-shrink-0">
                <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="40" rx="10" fill="#ECFDF5"/>
                    <path d="M22 8C18.686 8 16 10.686 16 14V19L8 27V32H32V14C32 10.686 29.314 8 26 8H22Z" fill="#34D399"/>
                    <path d="M16 19L8 27V32H26V20C26 17.791 24.209 16 22 16H18C16.895 16 16 16.895 16 18V19Z" fill="#059669"/>
                    <circle cx="24" cy="14" r="2" fill="white" opacity="0.8"/>
                </svg>
            </div>
            <span class="text-base font-extrabold text-slate-800 tracking-tight">Famspay</span>
        </div>

        {{-- Nav Links --}}
        <div class="hidden md:flex items-center gap-1">
            <a href="#" class="px-4 py-2 rounded-xl text-sm font-semibold bg-emerald-500 text-white">
                Dashboard
            </a>
            <a href="#" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                Transaksi
            </a>
            <a href="#" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                Goals
            </a>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center text-white font-bold text-xs">
                U
            </div>
        </div>
    </div>
</nav>
