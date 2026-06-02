<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — FamsPay</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if($isNewUser)
    {{-- Script ini berjalan SEBELUM browser paint apapun.
         Kalau user sudah pernah lihat tour, sembunyikan elemen tour via CSS
         sehingga tidak pernah terlihat sama sekali (tidak ada flash). --}}
    <script>
        (function() {
            try {
                if (localStorage.getItem('famspay_tour_done') === '1') {
                    // Inject style untuk sembunyikan tour sebelum DOM dirender
                    var s = document.createElement('style');
                    s.textContent = '#tour-backdrop,#tour-highlight,#tour-popup{display:none!important;}';
                    document.head.appendChild(s);
                }
            } catch(e) {}
        })();
    </script>
    @endif
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* ── Sidebar full-height fix ── */
        html, body { height: 100%; margin: 0; }
        #app-sidebar {
            background: linear-gradient(180deg, #A7F3D0 0%, #6EE7B7 25%, #34D399 60%, #10B981 100%);
            position: fixed;
            top: 0; left: 0;
            width: 200px;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            z-index: 30;
            box-shadow: 4px 0 20px rgba(0,0,0,.08);
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
        .nav-item { color: #065F46; transition: all .15s; }
        .nav-item.active {
            background-color: #059669;
            color: #fff;
            box-shadow: 0 4px 12px rgba(5,150,105,.35);
        }
        .nav-item:not(.active):hover {
            background-color: rgba(255,255,255,.30);
            color: #022c22;
        }

        /* ── Wallet card (dompet fisik) ── */
        .wallet-physical {
            border-radius: 18px;
            position: relative;
            overflow: visible;
            aspect-ratio: 5 / 3;   /* proporsi kartu fisik — tidak boleh diubah */
            width: 100%;
            max-width: 420px;
        }
        /* Kartu-kartu di belakang dompet */
        .wallet-physical .card-stack-1,
        .wallet-physical .card-stack-2 {
            position: absolute;
            left: 12px; right: 12px;
            height: 14px;
            border-radius: 12px 12px 0 0;
            z-index: 0;
        }
        .wallet-physical .card-stack-1 { top: -10px; }
        .wallet-physical .card-stack-2 { top: -5px; }
        /* Body utama dompet — isi penuh area aspect-ratio */
        .wallet-physical .wallet-body {
            position: absolute; inset: 0; z-index: 1;
            border-radius: 18px;
            padding: 16px 20px 14px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        /* Slot bawah dompet */
        .wallet-body .wallet-slot {
            background: rgba(0,0,0,0.15);
            border-radius: 8px;
            height: 26px;
            flex: 1;
            display: flex;
            align-items: center;
            padding: 0 10px;
        }
        /* Arrow icon dompet */
        .wallet-arrow {
            position: absolute;
            top: 14px; right: 14px;
            width: 28px; height: 28px;
            background: rgba(255,255,255,0.25);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }

        /* Warna border atas per member */
        .dompet-darkred  .card-stack-1 { background: #7f1d1d; }
        .dompet-darkred  .card-stack-2 { background: #991b1b; }
        .dompet-darkred  .wallet-body  { background: linear-gradient(145deg, #059669 0%, #10B981 100%); }

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

        /* Admin main wallet — hijau tua tanpa arrow icon */
        .dompet-admin .card-stack-1 { background: #14532d; }
        .dompet-admin .card-stack-2 { background: #166534; }
        .dompet-admin .wallet-body  { background: linear-gradient(145deg, #059669 0%, #047857 100%); }

        /* ── Stat card ── */
        .stat-card { border-radius: 16px; background: #fff; border: 1px solid #f1f5f9; }

        /* ── Tour overlay ── */
        #tour-backdrop {
            position: fixed; inset: 0; z-index: 8998;
            background: rgba(10,15,30,.72);
            backdrop-filter: blur(1px);
        }
        /* Highlight cutout via box-shadow */
        #tour-highlight {
            position: fixed;
            z-index: 8999;
            border-radius: 14px;
            box-shadow: 0 0 0 9999px rgba(10,15,30,.72);
            pointer-events: none;
            transition: top .38s cubic-bezier(.4,0,.2,1),
                        left .38s cubic-bezier(.4,0,.2,1),
                        width .38s cubic-bezier(.4,0,.2,1),
                        height .38s cubic-bezier(.4,0,.2,1);
            outline: 2px solid rgba(255,255,255,.18);
        }
        /* Tour popup */
        #tour-popup {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 48px rgba(0,0,0,.22);
            width: 340px;
            position: fixed;
            z-index: 9001;
            overflow: hidden;
            transition: top .38s cubic-bezier(.4,0,.2,1),
                        left .38s cubic-bezier(.4,0,.2,1),
                        opacity .22s ease;
        }
        /* Animasi masuk pertama kali */
        @keyframes tour-enter {
            from { opacity:0; transform: translateY(12px) scale(.97); }
            to   { opacity:1; transform: translateY(0) scale(1); }
        }
        #tour-popup { animation: tour-enter .3s ease forwards; }

        /* Step content animasi slide */
        @keyframes step-in {
            from { opacity:0; transform: translateX(14px); }
            to   { opacity:1; transform: translateX(0); }
        }
        .tour-step { animation: step-in .25s ease forwards; }
        .tour-step.hidden { display: none; }

        /* Header strip warna */
        .tour-header {
            padding: 20px 22px 16px;
            display: flex; align-items: flex-start; gap: 14px;
        }
        .tour-icon-wrap {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        /* Body */
        .tour-body { padding: 0 22px 18px; }
        .tour-title { font-size: 14px; font-weight: 800; color: #0f172a; line-height: 1.3; }
        .tour-sub   { font-size: 11px; color: #64748b; margin-top: 2px; line-height: 1.5; }
        .tour-desc  { font-size: 12px; color: #475569; line-height: 1.65; margin-bottom: 16px; }

        /* Hint chip */
        .tour-hint {
            display: flex; align-items: center; gap: 7px;
            padding: 8px 11px; border-radius: 9px;
            margin-bottom: 16px;
            font-size: 11px; font-weight: 600; line-height: 1.4;
        }
        .tour-hint svg { flex-shrink:0; }

        /* Footer: dots + btns */
        .tour-footer {
            display: flex; align-items: center; justify-content: space-between;
        }
        .tour-dots { display: flex; gap: 5px; }
        .tour-dot  {
            width: 6px; height: 6px; border-radius: 50%;
            background: #e2e8f0; transition: all .2s;
        }
        .tour-dot.active {
            width: 18px; border-radius: 3px; background: #059669;
        }
        .tour-btn-next {
            font-size: 12px; font-weight: 700;
            padding: 8px 18px; border-radius: 9px;
            cursor: pointer; border: none; transition: all .15s;
        }
        .tour-btn-back {
            font-size: 12px; font-weight: 600; color: #94a3b8;
            padding: 8px 12px; border-radius: 9px; cursor: pointer;
            border: none; background: transparent; transition: color .15s;
        }
        .tour-btn-back:hover { color: #475569; }
        .tour-divider { height: 1px; background: #f1f5f9; margin: 0 22px 14px; }

        /* ── Chart (Money Flow) ── */
        .chart-bar-wrap { display: flex; align-items: flex-end; gap: 3px; flex: 1; }
        .chart-bar {
            border-radius: 5px 5px 0 0;
            transition: height .5s cubic-bezier(.4,0,.2,1);
            min-width: 10px;
            min-height: 2px;
            cursor: pointer;
            position: relative;
        }
        .chart-bar:hover::after {
            content: attr(data-tip);
            position: absolute; bottom: calc(100% + 6px); left: 50%;
            transform: translateX(-50%);
            background: #1e293b; color: #fff;
            font-size: 9px; font-weight: 600;
            padding: 3px 6px; border-radius: 6px;
            white-space: nowrap; pointer-events: none;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #d1fae5; border-radius: 10px; }

        /* ── Responsive: stats mengisi ruang saat zoom out ── */
        .stat-card { min-width: 0; }
        .stat-card p.font-extrabold { word-break: break-all; }

        /* main content offset */
        #main-wrapper { margin-left: 200px; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased" style="height:100%;min-height:100vh;">

@php
    $walletBalance  = $personalData['wallet_balance']  ?? 0;
    $monthlyIncome  = $personalData['current_month']['income']  ?? 0;
    $monthlyExpense = $personalData['current_month']['expense'] ?? 0;
    $totalSavings   = $personalData['total_savings']   ?? 0;
    $budgetData     = $personalData['budget_analysis'] ?? null;
    $recentTxns     = $personalData['recent_transactions'] ?? [];

    // Limit: jika belum ada budget_analysis (belum di-set admin), tampilkan 0/0
    $limitBase      = $budgetData['monthly_limit_base']      ?? 0;
    $usedLimit      = $budgetData['total_expense_in_period'] ?? 0;
    $remainingLimit = $budgetData['remaining_limit']         ?? 0;

    // Weekly money flow — ambil dari daily_flow jika tersedia, fallback ke 0 semua hari
    $dailyFlow = $personalData['daily_flow'] ?? [];
    $dayNames  = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];

    // Normalisasi data per-hari; key bisa berupa 'Mon','Tue',dst ATAU index 0-6
    $incPerDay = [0,0,0,0,0,0,0];
    $expPerDay = [0,0,0,0,0,0,0];

    // Jika BE tidak kirim daily_flow, build dari recent transactions
    if (!empty($dailyFlow)) {
        foreach ($dailyFlow as $idx => $day) {
            $i = is_numeric($idx) ? (int)$idx : $idx;
            if (is_int($i) && $i >= 0 && $i < 7) {
                $incPerDay[$i] = (int)($day['income']  ?? 0);
                $expPerDay[$i] = (int)($day['expense'] ?? 0);
            }
        }
    } else {
        // Fallback: hitung dari recent transactions (per hari dalam minggu)
        foreach ($recentTxns as $txn) {
            if (!empty($txn['txn_date'])) {
                $dow = (int)\Carbon\Carbon::parse($txn['txn_date'])->format('N') - 1; // 0=Mon..6=Sun
                if ($txn['type'] === 'income')  $incPerDay[$dow] += (int)$txn['amount'];
                else                             $expPerDay[$dow] += (int)$txn['amount'];
            }
        }
    }
    $maxFlow = max(array_merge($incPerDay, $expPerDay, [1]));

    // Hari ini (0=Sen..6=Min) untuk highlight
    $todayDow = (int)\Carbon\Carbon::now()->format('N') - 1;

    // Budget kategori dari recent transactions (bulan ini)
    $catMap = [];
    foreach ($recentTxns as $txn) {
        if (($txn['type'] ?? '') === 'expense' && !empty($txn['category_name'])) {
            $c = $txn['category_name'];
            $catMap[$c] = ($catMap[$c] ?? 0) + (int)$txn['amount'];
        }
    }
    // Fallback jika tidak ada data
    if (empty($catMap) && $monthlyExpense > 0) {
        $catMap = ['Lainnya' => $monthlyExpense];
    }
    // Warna per kategori (konsisten)
    $catColorPalette = [
        'Food'           => '#10B981', 'Makanan'        => '#10B981',
        'Transport'      => '#3B82F6', 'Transportasi'   => '#3B82F6',
        'Shopping'       => '#F59E0B', 'Belanja'        => '#F59E0B',
        'Health'         => '#EF4444', 'Kesehatan'      => '#EF4444',
        'Education'      => '#8B5CF6', 'Pendidikan'     => '#8B5CF6',
        'Entertainment'  => '#EC4899', 'Hiburan'        => '#EC4899',
        'Bills'          => '#F97316', 'Tagihan'        => '#F97316',
        'Housing'        => '#14B8A6', 'Perumahan'      => '#14B8A6',
        'Savings'        => '#06B6D4', 'Tabungan'       => '#06B6D4',
        'Salary'         => '#22C55E', 'Gaji'           => '#22C55E',
        'Transfer'       => '#6366F1',
        'Servis'         => '#84CC16',
        'Other'          => '#94A3B8', 'Lainnya'        => '#94A3B8',
    ];
    $fallbackColors = ['#10B981','#3B82F6','#F59E0B','#EF4444','#8B5CF6','#EC4899','#F97316','#14B8A6','#06B6D4','#94A3B8'];
    $totalCatExp = array_sum($catMap);
    $catChartData = [];
    $fci = 0;
    foreach ($catMap as $cName => $cAmt) {
        $catChartData[] = [
            'name'  => $cName,
            'amount'=> $cAmt,
            'pct'   => $totalCatExp > 0 ? round($cAmt / $totalCatExp * 100) : 0,
            'color' => $catColorPalette[$cName] ?? $fallbackColors[$fci % count($fallbackColors)],
        ];
        $fci++;
    }

    // Goals untuk card target tabungan (via API call inline)
    $goalsList = [];
    try {
        $userModel2 = new \App\Models\User();
        $userModel2->forceFill($user ?? []);
        $userModel2->exists = true;
        $goalCtrl = app(\App\Http\Controllers\Api\V1\GoalController::class);
        $goalFakeReq = \Illuminate\Http\Request::create('/api/v1/goals','GET');
        $goalFakeReq->setUserResolver(fn() => $userModel2);
        $goalResp = $goalCtrl->index($goalFakeReq);
        $goalsList = $goalResp->getData(true)['data'] ?? [];
    } catch(\Exception $e) { $goalsList = []; }

    // Format IDR
    function fmtRp(int|float $n): string {
        return 'Rp' . number_format((int)$n, 0, ',', '.');
    }

    // Warna dompet per member (index → class)
    $dompetColors = ['dompet-darkred','dompet-purple','dompet-blue','dompet-lime','dompet-teal','dompet-rose'];
    $initials = strtoupper(substr($user['full_name'] ?? 'U', 0, 1));
@endphp

{{-- ════════════════════════════════════
     TOUR (User Baru) — Minimalist
     ════════════════════════════════════ --}}
@if($isNewUser)
<div id="tour-backdrop"></div>
<div id="tour-highlight" style="display:none;"></div>

<div id="tour-popup">

    {{-- Step 1: Welcome --}}
    <div id="ts-1" class="tour-step">
        <div class="tour-header" style="background:#f0fdf4;">
            <div class="tour-icon-wrap" style="background:#dcfce7;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <div>
                <p class="tour-title">Selamat datang di FamsPay</p>
                <p class="tour-sub">Panduan singkat untuk mulai mengelola keuangan keluarga.</p>
            </div>
        </div>
        <div class="tour-divider"></div>
        <div class="tour-body">
            <p class="tour-desc">Halo, <strong>{{ explode(' ', $user['full_name'] ?? 'Pengguna')[0] }}</strong>. Ikuti panduan ini untuk mengenal fitur utama FamsPay — hanya butuh 30 detik.</p>
            <div class="tour-footer">
                <div class="tour-dots">
                    <div class="tour-dot active"></div>
                    <div class="tour-dot"></div>
                    <div class="tour-dot"></div>
                    <div class="tour-dot"></div>
                </div>
                <button class="tour-btn-next" style="background:#059669;color:#fff;" onclick="tourNext(2)">Mulai &rarr;</button>
            </div>
        </div>
    </div>

    {{-- Step 2: Transaksi --}}
    <div id="ts-2" class="tour-step hidden">
        <div class="tour-header" style="background:#eff6ff;">
            <div class="tour-icon-wrap" style="background:#dbeafe;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
            <div>
                <p class="tour-title">Catat Transaksi</p>
                <p class="tour-sub">Pantau setiap pemasukan dan pengeluaran.</p>
            </div>
        </div>
        <div class="tour-divider"></div>
        <div class="tour-body">
            <p class="tour-desc">Menu <strong>Transaksi</strong> di sidebar kiri adalah tempat kamu mencatat semua arus keuangan. Saldo dompet otomatis diperbarui setiap ada transaksi baru.</p>
            <div class="tour-hint" style="background:#eff6ff;color:#3b82f6;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l-4 4-4-4"/></svg>
                Lihat sidebar — menu Transaksi sudah disorot
            </div>
            <div class="tour-footer">
                <div class="tour-dots">
                    <div class="tour-dot"></div>
                    <div class="tour-dot active"></div>
                    <div class="tour-dot"></div>
                    <div class="tour-dot"></div>
                </div>
                <div style="display:flex;gap:6px;">
                    <button class="tour-btn-back" onclick="tourNext(1)">&larr; Kembali</button>
                    <button class="tour-btn-next" style="background:#3b82f6;color:#fff;" onclick="tourNext(3)">Lanjut &rarr;</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 3: Goals --}}
    <div id="ts-3" class="tour-step hidden">
        <div class="tour-header" style="background:#f5f3ff;">
            <div class="tour-icon-wrap" style="background:#ede9fe;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <p class="tour-title">Target Tabungan</p>
                <p class="tour-sub">Tetapkan tujuan keuangan keluarga.</p>
            </div>
        </div>
        <div class="tour-divider"></div>
        <div class="tour-body">
            <p class="tour-desc">Di menu <strong>Goals</strong>, buat target tabungan untuk liburan, gadget, atau kebutuhan lain. Alokasikan dana secara bertahap dan pantau progresnya.</p>
            <div class="tour-hint" style="background:#f5f3ff;color:#7c3aed;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l-4 4-4-4"/></svg>
                Lihat sidebar — menu Goals sudah disorot
            </div>
            <div class="tour-footer">
                <div class="tour-dots">
                    <div class="tour-dot"></div>
                    <div class="tour-dot"></div>
                    <div class="tour-dot active"></div>
                    <div class="tour-dot"></div>
                </div>
                <div style="display:flex;gap:6px;">
                    <button class="tour-btn-back" onclick="tourNext(2)">&larr; Kembali</button>
                    <button class="tour-btn-next" style="background:#7c3aed;color:#fff;" onclick="tourNext(4)">Lanjut &rarr;</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 4: Mulai --}}
    <div id="ts-4" class="tour-step hidden">
        <div class="tour-header" style="background:#f0fdf4;">
            <div class="tour-icon-wrap" style="background:#dcfce7;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="tour-title">Siap memulai</p>
                <p class="tour-sub">Tambahkan transaksi pertama untuk mulai melacak keuangan.</p>
            </div>
        </div>
        <div class="tour-divider"></div>
        <div class="tour-body">
            <p class="tour-desc">Klik tombol <strong>+ Tambah Transaksi</strong> di bagian Transaksi Terbaru di bawah untuk mencatat transaksi pertamamu.</p>
            <div class="tour-hint" style="background:#f0fdf4;color:#059669;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                Tombol disorot di bawah layar — scroll ke bawah
            </div>
            <div class="tour-footer">
                <div class="tour-dots">
                    <div class="tour-dot"></div>
                    <div class="tour-dot"></div>
                    <div class="tour-dot"></div>
                    <div class="tour-dot active"></div>
                </div>
                <div style="display:flex;gap:6px;">
                    <button class="tour-btn-back" onclick="tourNext(3)">&larr; Kembali</button>
                    <button class="tour-btn-next" style="background:#059669;color:#fff;" onclick="closeTour()">Mengerti</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endif

{{-- ════════════════════════════════════
     LAYOUT UTAMA
     ════════════════════════════════════ --}}
<div class="flex" style="min-height:100vh;">

    {{-- ── SIDEBAR (full height fixed) ── --}}
    <aside id="app-sidebar">
        <div class="sidebar-circle-lg"></div>
        <div class="sidebar-circle-sm"></div>
        <div class="sidebar-circle-xs"></div>

        {{-- Brand / Logo --}}
        <div class="px-5 py-5 flex items-center gap-2.5 relative z-10 flex-shrink-0">
            <div class="w-9 h-9 flex-shrink-0">
                <svg width="36" height="36" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M35.9406 51.8687C35.8334 50.465 35.5468 48.3714 34.6974 45.9918C33.5936 42.8956 32.0959 40.8796 29.6284 37.6043C28.2245 35.7407 26.2365 33.2808 23.627 30.5483C23.2358 30.0483 22.2793 28.682 22.1587 26.6874C22.0141 24.3158 23.1474 22.6367 23.493 22.1607C28.1629 17.9442 32.83 13.7304 37.4999 9.51394C38.0518 9.07277 40.3908 7.31078 43.7692 7.51666C46.7137 7.6958 48.6481 9.26261 49.2375 9.77864L58.9764 20.0298C59.314 20.3372 62.1352 22.9949 61.7789 27.0858C61.4708 30.6178 59.0166 32.6445 58.5772 32.9947C51.0326 39.2861 43.4879 45.5747 35.9433 51.866L35.9406 51.8687Z" fill="#038599"/>
                    <path d="M3.18726 29.2301C2.82289 28.5724 1.99769 26.8745 2.17452 24.6019C2.37546 22.0003 3.76061 20.2945 4.23751 19.757C8.91273 16.0272 13.5853 12.2973 18.2605 8.56743C18.8848 8.30006 20.5619 7.6851 22.6919 8.08081C24.7763 8.4685 26.0999 9.60752 26.5928 10.0781C30.25 14.1048 33.9071 18.1314 37.5642 22.1581C41.1892 26.0403 44.8115 29.9226 48.4364 33.8049C49.1813 34.214 50.3414 34.73 51.839 34.9359C54.261 35.2701 56.1901 34.607 57.093 34.2273C54.5531 36.1899 52.6026 37.9652 51.2067 39.1336C44.8865 44.4277 38.668 49.7243 32.546 55.0183C32.313 55.2109 30.1455 56.9381 27.3591 56.2349C25.2264 55.6975 24.1387 54.0906 23.8922 53.7055C16.9906 45.5453 10.0889 37.385 3.18726 29.2248V29.2301Z" fill="#03EC65"/>
                    <path opacity="0.58" d="M35.9406 51.8687C35.8334 50.465 35.5468 48.3714 34.6974 45.9918C33.5936 42.8956 32.0959 40.8796 29.6284 37.6043C28.2245 35.7407 26.2365 33.2808 23.627 30.5483C23.2358 30.0483 22.2793 28.682 22.1587 26.6874C22.0141 24.3158 23.1474 22.6367 23.493 22.1607C28.1629 17.9442 32.83 13.7304 37.4999 9.51394C38.0518 9.07277 40.3908 7.31078 43.7692 7.51666C46.7137 7.6958 48.6481 9.26261 49.2375 9.77864L58.9764 20.0298C59.314 20.3372 62.1352 22.9949 61.7789 27.0858C61.4708 30.6178 59.0166 32.6445 58.5772 32.9947C51.0326 39.2861 43.4879 45.5747 35.9433 51.866L35.9406 51.8687Z" fill="#038599"/>
                    <circle cx="9.01722" cy="25.5885" r="3.48" fill="white"/>
                </svg>
            </div>
            <span class="text-lg font-extrabold text-emerald-900 tracking-tight">Famspay</span>
        </div>

        {{-- Navigation — flex-1 agar memenuhi sisa tinggi --}}
        <nav class="flex-1 px-3 py-2 space-y-0.5 relative z-10 overflow-y-auto">
            <a href="{{ route('dashboard') }}" id="nav-dashboard" class="nav-item active flex items-center gap-3 px-3 py-2.5 rounded-2xl text-sm font-semibold">
                <svg class="w-4.5 h-4.5 w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            @if($isAdmin)
            <a href="{{ route('anggota') }}" id="nav-anggota" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-2xl text-sm font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Kelola Anggota
            </a>
            @endif

            <a href="{{ route('transaksi') }}" id="nav-transaksi" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-2xl text-sm font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Transaksi
            </a>

            <a href="{{ route('goals') }}" id="nav-goals" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-2xl text-sm font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Goals
            </a>

            <a href="{{ route('pengaturan') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-2xl text-sm font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Pengaturan
            </a>
        </nav>

        {{-- User info di bawah sidebar — flex-shrink-0 agar tidak terpotong --}}
        <div class="px-3 py-4 relative z-10 flex-shrink-0 border-t border-white/20">
            <div class="flex items-center gap-2.5 px-2">
                <div class="w-8 h-8 rounded-full bg-white/30 flex items-center justify-center text-emerald-900 font-bold text-xs flex-shrink-0 border border-white/40">
                    {{ $initials }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-emerald-900 truncate">{{ $user['full_name'] ?? 'Pengguna' }}</p>
                    <p class="text-[10px] text-emerald-700 truncate">{{ $user['email'] ?? '' }}</p>
                </div>
                <form method="POST" action="/logout" class="inline">
                    @csrf
                    <button type="submit" class="text-emerald-700 hover:text-rose-600 transition-colors" title="Keluar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ── MAIN CONTENT ── --}}
    <div id="main-wrapper" class="flex-1 flex flex-col min-h-screen">

        {{-- Header --}}
        <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-slate-100 px-7 py-4 flex justify-between items-center relative">
            <div>
                <h1 class="text-base font-bold text-slate-800">Selamat Datang, {{ explode(' ', $user['full_name'] ?? 'Pengguna')[0] }}! 👋</h1>
                <p class="text-xs text-slate-400">Saatnya kelola keuanganmu sekarang juga.</p>
            </div>
            <div class="flex items-center gap-2.5">
                {{-- Search Button --}}
                <button id="btn-search" onclick="toggleSearch()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-emerald-50 flex items-center justify-center text-slate-500 hover:text-emerald-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
                {{-- Notification Button --}}
                <button id="btn-notif" onclick="toggleNotif()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-emerald-50 flex items-center justify-center text-slate-500 hover:text-emerald-600 transition-colors relative">
                    <svg id="notif-icon-off" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span id="notif-dot" class="hidden absolute top-1.5 right-1.5 w-2 h-2 bg-emerald-500 rounded-full"></span>
                </button>
                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center text-white font-bold text-xs">
                        {{ $initials }}
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-700 leading-tight">{{ $user['full_name'] ?? 'Pengguna' }}</p>
                        <p class="text-[10px] text-slate-400 leading-tight">{{ $user['email'] ?? '' }}</p>
                    </div>
                </div>
            </div>

            {{-- Search Dropdown --}}
            <div id="search-dropdown" class="hidden absolute top-full right-0 mt-2 w-72 bg-white rounded-2xl shadow-xl border border-slate-100 z-50 overflow-hidden" style="right:24px;">
                <div class="p-3">
                    <div class="flex items-center gap-2 bg-slate-50 rounded-xl px-3 py-2">
                        <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input id="search-input" type="text" placeholder="Cari halaman atau fitur..." oninput="runSearch(this.value)"
                            class="flex-1 bg-transparent border-none outline-none text-sm text-slate-700 placeholder-slate-400">
                    </div>
                </div>
                <div id="search-results" class="pb-2"></div>
            </div>

            {{-- Notif Toast --}}
            <div id="notif-toast" class="hidden absolute top-full right-0 mt-2 w-72 bg-white rounded-2xl shadow-xl border border-slate-100 z-50 p-4" style="right:24px;">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-bold text-slate-700">Notifikasi</p>
                    <span id="notif-status-badge" class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full">Nonaktif</span>
                </div>
                <div id="notif-content"></div>
                <button id="btn-enable-notif" onclick="enableNotifications()" class="w-full mt-3 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold rounded-xl transition-colors">
                    Aktifkan Notifikasi
                </button>
            </div>
        </header>

        <main class="flex-1 px-7 py-6 space-y-5">

            {{-- ── Admin: peringatan anggota over-limit ── --}}
            @if($isAdmin && !empty($membersData))
            @php
                $overLimitMembers = [];
                foreach($membersData as $m) {
                    // We don't have limit data in UserResource, so just flag if member role
                    // Real check happens in kelola-anggota. Show placeholder if family budget shows issue.
                }
            @endphp
            @endif

            {{-- ── ROW 1: Wallet + Net Flow (kiri) | Stats 2x2 (kanan) ── --}}
            <div class="flex gap-5 items-start">

                {{-- KIRI: Dompet + Net Flow + Limit Terpakai --}}
                <div class="flex-shrink-0 pt-3" style="width:42%;">

                    {{-- Wallet — aspect-ratio dikunci, JANGAN DIUBAH --}}
                    <div class="wallet-physical dompet-admin">
                        <div class="card-stack-1"></div>
                        <div class="card-stack-2"></div>
                        <div class="wallet-body text-white">
                            <a href="{{ route('transaksi') }}" class="wallet-arrow" title="Lihat Transaksi" style="text-decoration:none;">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H7M17 7v10"/>
                                </svg>
                            </a>
                            @if($limitBase > 0 && $usedLimit >= $limitBase)
                            <div class="mb-1 bg-rose-400/30 border border-rose-300/50 rounded-lg px-2 py-1 flex items-center gap-1.5">
                                <svg class="w-3 h-3 text-rose-200 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span class="text-[9px] text-rose-100 font-semibold">Kamu telah melewati limit bulanan!</span>
                            </div>
                            @endif
                            <p class="text-white/75 text-[10px] font-semibold uppercase tracking-widest mb-1">Sisa limit bulan ini (IDR):</p>
                            <p class="text-2xl font-extrabold tracking-tight mb-3">{{ $limitBase > 0 ? fmtRp($remainingLimit) : 'Rp0' }}</p>
                            <div class="flex gap-3">
                                <div class="flex-1">
                                    <p class="text-white/60 text-[9px] uppercase tracking-wider mb-1">Limit Bulanan</p>
                                    <div class="wallet-slot flex items-center px-3">
                                        <span class="text-white text-[11px] font-bold">{{ $limitBase > 0 ? fmtRp($limitBase) : 'Belum diset' }}</span>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-white/60 text-[9px] uppercase tracking-wider mb-1">Terpakai</p>
                                    <div class="wallet-slot flex items-center px-3">
                                        <span class="text-white text-[11px] font-bold">{{ fmtRp($usedLimit) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- KANAN: Stats 2x2 — flex-1 mengisi sisa ruang secara responsive --}}
                <div class="flex-1 grid grid-cols-2 gap-3">
                    <div class="stat-card px-4 py-4">
                        <p class="text-slate-400 text-xs font-medium mb-1">Saldo dapat dipakai</p>
                        <p class="text-slate-800 text-xl font-extrabold">{{ $limitBase > 0 ? fmtRp($remainingLimit) : fmtRp($walletBalance) }}</p>
                        <div class="flex items-center gap-1 mt-1">
                            <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            <span class="text-[10px] text-emerald-500 font-medium">{{ $limitBase > 0 ? 'Sisa limit bulan ini' : 'Saldo aktif' }}</span>
                        </div>
                    </div>
                    <div class="stat-card px-4 py-4">
                        <p class="text-slate-400 text-xs font-medium mb-1">Pemasukan</p>
                        <p class="text-slate-800 text-xl font-extrabold">{{ fmtRp($monthlyIncome) }}</p>
                        <div class="flex items-center gap-1 mt-1">
                            <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            <span class="text-[10px] text-emerald-500 font-medium">Bulan ini</span>
                        </div>
                    </div>
                    <div class="stat-card px-4 py-4">
                        <p class="text-slate-400 text-xs font-medium mb-1">Pengeluaran</p>
                        <p class="text-slate-800 text-xl font-extrabold">{{ fmtRp($monthlyExpense) }}</p>
                        <div class="flex items-center gap-1 mt-1">
                            <svg class="w-3 h-3 text-rose-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            <span class="text-[10px] text-rose-400 font-medium">Bulan ini</span>
                        </div>
                    </div>
                    <div class="stat-card px-4 py-4">
                        <p class="text-slate-400 text-xs font-medium mb-1">Total Tabungan</p>
                        <p class="text-slate-800 text-xl font-extrabold">{{ fmtRp($totalSavings) }}</p>
                        <div class="flex items-center gap-1 mt-1">
                            <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            <span class="text-[10px] text-emerald-500 font-medium">Total terkumpul</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── ROW 3: Money Flow + Budget ── --}}
            <div class="grid grid-cols-3 gap-5">
                {{-- Money Flow — data per hari dari backend --}}
                <div class="col-span-2 bg-white rounded-2xl border border-slate-100 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-slate-700 text-sm">Money Flow</h3>
                        <div class="flex items-center gap-4 text-xs text-slate-500">
                            <span class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>Pemasukan
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-200 inline-block"></span>Pengeluaran
                            </span>
                        </div>
                    </div>

                    {{-- Y-axis label + bar chart --}}
                    <div class="flex gap-2" style="height:140px;">
                        {{-- Y-axis --}}
                        <div class="flex flex-col justify-between text-right pr-2" style="width:48px;">
                            @php
                                $yLabels = [500, 400, 300, 200, 100, 0];
                                // Sesuaikan label dengan maxFlow
                                $yMax = $maxFlow > 0 ? ceil($maxFlow/100000)*100000 : 500000;
                                $yStep = $yMax / 5;
                            @endphp
                            @for($yi = 5; $yi >= 0; $yi--)
                            <span class="text-[9px] text-slate-300 font-medium leading-none">
                                @php $yVal = $yi * $yStep; @endphp
                                {{ $yVal >= 1000000 ? round($yVal/1000000,1).'jt' : ($yVal >= 1000 ? round($yVal/1000).'rb' : $yVal) }}
                            </span>
                            @endfor
                        </div>
                        {{-- Bars --}}
                        <div class="flex-1 flex items-end">
                            <div class="w-full flex items-end gap-0" style="height:120px;">
                                @foreach($dayNames as $di => $day)
                                @php
                                    $iH = $maxFlow > 0 ? max(2, round(($incPerDay[$di] / $maxFlow) * 100)) : 2;
                                    $eH = $maxFlow > 0 ? max(2, round(($expPerDay[$di] / $maxFlow) * 100)) : 2;
                                    $iTip = 'Rp'.number_format($incPerDay[$di],0,',','.');
                                    $eTip = 'Rp'.number_format($expPerDay[$di],0,',','.');
                                    $isToday = ($di === $todayDow);
                                    $incColor = $isToday ? 'bg-emerald-600' : 'bg-emerald-500';
                                    $expColor = $isToday ? 'bg-emerald-400' : 'bg-emerald-200';
                                    $dayLabel = $isToday ? '<span style="color:#059669;font-weight:700;">'.$day.'</span>' : $day;
                                @endphp
                                <div class="flex-1 flex flex-col items-center gap-0">
                                    <div class="w-full flex items-end justify-center gap-1" style="height:110px;">
                                        <div class="chart-bar {{ $incColor }} w-full max-w-[14px] {{ $isToday ? 'ring-1 ring-emerald-600 ring-offset-1' : '' }}"
                                             style="height:{{ $iH }}%;"
                                             data-tip="Masuk: {{ $iTip }}"></div>
                                        <div class="chart-bar {{ $expColor }} w-full max-w-[14px]"
                                             style="height:{{ $eH }}%;"
                                             data-tip="Keluar: {{ $eTip }}"></div>
                                    </div>
                                    <p class="text-[9px] text-slate-400 font-medium mt-1.5">{!! $dayLabel !!}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Budget Donut — kategori real dari transaksi bulan ini --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex flex-col">
                    <h3 class="font-bold text-slate-700 text-sm mb-3">Budget</h3>
                    <div class="flex-1 flex flex-col items-center justify-center">
                        <div class="relative w-28 h-28">
                            <svg viewBox="0 0 36 36" class="w-28 h-28 -rotate-90">
                                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f1f5f9" stroke-width="3.8"/>
                                @if(!empty($catChartData))
                                @php $offset2 = 0; @endphp
                                @foreach($catChartData as $cd)
                                <circle cx="18" cy="18" r="15.9" fill="none"
                                    stroke="{{ $cd['color'] }}" stroke-width="3.8"
                                    stroke-dasharray="{{ $cd['pct'] }} {{ 100 - $cd['pct'] }}"
                                    stroke-dashoffset="{{ -$offset2 }}"
                                    stroke-linecap="round"/>
                                @php $offset2 += $cd['pct']; @endphp
                                @endforeach
                                @else
                                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e2e8f0" stroke-width="3.8"
                                    stroke-dasharray="100 0"/>
                                @endif
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <p class="text-[9px] text-slate-400 font-medium">Total</p>
                                <p class="text-xs font-extrabold text-slate-700">{{ fmtRp($monthlyExpense) }}</p>
                            </div>
                        </div>
                    </div>
                    @if(!empty($catChartData))
                    <div class="mt-3 space-y-1.5">
                        @foreach(array_slice($catChartData, 0, 5) as $cd)
                        <div class="flex items-center justify-between text-xs text-slate-600">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:{{ $cd['color'] }}"></span>
                                {{ $cd['name'] }}
                            </div>
                            <span class="text-[10px] text-slate-400 font-medium">{{ $cd['pct'] }}%</span>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-center text-slate-400 text-xs mt-3">Belum ada pengeluaran bulan ini</p>
                    @endif
                </div>
            </div>

            {{-- ── ROW 4: Transaksi Terbaru + Target/Anggota ── --}}
            <div class="grid grid-cols-2 gap-5">

                {{-- Transaksi Terbaru --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-slate-700 text-sm">Transaksi Terbaru</h3>
                        <a href="{{ route('transaksi') }}" class="text-xs text-emerald-600 font-semibold hover:text-emerald-700 flex items-center gap-1">
                            Lihat Semua
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>

                    @if(empty($recentTxns))
                    <div class="py-8 flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center mb-3">
                            <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <p class="text-slate-500 text-sm font-medium">Belum ada transaksi</p>
                        <p class="text-slate-400 text-xs mt-1">Tambah transaksi pertamamu sekarang!</p>
                        <a id="btn-tambah-transaksi" href="{{ route('transaksi') }}" class="mt-3 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded-xl transition-colors">
                            + Tambah Transaksi
                        </a>
                    </div>
                    @else
                    <div class="overflow-hidden rounded-xl border border-slate-100">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-emerald-50">
                                    <th class="text-left text-emerald-700 text-xs font-semibold px-4 py-2.5">Pembayaran</th>
                                    <th class="text-left text-emerald-700 text-xs font-semibold px-3 py-2.5">Kategori</th>
                                    <th class="text-right text-emerald-700 text-xs font-semibold px-4 py-2.5">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach(array_slice($recentTxns, 0, 5) as $txn)
                                @php
                                    $isIncome = ($txn['type'] ?? '') === 'income';
                                    $amount   = fmtRp(abs($txn['amount'] ?? 0));
                                    $label    = $txn['note'] ?: ($txn['category_name'] ?? 'Transaksi');
                                    $cat      = $txn['category_name'] ?? '-';
                                    $date     = $txn['txn_date'] ? \Carbon\Carbon::parse($txn['txn_date'])->format('d M Y') : '-';
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl {{ $isIncome ? 'bg-emerald-100' : 'bg-rose-100' }} flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4 {{ $isIncome ? 'text-emerald-600' : 'text-rose-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    @if($isIncome)
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                                    @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
                                                    @endif
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-slate-700 text-xs">{{ Str::limit($label, 20) }}</p>
                                                <p class="text-slate-400 text-[10px]">{{ $date }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="text-xs text-emerald-600 font-medium">{{ $cat }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="text-xs font-bold {{ $isIncome ? 'text-emerald-600' : 'text-rose-500' }}">
                                            {{ $isIncome ? '+' : '-' }}{{ $amount }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

                {{-- Admin: Daftar Anggota (dompet fisik per member) | Member: Target Tabungan --}}
                @if($isAdmin && !empty($membersData))
                <div class="bg-white rounded-2xl border border-slate-100 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-slate-700 text-sm">Anggota Keluarga</h3>
                        <a href="{{ route('anggota') }}" class="text-xs text-emerald-600 font-semibold hover:text-emerald-700 flex items-center gap-1">
                            Kelola
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($membersData as $mi => $member)
                        @php
                            $dColor  = $dompetColors[$mi % count($dompetColors)];
                            $mInit   = strtoupper(substr($member['full_name'] ?? 'A', 0, 1));
                            $mBal    = $member['wallet_balance'] ?? 0;
                            $mName   = $member['full_name'] ?? '-';
                        @endphp
                        {{-- Mini dompet fisik per member — lebih besar, dengan dekorasi --}}
                        <a href="{{ route('anggota') }}" class="wallet-physical {{ $dColor }} mt-3 block" style="text-decoration:none;">
                            <div class="card-stack-1"></div>
                            <div class="card-stack-2"></div>
                            <div class="wallet-body text-white" style="padding:12px 14px 12px; position:relative;">
                                {{-- Decorative dots --}}
                                <div style="position:absolute;bottom:8px;right:8px;display:flex;gap:3px;opacity:0.25;">
                                    <div style="width:5px;height:5px;border-radius:50%;background:white;"></div>
                                    <div style="width:5px;height:5px;border-radius:50%;background:white;"></div>
                                    <div style="width:5px;height:5px;border-radius:50%;background:white;"></div>
                                </div>
                                {{-- Arrow --}}
                                <div style="position:absolute;top:10px;right:10px;width:22px;height:22px;background:rgba(255,255,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H7M17 7v10"/>
                                    </svg>
                                </div>
                                {{-- Avatar + name --}}
                                <div class="flex items-center gap-1.5 mb-1">
                                    <div style="width:20px;height:20px;border-radius:50%;background:rgba(255,255,255,0.25);display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;flex-shrink:0;">{{ $mInit }}</div>
                                    <p class="text-white/70 text-[9px] uppercase tracking-wider">Dompet milik:</p>
                                </div>
                                <p class="font-extrabold text-sm leading-tight pr-7 mb-1">{{ Str::limit($mName, 16) }}</p>
                                <p class="text-white/80 text-xs font-semibold mb-2">{{ fmtRp($mBal) }}</p>
                                {{-- Limit bar --}}
                                <div style="height:3px;background:rgba(255,255,255,0.2);border-radius:4px;overflow:hidden;">
                                    <div style="height:100%;background:rgba(255,255,255,0.7);width:{{ min(100, ($mBal > 0 ? 60 : 5)) }}%;border-radius:4px;"></div>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>

                @else
                {{-- MEMBER: Target Tabungan — real data --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-slate-700 text-sm">Target Tabungan</h3>
                        <a href="{{ route('goals') }}" class="text-xs text-emerald-600 font-semibold hover:text-emerald-700">Lihat Semua</a>
                    </div>
                    @if(empty($goalsList))
                    <div class="py-8 flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center mb-3">
                            <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <p class="text-slate-500 text-sm font-medium">Belum ada target tabungan</p>
                        <p class="text-slate-400 text-xs mt-1">Buat target impianmu sekarang!</p>
                        <a id="btn-tambah-target" href="{{ route('goals') }}" class="mt-3 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded-xl transition-colors">
                            + Tambah Target
                        </a>
                    </div>
                    @else
                    <div class="space-y-3">
                        @php
                            $goalProgressColors = ['bg-emerald-500','bg-blue-500','bg-purple-500','bg-amber-500','bg-teal-500','bg-rose-500'];
                        @endphp
                        @foreach(array_slice($goalsList, 0, 4) as $gi => $gl)
                        @php
                            $gCollected = $gl['current_amount'] ?? 0;
                            $gTarget    = $gl['target_amount']  ?? 1;
                            $gPct       = $gTarget > 0 ? min(100, round($gCollected / $gTarget * 100)) : 0;
                            $gBarColor  = $goalProgressColors[$gi % count($goalProgressColors)];
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-xs font-semibold text-slate-700 truncate max-w-[65%]">{{ $gl['title'] }}</p>
                                <span class="text-[10px] font-bold text-emerald-600">{{ $gPct }}%</span>
                            </div>
                            <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden mb-1">
                                <div class="{{ $gBarColor }} h-full rounded-full transition-all duration-700" style="width:{{ $gPct }}%"></div>
                            </div>
                            <p class="text-[10px] text-slate-400">{{ fmtRp($gCollected) }} / {{ fmtRp($gTarget) }}</p>
                        </div>
                        @endforeach
                        @if(count($goalsList) > 4)
                        <p class="text-xs text-slate-400 text-center pt-1">+{{ count($goalsList) - 4 }} target lainnya</p>
                        @endif
                    </div>
                    @endif
                </div>
                @endif
            </div>

            {{-- ── ROW 5 (Admin): Ringkasan Keluarga ── --}}
            @if($isAdmin && !empty($familyData))
            @php
                $famIncome      = $familyData['current_month']['income']          ?? 0;
                $famExpense     = $familyData['current_month']['expense']         ?? 0;
                $famBalance     = $familyData['total_family_wallet_balance']      ?? 0;
                $famSavings     = $familyData['total_family_savings']             ?? 0;
                $membersSummary = $familyData['members_summary']                  ?? [];
            @endphp
            <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 text-white">
                <h3 class="font-bold text-slate-100 mb-4 flex items-center gap-2 text-sm">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Ringkasan Keluarga (Bulan Ini)
                </h3>
                <div class="grid grid-cols-4 gap-4">
                    <div class="bg-white/10 rounded-xl px-4 py-3">
                        <p class="text-slate-300 text-xs">Total Saldo Keluarga</p>
                        <p class="font-extrabold text-base mt-0.5 text-emerald-300">{{ fmtRp($famBalance) }}</p>
                    </div>
                    <div class="bg-white/10 rounded-xl px-4 py-3">
                        <p class="text-slate-300 text-xs">Pemasukan Keluarga</p>
                        <p class="font-extrabold text-base mt-0.5 text-blue-300">{{ fmtRp($famIncome) }}</p>
                    </div>
                    <div class="bg-white/10 rounded-xl px-4 py-3">
                        <p class="text-slate-300 text-xs">Pengeluaran Keluarga</p>
                        <p class="font-extrabold text-base mt-0.5 text-rose-300">{{ fmtRp($famExpense) }}</p>
                    </div>
                    <div class="bg-white/10 rounded-xl px-4 py-3">
                        <p class="text-slate-300 text-xs">Total Tabungan Keluarga</p>
                        <p class="font-extrabold text-base mt-0.5 text-amber-300">{{ fmtRp($famSavings) }}</p>
                    </div>
                </div>
                @if(!empty($membersSummary))
                <div class="mt-4 pt-4 border-t border-white/10">
                    <p class="text-slate-400 text-xs font-medium mb-3">Distribusi per Anggota</p>
                    <div class="flex flex-wrap gap-3">
                        @foreach($membersSummary as $ms)
                        <div class="flex items-center gap-2 bg-white/10 rounded-xl px-3 py-2">
                            <div class="w-7 h-7 rounded-full bg-emerald-400/30 flex items-center justify-center text-xs font-bold text-emerald-200">
                                {{ strtoupper(substr($ms['full_name'] ?? 'A', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-white">{{ $ms['full_name'] ?? '-' }}</p>
                                <p class="text-[10px] text-slate-400">{{ fmtRp($ms['wallet_balance'] ?? 0) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endif

        </main>

        <footer class="px-7 py-4 border-t border-slate-100">
            <p class="text-slate-400 text-xs">© 2026 FamsPay • Smart Family Finance</p>
        </footer>
    </div>
</div>

{{-- ── SCRIPTS ── --}}
<script>
// ─────────────────────────────────────────
// SEARCH
// ─────────────────────────────────────────
const SEARCH_ROUTES = [
    { label: 'Dashboard',       icon: '🏠', url: '{{ route("dashboard") }}',   keywords: ['dashboard','beranda','home'] },
    { label: 'Transaksi',       icon: '💸', url: '{{ route("transaksi") }}',   keywords: ['transaksi','pembayaran','transfer','pengeluaran','pemasukan'] },
    { label: 'Goals / Target',  icon: '🎯', url: '{{ route("goals") }}',       keywords: ['goals','target','tabungan','saving'] },
    { label: 'Pengaturan',      icon: '⚙️',  url: '{{ route("pengaturan") }}', keywords: ['pengaturan','profil','akun','password','setting'] },
    @if($isAdmin)
    { label: 'Kelola Anggota',  icon: '👥', url: '{{ route("anggota") }}',     keywords: ['anggota','member','keluarga','limit','kelola'] },
    @endif
];

function toggleSearch() {
    const dd = document.getElementById('search-dropdown');
    const nt = document.getElementById('notif-toast');
    nt.classList.add('hidden');
    dd.classList.toggle('hidden');
    if (!dd.classList.contains('hidden')) {
        setTimeout(() => document.getElementById('search-input').focus(), 50);
        document.getElementById('search-results').innerHTML = renderSearchItems(SEARCH_ROUTES);
    }
}

function runSearch(q) {
    const query = q.toLowerCase().trim();
    const filtered = query ? SEARCH_ROUTES.filter(r =>
        r.label.toLowerCase().includes(query) || r.keywords.some(k => k.includes(query))
    ) : SEARCH_ROUTES;
    document.getElementById('search-results').innerHTML = renderSearchItems(filtered);
}

function renderSearchItems(items) {
    if (!items.length) return '<p class="text-center text-xs text-slate-400 py-3">Tidak ditemukan</p>';
    return items.map(r => `
        <a href="${r.url}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-emerald-50 transition-colors">
            <span class="text-base">${r.icon}</span>
            <span class="text-sm font-medium text-slate-700">${r.label}</span>
        </a>`).join('');
}

// ─────────────────────────────────────────
// NOTIFICATIONS
// ─────────────────────────────────────────
let notifEnabled = false;
try { notifEnabled = localStorage.getItem('famspay_notif') === '1'; } catch(e){}

function toggleNotif() {
    const toast = document.getElementById('notif-toast');
    const dd    = document.getElementById('search-dropdown');
    dd.classList.add('hidden');
    toast.classList.toggle('hidden');
    if (!toast.classList.contains('hidden')) renderNotifContent();
}

function renderNotifContent() {
    const badge   = document.getElementById('notif-status-badge');
    const content = document.getElementById('notif-content');
    const btn     = document.getElementById('btn-enable-notif');
    const dot     = document.getElementById('notif-dot');

    if (notifEnabled) {
        badge.textContent = 'Aktif';
        badge.className   = 'text-[10px] bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded-full';
        dot.classList.remove('hidden');
        btn.textContent   = 'Nonaktifkan Notifikasi';
        btn.className     = btn.className.replace('bg-emerald-500 hover:bg-emerald-600','bg-slate-200 hover:bg-slate-300 text-slate-700');
        @if($limitBase > 0 && $usedLimit >= $limitBase)
        content.innerHTML = `<div class="bg-rose-50 border border-rose-100 rounded-xl px-3 py-2.5 text-xs text-rose-600 font-medium mb-2">⚠️ Limit bulanan Anda sudah terlampaui!</div>`;
        @elseif($limitBase > 0)
        const pctUsed = {{ $limitBase > 0 ? round($usedLimit/$limitBase*100) : 0 }};
        if (pctUsed >= 80) {
            content.innerHTML = `<div class="bg-amber-50 border border-amber-100 rounded-xl px-3 py-2.5 text-xs text-amber-700 font-medium mb-2">⚠️ Anda telah menggunakan ${pctUsed}% dari limit bulanan.</div>`;
        } else {
            content.innerHTML = `<p class="text-xs text-slate-400 py-2 text-center">Tidak ada notifikasi baru.</p>`;
        }
        @else
        content.innerHTML = `<p class="text-xs text-slate-400 py-2 text-center">Belum ada limit yang diset admin.</p>`;
        @endif
    } else {
        badge.textContent = 'Nonaktif';
        badge.className   = 'text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full';
        dot.classList.add('hidden');
        btn.textContent = 'Aktifkan Notifikasi';
        btn.className = 'w-full mt-3 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold rounded-xl transition-colors';
        content.innerHTML = `<p class="text-xs text-slate-400 py-2 text-center">Aktifkan notifikasi untuk peringatan limit.</p>`;
    }
}

function enableNotifications() {
    notifEnabled = !notifEnabled;
    try { localStorage.setItem('famspay_notif', notifEnabled ? '1' : '0'); } catch(e){}
    renderNotifContent();
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('#btn-search') && !e.target.closest('#search-dropdown')) {
        document.getElementById('search-dropdown').classList.add('hidden');
    }
    if (!e.target.closest('#btn-notif') && !e.target.closest('#notif-toast')) {
        document.getElementById('notif-toast').classList.add('hidden');
    }
});

// Init notif dot state
if (notifEnabled) {
    @if($limitBase > 0 && $usedLimit >= $limitBase)
    document.getElementById('notif-dot').classList.remove('hidden');
    @endif
}
</script>

</body>
</html>
