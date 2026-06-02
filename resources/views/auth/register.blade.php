{{-- resources/views/auth/register.blade.php --}}
<x-layouts.guest>
    <x-slot:title>FamsPay - Daftar Akun</x-slot:title>

    <div style="padding: 32px 32px 28px;">

        {{-- Logo --}}
        <div style="display: flex; justify-content: center; margin-bottom: 14px;">
            <div style="width: 56px; height: 56px; border-radius: 14px; background: #F1F5F9; display: flex; align-items: center; justify-content: center;">
                <img src="{{ asset('images/Frame_252.png') }}" alt="FamsPay Logo"
                    style="width: 36px; height: 36px; object-fit: contain;">
            </div>
        </div>

        {{-- Heading --}}
        <div style="text-align: center; margin-bottom: 22px;">
            <h1 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0 0 6px;">Daftar Akun</h1>
            <p style="font-size: 0.8rem; color: #94a3b8; line-height: 1.5; margin: 0;">
                Halo! Senang melihatmu kembali.<br>Silakan buat akun Anda.
            </p>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:10px 14px;margin-bottom:14px;">
                @foreach ($errors->all() as $error)
                    <div style="font-size:0.78rem;color:#DC2626;font-weight:500;margin-bottom:2px;">• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('register') }}" method="POST">
            @csrf

            {{-- Nama --}}
            <div style="display:flex;align-items:center;gap:10px;background:#F1F5F9;border-radius:12px;padding:11px 14px;margin-bottom:10px;border:2px solid {{ $errors->has('name') ? '#FECACA' : 'transparent' }};">
                <svg style="width:17px;height:17px;color:#94a3b8;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <input type="text" name="name" placeholder="Nama" value="{{ old('name') }}" autocomplete="name"
                    style="flex:1;background:transparent;border:none;outline:none;font-size:0.825rem;color:#334155;font-family:inherit;">
            </div>

            {{-- Email --}}
            <div style="display:flex;align-items:center;gap:10px;background:#F1F5F9;border-radius:12px;padding:11px 14px;margin-bottom:10px;border:2px solid {{ $errors->has('email') ? '#FECACA' : 'transparent' }};">
                <svg style="width:17px;height:17px;color:#94a3b8;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" autocomplete="email"
                    style="flex:1;background:transparent;border:none;outline:none;font-size:0.825rem;color:#334155;font-family:inherit;">
            </div>

            {{-- Password --}}
            <div style="display:flex;align-items:center;gap:10px;background:#F1F5F9;border-radius:12px;padding:11px 14px;margin-bottom:18px;border:2px solid {{ $errors->has('password') ? '#FECACA' : 'transparent' }};">
                <svg style="width:17px;height:17px;color:#94a3b8;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <input type="password" name="password" id="password" placeholder="Password" autocomplete="new-password"
                    style="flex:1;background:transparent;border:none;outline:none;font-size:0.825rem;color:#334155;font-family:inherit;">
                <button type="button" onclick="togglePassword()"
                    style="background:none;border:none;cursor:pointer;padding:0;color:#94a3b8;flex-shrink:0;">
                    <svg id="eye-icon" style="width:17px;height:17px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>

            {{-- Tombol Daftar --}}
            <button type="submit"
                style="width:100%;background:#10B981;color:white;font-weight:700;font-size:0.875rem;padding:12px;border-radius:12px;border:none;cursor:pointer;font-family:inherit;"
                onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10B981'">
                Daftar
            </button>

            {{-- Divider --}}
            <div style="display:flex;align-items:center;gap:14px;margin:16px 0;">
                <div style="flex:1;height:1px;background:#e2e8f0;"></div>
                <span style="font-size:0.7rem;color:#94a3b8;">atau</span>
                <div style="flex:1;height:1px;background:#e2e8f0;"></div>
            </div>

        </form>

        {{-- Link Login --}}
        <p style="text-align:center;font-size:0.72rem;color:#94a3b8;margin-top:18px;margin-bottom:0;">
            Sudah punya akun?
            <a href="{{ route('login') }}" style="color:#10B981;font-weight:600;text-decoration:none;">Masuk di sini</a>
        </p>

    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
            } else {
                input.type = 'password';
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`;
            }
        }
    </script>
</x-layouts.guest>
