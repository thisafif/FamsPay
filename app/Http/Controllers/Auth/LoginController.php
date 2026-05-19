<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function create()
    {
        // Jika sudah login, redirect ke choose-family atau dashboard
        if (session('auth_token')) {
            return redirect()->route('family.choose');
        }

        return view('auth.login');
    }

    /**
     * Proses login via API.
     */
    public function store(Request $request)
    {
        // Validasi input sisi client
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);

        // Kirim request ke API
        $response = Http::post(config('app.api_url') . '/login', [
            'email'    => $request->email,
            'password' => $request->password,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            // HAPUS atau komentari baris dd($data); yang tadi ya!

            // 👇 KITA UBAH JALURNYA MASUK KE DALAM ['data'] 👇
            Session::put('auth_token', $data['data']['token'] ?? $data['data']['access_token'] ?? null);
            Session::put('user', $data['data']['user'] ?? null);

            return redirect()->route('family.choose');
        }

        // Handle error dari API
        $message = $response->json('message') ?? 'Email atau password salah.';

        return back()
            ->withInput($request->only('email'))
            ->with('error', $message);
    }

    /**
     * Logout — hapus session dan redirect ke login.
     */
    public function destroy(Request $request)
    {
        // Opsional: hit API logout endpoint
        if (session('auth_token')) {
            Http::withToken(session('auth_token'))
                ->post(config('app.api_url') . '/logout')
                ->ok();
        }

        Session::flush();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
