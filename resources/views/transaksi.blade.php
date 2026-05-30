<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi — FamsPay</title>
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
            background: #fff; border-radius: 20px; padding: 28px;
            width: 420px; max-width: 90vw;
            transform: translateY(16px) scale(.97); transition: transform .25s cubic-bezier(.4,0,.2,1);
            box-shadow: 0 24px 64px rgba(0,0,0,.18);
        }
        .modal-overlay.open .modal-box { transform: translateY(0) scale(1); }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased">

@php
    $user      = session('user');
    $isAdmin   = ($user['role'] ?? 'member') === 'admin';
    $initials  = strtoupper(substr($user['full_name'] ?? 'U', 0, 1));
    function fmtRp2(int|float $n): string { return 'Rp' . number_format((int)$n, 0, ',', '.'); }
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
        <a href="{{ route('transaksi') }}" class="nav-item active flex items-center gap-3 px-3 py-2.5 rounded-2xl text-sm font-semibold">
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
            <form method="POST" action="/logout" class="inline">
                @csrf
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
            <h1 class="text-base font-bold text-slate-800">Transaksi</h1>
            <p class="text-xs text-slate-400">Saatnya kelola keuanganmu sekarang juga.</p>
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
        <div class="bg-white rounded-2xl border border-slate-100 p-6">
            {{-- Toolbar --}}
            <div class="flex items-center gap-3 mb-5">
                <div class="flex-1 relative">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input id="search-input" type="text" placeholder="Cari transaksi, kategori, atau tanggal..." class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-300 focus:border-transparent bg-slate-50">
                </div>
                <select id="filter-type" class="text-sm border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-300 bg-slate-50 text-slate-600">
                    <option value="">Semua Tipe</option>
                    <option value="income">Pemasukan</option>
                    <option value="expense">Pengeluaran</option>
                </select>
                <button id="btn-tambah-transaksi" onclick="openModal('add')" class="flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors shadow-sm shadow-emerald-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Transaksi
                </button>
            </div>

            {{-- Table --}}
            <div class="overflow-hidden rounded-xl border border-slate-100">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-emerald-50">
                            <th class="text-left text-emerald-700 text-xs font-semibold px-4 py-3">ID</th>
                            <th class="text-left text-emerald-700 text-xs font-semibold px-4 py-3">PEMBAYARAN</th>
                            <th class="text-left text-emerald-700 text-xs font-semibold px-4 py-3">KATEGORI</th>
                            <th class="text-right text-emerald-700 text-xs font-semibold px-4 py-3">JUMLAH</th>
                            <th class="text-left text-emerald-700 text-xs font-semibold px-4 py-3">TANGGAL</th>
                            <th class="text-center text-emerald-700 text-xs font-semibold px-4 py-3">AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="txn-table-body" class="divide-y divide-slate-50">
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400 text-sm">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-10 h-10 bg-slate-100 rounded-2xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    </div>
                                    <span>Memuat transaksi...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination info --}}
            <div class="flex items-center justify-between mt-4">
                <p id="pagination-info" class="text-xs text-slate-400">—</p>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    Tampilkan:
                    <select id="per-page" class="text-xs border border-slate-200 rounded-lg px-2 py-1 focus:outline-none focus:ring-1 focus:ring-emerald-300">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                    </select>
                </div>
            </div>
        </div>
    </main>
</div>

{{-- ══ MODAL TAMBAH / EDIT TRANSAKSI ══ --}}
<div id="modal-txn" class="modal-overlay" onclick="if(event.target===this)closeModal('txn')">
    <div class="modal-box">
        <div class="flex items-center justify-between mb-5">
            <h2 id="modal-txn-title" class="text-base font-bold text-slate-800">Tambah Transaksi</h2>
            <button onclick="closeModal('txn')" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div id="modal-txn-error" class="hidden mb-4 bg-rose-50 border border-rose-200 rounded-xl px-4 py-3 text-xs text-rose-600 font-medium"></div>
        <div id="modal-txn-warning" class="hidden mb-4 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-xs text-amber-700 font-medium"></div>

        <div class="space-y-4">
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Tipe Transaksi</label>
                <div class="grid grid-cols-2 gap-2">
                    <button id="btn-type-expense" onclick="setType('expense')" class="type-btn active-type py-2.5 rounded-xl text-sm font-semibold border-2 transition-all">
                        Pengeluaran
                    </button>
                    <button id="btn-type-income" onclick="setType('income')" class="type-btn py-2.5 rounded-xl text-sm font-semibold border-2 transition-all">
                        Pemasukan
                    </button>
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Pembayaran / Keterangan</label>
                <input id="txn-note" type="text" placeholder="Spotify Prem" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Kategori</label>
                <select id="txn-category" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 bg-white">
                    <option value="">Pilih kategori...</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Jumlah (Rp)</label>
                <input id="txn-amount" type="number" min="1" placeholder="150000" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Tanggal</label>
                <input id="txn-date" type="date" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300">
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button onclick="closeModal('txn')" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">Batal</button>
            <button id="btn-simpan-txn" onclick="submitTxn()" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white bg-emerald-500 hover:bg-emerald-600 transition-colors">Simpan</button>
        </div>
    </div>
</div>

{{-- ══ MODAL KONFIRMASI HAPUS ══ --}}
<div id="modal-delete" class="modal-overlay" onclick="if(event.target===this)closeModal('delete')">
    <div class="modal-box" style="width:340px">
        <div class="text-center py-3">
            <div class="w-14 h-14 bg-rose-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <p class="font-bold text-slate-800 text-sm mb-1">Apakah anda yakin ingin</p>
            <p class="text-rose-500 font-bold text-sm">Menghapus</p>
            <p class="text-slate-400 text-xs mt-2">Transaksi ini akan dihapus secara permanen dan saldo akan dipulihkan.</p>
        </div>
        <div class="flex gap-3 mt-5">
            <button onclick="closeModal('delete')" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">Batal</button>
            <button id="btn-confirm-delete" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white bg-rose-500 hover:bg-rose-600 transition-colors">Ya, Hapus</button>
        </div>
    </div>
</div>

<script>
const TOKEN = '{{ session("auth_token") }}';
const HEADERS = { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + TOKEN, 'Accept': 'application/json' };

// State
let allTxns = [];
let editId = null;
let deleteId = null;
let currentType = 'expense';
let needConfirm = false;
let perPage = 10;
let currentPage = 1;

const INCOME_CATS  = ['Salary','Bonus','Investment','Gift','Other'];
const EXPENSE_CATS = ['Food','Transport','Shopping','Health','Education','Entertainment','Bills','Housing','Savings','Other'];
const CAT_ID_MAP   = { 'Food':'Makanan','Transport':'Transportasi','Shopping':'Belanja','Health':'Kesehatan','Education':'Pendidikan','Entertainment':'Hiburan','Bills':'Tagihan','Housing':'Perumahan','Savings':'Tabungan','Other':'Lainnya','Salary':'Gaji','Bonus':'Bonus','Investment':'Investasi','Gift':'Hadiah' };

function fmtRp(n) { return 'Rp' + Math.abs(n).toLocaleString('id-ID'); }
function fmtDate(d) { if(!d) return '-'; const dt = new Date(d); return dt.toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric'}); }
function todayISO() { return new Date().toISOString().split('T')[0]; }

// Modal helpers
function openModal(name) { document.getElementById('modal-' + name).classList.add('open'); }
function closeModal(name) {
    document.getElementById('modal-' + name).classList.remove('open');
    if (name === 'txn') { resetForm(); }
    if (name === 'delete') { deleteId = null; }
}

function resetForm() {
    editId = null; needConfirm = false;
    document.getElementById('modal-txn-title').textContent = 'Tambah Transaksi';
    document.getElementById('txn-note').value = '';
    document.getElementById('txn-amount').value = '';
    document.getElementById('txn-date').value = todayISO();
    document.getElementById('modal-txn-error').classList.add('hidden');
    document.getElementById('modal-txn-warning').classList.add('hidden');
    document.getElementById('btn-simpan-txn').textContent = 'Simpan';
    setType('expense');
}

function setType(type) {
    currentType = type;
    const expBtn = document.getElementById('btn-type-expense');
    const incBtn = document.getElementById('btn-type-income');
    if (type === 'expense') {
        expBtn.className = 'type-btn py-2.5 rounded-xl text-sm font-semibold border-2 border-rose-400 bg-rose-50 text-rose-600';
        incBtn.className = 'type-btn py-2.5 rounded-xl text-sm font-semibold border-2 border-slate-200 bg-white text-slate-500';
    } else {
        incBtn.className = 'type-btn py-2.5 rounded-xl text-sm font-semibold border-2 border-emerald-400 bg-emerald-50 text-emerald-600';
        expBtn.className = 'type-btn py-2.5 rounded-xl text-sm font-semibold border-2 border-slate-200 bg-white text-slate-500';
    }
    populateCategories(type);
}

function populateCategories(type) {
    const sel = document.getElementById('txn-category');
    const cats = type === 'income' ? INCOME_CATS : EXPENSE_CATS;
    sel.innerHTML = '<option value="">Pilih kategori...</option>' + cats.map(c => `<option value="${c}">${CAT_ID_MAP[c] || c}</option>`).join('');
}

// Modal add
function openModal(name) {
    if (name === 'add') {
        resetForm();
        document.getElementById('txn-date').value = todayISO();
        document.getElementById('modal-txn').classList.add('open');
        return;
    }
    document.getElementById('modal-' + name).classList.add('open');
}

// Edit
function openEdit(txn) {
    resetForm();
    editId = txn.id;
    document.getElementById('modal-txn-title').textContent = 'Edit Transaksi';
    setType(txn.type || 'expense');
    document.getElementById('txn-note').value = txn.note || txn.category_name || '';
    document.getElementById('txn-amount').value = Math.abs(txn.amount || 0);
    document.getElementById('txn-date').value = (txn.txn_date || '').split('T')[0] || todayISO();
    // Set category
    setTimeout(() => { document.getElementById('txn-category').value = txn.category_name || ''; }, 50);
    document.getElementById('modal-txn').classList.add('open');
}

// Submit (create or update)
async function submitTxn() {
    const note     = document.getElementById('txn-note').value.trim();
    const catVal   = document.getElementById('txn-category').value;
    const amount   = parseInt(document.getElementById('txn-amount').value);
    const date     = document.getElementById('txn-date').value;
    const errEl    = document.getElementById('modal-txn-error');
    const warnEl   = document.getElementById('modal-txn-warning');
    errEl.classList.add('hidden');
    warnEl.classList.add('hidden');

    if (!amount || amount < 1) { errEl.textContent = 'Jumlah harus lebih dari 0.'; errEl.classList.remove('hidden'); return; }
    if (!date) { errEl.textContent = 'Tanggal wajib diisi.'; errEl.classList.remove('hidden'); return; }

    const payload = {
        type: currentType,
        amount: amount,
        txn_date: date,
        category_name: catVal || 'Other',
        note: note || null,
        confirm: needConfirm,
    };

    const btn = document.getElementById('btn-simpan-txn');
    btn.textContent = 'Menyimpan...'; btn.disabled = true;

    try {
        const url    = editId ? `/api/v1/transactions/${editId}` : '/api/v1/transactions';
        const method = editId ? 'PUT' : 'POST';
        const res    = await fetch(url, { method, headers: HEADERS, body: JSON.stringify(payload) });
        const data   = await res.json();

        if (res.status === 422 && data.errors?.warnings) {
            // Warning — perlu konfirmasi
            warnEl.textContent = '⚠️ ' + data.errors.warnings[0] + ' — Klik Simpan lagi untuk melanjutkan.';
            warnEl.classList.remove('hidden');
            needConfirm = true;
            btn.textContent = 'Konfirmasi & Simpan';
            btn.disabled = false;
            return;
        }

        if (!res.ok) {
            errEl.textContent = data.message || 'Terjadi kesalahan.';
            errEl.classList.remove('hidden');
            btn.textContent = 'Simpan'; btn.disabled = false;
            return;
        }

        closeModal('txn');
        loadTransactions();
    } catch(e) {
        errEl.textContent = 'Koneksi gagal. Coba lagi.';
        errEl.classList.remove('hidden');
        btn.textContent = 'Simpan'; btn.disabled = false;
    }
}

// Delete
function confirmDelete(id) {
    deleteId = id;
    document.getElementById('modal-delete').classList.add('open');
}

document.getElementById('btn-confirm-delete').onclick = async function() {
    if (!deleteId) return;
    this.textContent = 'Menghapus...'; this.disabled = true;
    try {
        const res = await fetch(`/api/v1/transactions/${deleteId}`, { method: 'DELETE', headers: HEADERS });
        if (res.ok) { closeModal('delete'); loadTransactions(); }
    } catch(e) {}
    this.textContent = 'Ya, Hapus'; this.disabled = false;
};

// Load transactions
async function loadTransactions() {
    const typeFilter = document.getElementById('filter-type').value;
    let url = '/api/v1/transactions';
    if (typeFilter) url += '?type=' + typeFilter;

    try {
        const res  = await fetch(url, { headers: HEADERS });
        const data = await res.json();
        allTxns = data.data || [];
        renderTable();
    } catch(e) {
        document.getElementById('txn-table-body').innerHTML = `<tr><td colspan="6" class="px-4 py-8 text-center text-rose-400 text-sm">Gagal memuat data transaksi.</td></tr>`;
    }
}

function renderTable() {
    const searchVal = document.getElementById('search-input').value.toLowerCase();
    let filtered = allTxns;

    if (searchVal) {
        filtered = filtered.filter(t =>
            (t.note || '').toLowerCase().includes(searchVal) ||
            (t.category_name || '').toLowerCase().includes(searchVal) ||
            (t.txn_date || '').includes(searchVal)
        );
    }

    const total = filtered.length;
    const start = (currentPage - 1) * perPage;
    const paged = filtered.slice(start, start + perPage);

    if (paged.length === 0) {
        document.getElementById('txn-table-body').innerHTML = `
            <tr><td colspan="6" class="px-4 py-12 text-center">
                <div class="flex flex-col items-center gap-2">
                    <div class="w-10 h-10 bg-slate-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                    <p class="text-sm text-slate-400">Belum ada transaksi</p>
                    <p class="text-xs text-slate-300">Tambahkan transaksi pertama Anda</p>
                </div>
            </td></tr>`;
        document.getElementById('pagination-info').textContent = 'Tidak ada data';
        return;
    }

    const rows = paged.map((t, idx) => {
        const isIncome = t.type === 'income';
        const amountStr = (isIncome ? '+' : '-') + fmtRp(t.amount);
        const amountClass = isIncome ? 'text-emerald-600' : 'text-rose-500';
        const isReadOnly = t.is_system || t.read_only;
        const editBtn = isReadOnly
            ? `<button disabled class="w-7 h-7 bg-slate-100 rounded-lg flex items-center justify-center opacity-40 cursor-not-allowed"><svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>`
            : `<button onclick='openEdit(${JSON.stringify(t)})' class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center hover:bg-blue-100 transition-colors"><svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>`;
        const delBtn = isReadOnly
            ? `<button disabled class="w-7 h-7 bg-slate-100 rounded-lg flex items-center justify-center opacity-40 cursor-not-allowed"><svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>`
            : `<button onclick="confirmDelete('${t.id}')" class="w-7 h-7 bg-rose-50 rounded-lg flex items-center justify-center hover:bg-rose-100 transition-colors"><svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>`;

        return `<tr class="hover:bg-slate-50 transition-colors">
            <td class="px-4 py-3 text-slate-400 text-xs">${start + idx + 1}</td>
            <td class="px-4 py-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl ${isIncome ? 'bg-emerald-100' : 'bg-rose-100'} flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 ${isIncome ? 'text-emerald-600' : 'text-rose-500'}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            ${isIncome ? '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>' : '<path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>'}
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-700 text-xs">${(t.note || t.category_name || 'Transaksi').slice(0,28)}</p>
                        <p class="text-slate-400 text-[10px]">${t.type === 'income' ? 'Pemasukan' : 'Pengeluaran'}</p>
                    </div>
                </div>
            </td>
            <td class="px-4 py-3"><span class="text-xs text-emerald-600 font-medium">${t.category_name || '-'}</span></td>
            <td class="px-4 py-3 text-right"><span class="text-xs font-bold ${amountClass}">${amountStr}</span></td>
            <td class="px-4 py-3 text-xs text-slate-500">${fmtDate(t.txn_date)}</td>
            <td class="px-4 py-3">
                <div class="flex items-center justify-center gap-1.5">${editBtn}${delBtn}</div>
            </td>
        </tr>`;
    });

    document.getElementById('txn-table-body').innerHTML = rows.join('');
    document.getElementById('pagination-info').textContent = `Menampilkan ${start + 1}–${Math.min(start + perPage, total)} dari ${total} data`;
}

// Event listeners
document.getElementById('search-input').addEventListener('input', () => { currentPage = 1; renderTable(); });
document.getElementById('filter-type').addEventListener('change', () => { currentPage = 1; loadTransactions(); });
document.getElementById('per-page').addEventListener('change', function() { perPage = parseInt(this.value); currentPage = 1; renderTable(); });

// Init
setType('expense');
loadTransactions();
</script>
</body>
</html>
