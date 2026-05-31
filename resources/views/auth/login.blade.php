{{-- resources/views/auth/login.blade.php --}}
<x-layouts.guest>
    <x-slot:title>FamsPay - Masuk ke Akun</x-slot:title>

    <div style="padding: 24px 28px 22px;">

        {{-- Logo --}}
        <div style="display: flex; justify-content: center; margin-bottom: 10px;">
            <div style="width: 50px; height: 50px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center;">
                <img src="{{ asset('images/Frame_252.png') }}" alt="FamsPay Logo"
                    style="width: 32px; height: 32px; object-fit: contain;">
            </div>
        </div>

        {{-- Heading --}}
        <div style="text-align: center; margin-bottom: 18px;">
            <h1 style="font-size: 1.1rem; font-weight: 700; color: #1e293b; margin: 0 0 5px;">Masuk ke Akun</h1>
            <p style="font-size: 0.75rem; color: #94a3b8; line-height: 1.5; margin: 0;">
                Halo! Senang melihatmu kembali.<br>Silakan masuk ke akun Anda.
            </p>
        </div>

        {{-- Error credential --}}
        @if (session('error'))
            <div style="background: #FEF2F2; border: 1px solid #FECACA; border-radius: 12px; padding: 12px 16px; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                <svg style="width:18px;height:18px;color:#EF4444;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                <span style="font-size: 0.8rem; color: #DC2626; font-weight: 500;">{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div style="background: #FEF2F2; border: 1px solid #FECACA; border-radius: 12px; padding: 12px 16px; margin-bottom: 16px;">
                <span style="font-size: 0.8rem; color: #DC2626; font-weight: 500;">{{ $errors->first() }}</span>
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('login') }}" method="POST">
            @csrf

            {{-- Email --}}
            <div style="display: flex; align-items: center; gap: 10px; background: #F1F5F9; border-radius: 14px; padding: 11px 14px; margin-bottom: 10px;">
                <svg style="width:18px;height:18px;color:#94a3b8;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" autocomplete="email"
                    style="flex:1;background:transparent;border:none;outline:none;font-size:0.82rem;color:#334155;font-family:inherit;">
            </div>

            {{-- Password --}}
            <div style="display: flex; align-items: center; gap: 10px; background: #F1F5F9; border-radius: 14px; padding: 11px 14px; margin-bottom: 5px;">
                <svg style="width:18px;height:18px;color:#94a3b8;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <input type="password" name="password" id="password" placeholder="Password" autocomplete="current-password"
                    style="flex:1;background:transparent;border:none;outline:none;font-size:0.82rem;color:#334155;font-family:inherit;">
                <button type="button" onclick="togglePassword()"
                    style="background:none;border:none;cursor:pointer;padding:0;color:#94a3b8;flex-shrink:0;">
                    <svg id="eye-icon" style="width:18px;height:18px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>

            {{-- Lupa Password --}}
            <div style="text-align: right; margin-bottom: 16px;">
                <a href="#" style="font-size: 0.72rem; color: #94a3b8; text-decoration: none;">Lupa password?</a>
            </div>

            {{-- Tombol Masuk --}}
            <button type="submit"
                style="width:100%;background:#10B981;color:white;font-weight:700;font-size:0.85rem;padding:12px;border-radius:14px;border:none;cursor:pointer;font-family:inherit;transition:background 0.15s;"
                onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10B981'">
                Mulai!
            </button>

            {{-- Divider --}}
            <div style="display:flex;align-items:center;gap:14px;margin:16px 0;">
                <div style="flex:1;height:1px;background:#e2e8f0;"></div>
                <span style="font-size:0.72rem;color:#94a3b8;">atau</span>
                <div style="flex:1;height:1px;background:#e2e8f0;"></div>
            </div>

            {{-- Google Button --}}
            <button type="button"
                style="width:100%;background:#F1F5F9;padding:10px;border-radius:14px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;"
                onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#F1F5F9'">
                <svg style="width:18px;height:18px;" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
            </button>

        </form>

        {{-- Link Register --}}
        <p style="text-align:center;font-size:0.72rem;color:#94a3b8;margin-top:18px;margin-bottom:0;">
            Belum punya akun?
            <a href="{{ route('register') }}" style="color:#10B981;font-weight:600;text-decoration:none;">Daftar di sini</a>
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