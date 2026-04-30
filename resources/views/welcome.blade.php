{{-- 
    welcome.blade.php — Sprint 1 Component Testing
    Memanggil layout via x-layouts.app (folder: components/layouts/app.blade.php)
--}}

<x-layouts.app>
    <x-slot:title>FamsPay — Sprint 1 Component Test</x-slot:title>
    <x-slot:pageTitle>Testing Komponen</x-slot:pageTitle>
    <x-slot:pageSubtitle>Sprint 1 — W-01 Setup Laravel Blade + TailwindCSS</x-slot:pageSubtitle>

    <div class="space-y-10 max-w-4xl">

        {{-- ─────────────────────────────────────────── --}}
        {{-- 1. ALERT COMPONENTS --}}
        {{-- ─────────────────────────────────────────── --}}
        <section>
            <h2 class="text-xs font-bold uppercase tracking-widest text-emerald-500 mb-1">Komponen</h2>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-4">1. Alert</h3>
            <div class="space-y-3">
                <x-alert type="success">
                    <strong>Berhasil!</strong> Transaksi sebesar Rp 150.000 berhasil disimpan.
                </x-alert>

                <x-alert type="warning">
                    <strong>Peringatan!</strong> Pengeluaran bulan ini sudah mencapai 90% dari limit Rp 3.000.000.
                </x-alert>

                <x-alert type="danger">
                    <strong>Saldo Negatif!</strong> Transaksi terakhir membuat saldo dompet Anda menjadi negatif.
                </x-alert>

                <x-alert type="info">
                    <strong>Info.</strong> Fitur transfer antar anggota keluarga akan tersedia di Sprint 2.
                </x-alert>
            </div>
        </section>

        {{-- ─────────────────────────────────────────── --}}
        {{-- 2. CARD + INPUT COMPONENTS --}}
        {{-- ─────────────────────────────────────────── --}}
        <section>
            <h2 class="text-xs font-bold uppercase tracking-widest text-emerald-500 mb-1">Komponen</h2>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-4">2. Card &amp; Input</h3>

            <x-card>
                <h4 class="text-base font-bold text-slate-700 mb-5">Form Tambah Transaksi</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <x-input
                        label="Nama Transaksi"
                        name="title"
                        placeholder="Contoh: Beli Kopi"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>'
                    />
                    <x-input
                        label="Nominal (Rp)"
                        name="amount"
                        type="number"
                        placeholder="0"
                        icon='<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                    />
                    <x-input
                        label="Kategori"
                        name="category"
                        placeholder="Pilih kategori"
                    />
                    <x-input
                        label="Tanggal"
                        name="date"
                        type="date"
                    />
                    <x-input
                        label="Kolom Error (contoh validasi)"
                        name="error_field"
                        placeholder="Isi field ini"
                        error="Kolom ini wajib diisi."
                    />
                    <x-input
                        label="Kolom Dengan Hint"
                        name="hint_field"
                        placeholder="Masukkan deskripsi"
                        hint="Maksimal 100 karakter."
                    />
                </div>
                <div class="mt-6 flex items-center gap-3">
                    <x-button variant="primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Transaksi
                    </x-button>
                    <x-button variant="secondary">Batal</x-button>
                </div>
            </x-card>
        </section>

        {{-- ─────────────────────────────────────────── --}}
        {{-- 3. BUTTON VARIANTS --}}
        {{-- ─────────────────────────────────────────── --}}
        <section>
            <h2 class="text-xs font-bold uppercase tracking-widest text-emerald-500 mb-1">Komponen</h2>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-4">3. Button Variants</h3>

            <x-card>
                <div class="flex flex-wrap gap-3">
                    <x-button variant="primary">Primary</x-button>
                    <x-button variant="secondary">Secondary</x-button>
                    <x-button variant="danger">Danger</x-button>
                    <x-button variant="outline">Outline</x-button>
                    <x-button variant="ghost">Ghost</x-button>
                </div>
            </x-card>
        </section>

        {{-- ─────────────────────────────────────────── --}}
        {{-- 4. MODAL COMPONENT --}}
        {{-- ─────────────────────────────────────────── --}}
        <section>
            <h2 class="text-xs font-bold uppercase tracking-widest text-emerald-500 mb-1">Komponen</h2>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-4">4. Modal</h3>

            <x-card class="flex flex-col items-center justify-center py-12 text-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center">
                    <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-700 text-sm">Uji Interaksi Modal</p>
                    <p class="text-slate-400 text-xs mt-1">Klik tombol di bawah untuk membuka konfirmasi modal</p>
                </div>
                <div class="flex gap-3">
                    <x-button variant="primary" onclick="document.getElementById('test-modal-confirm').classList.remove('hidden')">
                        Buka Modal Konfirmasi
                    </x-button>
                    <x-button variant="outline" onclick="document.getElementById('test-modal-delete').classList.remove('hidden')">
                        Modal Hapus
                    </x-button>
                </div>
            </x-card>

            {{-- Modal: Konfirmasi Simpan --}}
            <x-modal id="test-modal-confirm" title="Konfirmasi Simpan Transaksi">
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-700 text-sm">Apakah Anda yakin?</p>
                        <p class="mt-0.5">Tindakan ini akan memotong saldo dompet personal Anda sebesar <strong class="text-slate-700">Rp 150.000</strong>. Transaksi yang tersimpan dapat diedit kembali.</p>
                    </div>
                </div>

                <x-slot:footer>
                    <x-button variant="secondary" class="flex-1" onclick="document.getElementById('test-modal-confirm').classList.add('hidden')">
                        Kembali
                    </x-button>
                    <x-button variant="primary" class="flex-1">
                        Ya, Simpan
                    </x-button>
                </x-slot:footer>
            </x-modal>

            {{-- Modal: Konfirmasi Hapus --}}
            <x-modal id="test-modal-delete" title="Hapus Transaksi">
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-10 h-10 rounded-2xl bg-rose-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-700 text-sm">Hapus transaksi ini?</p>
                        <p class="mt-0.5">Transaksi <strong class="text-slate-700">"Beli Kopi"</strong> akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>

                <x-slot:footer>
                    <x-button variant="secondary" class="flex-1" onclick="document.getElementById('test-modal-delete').classList.add('hidden')">
                        Batal
                    </x-button>
                    <x-button variant="danger" class="flex-1">
                        Ya, Hapus
                    </x-button>
                </x-slot:footer>
            </x-modal>
        </section>

        {{-- ─────────────────────────────────────────── --}}
        {{-- 5. BADGE / STATUS --}}
        {{-- ─────────────────────────────────────────── --}}
        <section>
            <h2 class="text-xs font-bold uppercase tracking-widest text-emerald-500 mb-1">Bonus</h2>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-4">5. Color Palette Preview</h3>
            <x-card>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @php
                        $palette = [
                            ['#047857', 'emerald-800'],
                            ['#059669', 'emerald-600'],
                            ['#10B981', 'emerald-500 ✓ Primary'],
                            ['#34D399', 'emerald-400'],
                            ['#6EE7B7', 'emerald-300'],
                            ['#A7F3D0', 'emerald-200'],
                            ['#D1FAE5', 'emerald-100'],
                            ['#ECFDF5', 'emerald-50'],
                        ];
                    @endphp
                    @foreach($palette as [$hex, $label])
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl border border-slate-200 shadow-sm flex-shrink-0" style="background-color: {{ $hex }}"></div>
                            <div>
                                <p class="text-xs font-bold text-slate-700">{{ $hex }}</p>
                                <p class="text-[10px] text-slate-400">{{ $label }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>
        </section>

    </div>

    {{-- Modal close on backdrop click --}}
    <script>
        ['test-modal-confirm','test-modal-delete'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('click', function(e) {
                    if (e.target === el) el.classList.add('hidden');
                });
            }
        });
    </script>

</x-layouts.app>
