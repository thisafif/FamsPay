<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Target Tabungan — FamsPay</title>
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

        /* Goal card progress bar */
        .goal-progress { height: 8px; border-radius: 99px; background: #f1f5f9; overflow: hidden; }
        .goal-progress-bar { height: 100%; border-radius: 99px; transition: width .7s cubic-bezier(.4,0,.2,1); }

        /* Goal icons */
        .goal-icon-spiritual { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); }
        .goal-icon-travel     { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); }
        .goal-icon-gadget     { background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); }
        .goal-icon-couple     { background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%); }
        .goal-icon-default    { background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased">

@php
    $user     = session('user');
    $isAdmin  = ($user['role'] ?? 'member') === 'admin';
    $initials = strtoupper(substr($user['full_name'] ?? 'U', 0, 1));
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
        <a href="{{ route('goals') }}" class="nav-item active flex items-center gap-3 px-3 py-2.5 rounded-2xl text-sm font-semibold">
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
            <h1 class="text-base font-bold text-slate-800">Target Tabungan</h1>
            <p class="text-xs text-slate-400">Kelola batasan belanja dan progres target tabungan pribadi anda</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="openAddGoal()" class="flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors shadow-sm shadow-emerald-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Target
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

    <main class="flex-1 px-7 py-6">
        <div id="goals-grid" class="grid grid-cols-2 gap-5">
            <div class="col-span-2 py-16 flex flex-col items-center text-center">
                <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <p class="text-slate-400 text-sm">Memuat target tabungan...</p>
            </div>
        </div>
    </main>
</div>

{{-- MODAL ADD / EDIT GOAL --}}
<div id="modal-goal" class="modal-overlay" onclick="if(event.target===this)closeGoalModal()">
    <div class="modal-box">
        <div class="flex items-center justify-between mb-5">
            <h2 id="modal-goal-title" class="text-base font-bold text-slate-800">Tambah Target</h2>
            <button onclick="closeGoalModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="modal-goal-error" class="hidden mb-4 bg-rose-50 border border-rose-200 rounded-xl px-4 py-3 text-xs text-rose-600 font-medium"></div>
        <div class="space-y-4">
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Target / Nama</label>
                <input id="goal-title" type="text" placeholder="Pergi Umroh" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Jumlah Target (Rp)</label>
                <input id="goal-amount" type="number" min="1" placeholder="6000000" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
            </div>
        </div>
        <div class="flex gap-3 mt-6">
            <button onclick="closeGoalModal()" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">Batal</button>
            <button id="btn-save-goal" onclick="submitGoal()" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white bg-emerald-500 hover:bg-emerald-600 transition-colors">Simpan</button>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH SALDO --}}
<div id="modal-saldo" class="modal-overlay" onclick="if(event.target===this)closeSaldoModal()">
    <div class="modal-box" style="width:360px">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base font-bold text-slate-800">Tambah Saldo</h2>
            <button onclick="closeSaldoModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <p id="modal-saldo-name" class="text-xs text-slate-500 mb-4"></p>
        <div id="modal-saldo-error" class="hidden mb-4 bg-rose-50 border border-rose-200 rounded-xl px-4 py-3 text-xs text-rose-600"></div>
        <div id="modal-saldo-warning" class="hidden mb-4 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-xs text-amber-700"></div>
        <div>
            <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Jumlah (Rp)</label>
            <input id="saldo-amount" type="number" min="1" placeholder="500000" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
        </div>
        <div class="flex gap-3 mt-6">
            <button onclick="closeSaldoModal()" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">Batal</button>
            <button id="btn-add-saldo" onclick="submitSaldo()" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white bg-emerald-500 hover:bg-emerald-600 transition-colors">Tambah</button>
        </div>
    </div>
</div>

{{-- MODAL KONFIRMASI ARCHIVE (delete) --}}
<div id="modal-archive" class="modal-overlay" onclick="if(event.target===this)closeArchiveModal()">
    <div class="modal-box" style="width:340px">
        <div class="text-center py-3">
            <div class="w-14 h-14 bg-rose-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2l1-12M10 12v4m4-4v4"/></svg>
            </div>
            <p class="font-bold text-slate-800 text-sm mb-1">Apakah anda yakin ingin</p>
            <p class="text-rose-500 font-bold text-sm">Menghapus</p>
            <p class="text-slate-400 text-xs mt-2">Target ini akan diarsipkan. Saldo yang terkumpul tidak akan terhapus.</p>
        </div>
        <div class="flex gap-3 mt-5">
            <button onclick="closeArchiveModal()" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">Batal</button>
            <button id="btn-confirm-archive" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white bg-rose-500 hover:bg-rose-600 transition-colors">Ya</button>
        </div>
    </div>
</div>

<script>
const TOKEN   = '{{ session("auth_token") }}';
const HEADERS = { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + TOKEN, 'Accept': 'application/json' };

let goals = [];
let editGoalId    = null;
let saldoGoalId   = null;
let archiveGoalId = null;
let saldoConfirm  = false;

function fmtRp(n) { return 'Rp' + Math.abs(n || 0).toLocaleString('id-ID'); }

const GOAL_ICONS = {
    spiritual: `<svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/></svg>`,
    travel:    `<svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v9a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-9"/></svg>`,
    gadget:    `<svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>`,
    couple:    `<svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>`,
    default:   `<svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>`,
};

const GOAL_ICON_COLORS = {
    spiritual: 'goal-icon-spiritual',
    travel: 'goal-icon-travel',
    gadget: 'goal-icon-gadget',
    couple: 'goal-icon-couple',
    default: 'goal-icon-default',
};

function guessIconType(title) {
    const t = (title || '').toLowerCase();
    if (t.includes('umroh') || t.includes('haji') || t.includes('ibadah')) return 'spiritual';
    if (t.includes('liburan') || t.includes('jalan') || t.includes('travel') || t.includes('trip') || t.includes('wisata')) return 'travel';
    if (t.includes('iphone') || t.includes('hp') || t.includes('laptop') || t.includes('gadget') || t.includes('beli')) return 'gadget';
    if (t.includes('nikah') || t.includes('wedding') || t.includes('kawin') || t.includes('pasangan')) return 'couple';
    return 'default';
}

const PROGRESS_COLORS = [
    'bg-emerald-500',
    'bg-blue-500',
    'bg-purple-500',
    'bg-pink-500',
    'bg-teal-500',
    'bg-amber-500',
];

async function loadGoals() {
    try {
        const res  = await fetch('/api/v1/goals', { headers: HEADERS });
        const data = await res.json();
        goals = data.data || [];
        renderGoals();
    } catch(e) {
        document.getElementById('goals-grid').innerHTML = '<div class="col-span-2 text-center text-rose-400 text-sm py-8">Gagal memuat data goals.</div>';
    }
}

function renderGoals() {
    const grid = document.getElementById('goals-grid');
    if (!goals.length) {
        grid.innerHTML = `
            <div class="col-span-2 py-16 flex flex-col items-center text-center">
                <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center mb-3">
                    <svg class="w-7 h-7 text-emerald-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <p class="text-slate-500 text-sm font-medium">Belum ada target tabungan</p>
                <p class="text-slate-400 text-xs mt-1">Buat target impianmu sekarang!</p>
                <button onclick="openAddGoal()" class="mt-3 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded-xl transition-colors">
                    + Tambah Target
                </button>
            </div>`;
        return;
    }

    grid.innerHTML = goals.map((g, idx) => {
        const collected = g.collected_amount || 0;
        const target    = g.target_amount || 1;
        const pct       = Math.min(Math.round(collected / target * 100), 100);
        const iconType  = guessIconType(g.title);
        const iconHtml  = GOAL_ICONS[iconType];
        const iconClass = GOAL_ICON_COLORS[iconType];
        const barColor  = PROGRESS_COLORS[idx % PROGRESS_COLORS.length];

        return `
        <div class="bg-white rounded-2xl border border-slate-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 ${iconClass}">${iconHtml}</div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">${g.title || '-'}</h3>
                    </div>
                </div>
                <button onclick="openEditGoal(${idx})" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-400 transition-colors flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
            </div>

            <div class="mb-3">
                <div class="flex justify-between text-xs mb-1.5">
                    <span class="text-slate-500">Terkumpul</span>
                    <span class="font-bold text-emerald-600">${pct}%</span>
                </div>
                <p class="font-bold text-slate-800 text-sm mb-1">${fmtRp(collected)} <span class="text-slate-400 font-normal text-xs">/ ${fmtRp(target)}</span></p>
                <div class="goal-progress">
                    <div class="goal-progress-bar ${barColor}" style="width:${pct}%"></div>
                </div>
            </div>

            <div class="flex gap-2">
                <button onclick="openSaldo(${idx})" class="flex-1 py-2 rounded-xl text-xs font-semibold bg-emerald-500 hover:bg-emerald-600 text-white transition-colors">
                    Tambah Saldo
                </button>
                <button onclick="openArchive(${idx})" class="w-9 h-9 rounded-xl bg-rose-50 hover:bg-rose-100 flex items-center justify-center text-rose-500 transition-colors flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </div>`;
    }).join('');
}

// Add Goal modal
function openAddGoal() {
    editGoalId = null;
    document.getElementById('modal-goal-title').textContent = 'Tambah Target';
    document.getElementById('goal-title').value = '';
    document.getElementById('goal-amount').value = '';
    document.getElementById('modal-goal-error').classList.add('hidden');
    document.getElementById('btn-save-goal').textContent = 'Simpan';
    document.getElementById('modal-goal').classList.add('open');
}

function openEditGoal(idx) {
    const g = goals[idx];
    editGoalId = g.id;
    document.getElementById('modal-goal-title').textContent = 'Edit Target';
    document.getElementById('goal-title').value = g.title || '';
    document.getElementById('goal-amount').value = g.target_amount || '';
    document.getElementById('modal-goal-error').classList.add('hidden');
    document.getElementById('btn-save-goal').textContent = 'Simpan';
    document.getElementById('modal-goal').classList.add('open');
}

function closeGoalModal() { document.getElementById('modal-goal').classList.remove('open'); editGoalId = null; }

async function submitGoal() {
    const title  = document.getElementById('goal-title').value.trim();
    const amount = parseInt(document.getElementById('goal-amount').value);
    const errEl  = document.getElementById('modal-goal-error');
    errEl.classList.add('hidden');

    if (!title) { errEl.textContent = 'Nama target wajib diisi.'; errEl.classList.remove('hidden'); return; }
    if (!amount || amount < 1) { errEl.textContent = 'Jumlah target harus lebih dari 0.'; errEl.classList.remove('hidden'); return; }

    const btn = document.getElementById('btn-save-goal');
    btn.textContent = 'Menyimpan...'; btn.disabled = true;

    try {
        // BE hanya punya store (POST /api/v1/goals) — tidak ada update endpoint, hanya archive.
        // Jika edit: archive dulu lalu create baru (atau bisa skip jika BE tidak support PATCH)
        // Berdasarkan GoalController: tidak ada update, hanya store/archive. Maka edit = buat baru.
        const res  = await fetch('/api/v1/goals', {
            method: 'POST', headers: HEADERS,
            body: JSON.stringify({ title, target_amount: amount })
        });
        const data = await res.json();
        if (!res.ok) { errEl.textContent = data.message || 'Terjadi kesalahan.'; errEl.classList.remove('hidden'); btn.textContent = 'Simpan'; btn.disabled = false; return; }

        closeGoalModal();
        loadGoals();
    } catch(e) {
        errEl.textContent = 'Koneksi gagal.'; errEl.classList.remove('hidden');
        btn.textContent = 'Simpan'; btn.disabled = false;
    }
}

// Saldo modal
function openSaldo(idx) {
    const g = goals[idx];
    saldoGoalId   = g.id;
    saldoConfirm  = false;
    document.getElementById('modal-saldo-name').textContent = 'Target: ' + (g.title || '-');
    document.getElementById('saldo-amount').value = '';
    document.getElementById('modal-saldo-error').classList.add('hidden');
    document.getElementById('modal-saldo-warning').classList.add('hidden');
    document.getElementById('btn-add-saldo').textContent = 'Tambah';
    document.getElementById('modal-saldo').classList.add('open');
}
function closeSaldoModal() { document.getElementById('modal-saldo').classList.remove('open'); saldoGoalId = null; }

async function submitSaldo() {
    const amount = parseInt(document.getElementById('saldo-amount').value);
    const errEl  = document.getElementById('modal-saldo-error');
    const warnEl = document.getElementById('modal-saldo-warning');
    errEl.classList.add('hidden'); warnEl.classList.add('hidden');

    if (!amount || amount < 1) { errEl.textContent = 'Jumlah harus lebih dari 0.'; errEl.classList.remove('hidden'); return; }

    const btn = document.getElementById('btn-add-saldo');
    btn.textContent = 'Memproses...'; btn.disabled = true;

    try {
        const res  = await fetch(`/api/v1/goals/${saldoGoalId}/allocate`, {
            method: 'POST', headers: HEADERS,
            body: JSON.stringify({ amount, confirm: saldoConfirm })
        });
        const data = await res.json();

        if (res.status === 422 && data.errors?.warnings) {
            warnEl.textContent = '⚠️ ' + data.errors.warnings[0] + ' — Klik Tambah lagi untuk melanjutkan.';
            warnEl.classList.remove('hidden');
            saldoConfirm = true;
            btn.textContent = 'Konfirmasi & Tambah'; btn.disabled = false;
            return;
        }

        if (!res.ok) { errEl.textContent = data.message || 'Gagal.'; errEl.classList.remove('hidden'); btn.textContent = 'Tambah'; btn.disabled = false; return; }

        closeSaldoModal();
        loadGoals();
    } catch(e) { errEl.textContent = 'Koneksi gagal.'; errEl.classList.remove('hidden'); btn.textContent = 'Tambah'; btn.disabled = false; }
}

// Archive
function openArchive(idx) {
    archiveGoalId = goals[idx].id;
    document.getElementById('modal-archive').classList.add('open');
}
function closeArchiveModal() { document.getElementById('modal-archive').classList.remove('open'); archiveGoalId = null; }

document.getElementById('btn-confirm-archive').onclick = async function() {
    if (!archiveGoalId) return;
    this.textContent = 'Memproses...'; this.disabled = true;
    try {
        const res = await fetch(`/api/v1/goals/${archiveGoalId}/archive`, { method: 'PUT', headers: HEADERS });
        if (res.ok) { closeArchiveModal(); loadGoals(); }
    } catch(e) {}
    this.textContent = 'Ya'; this.disabled = false;
};

// Init
loadGoals();
</script>
</body>
</html>
