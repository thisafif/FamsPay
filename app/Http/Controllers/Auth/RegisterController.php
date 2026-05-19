<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class RegisterController extends Controller
{
    /**
     * Tampilkan halaman register.
     */
    public function create()
    {
        if (session('auth_token')) {
            return redirect()->route('family.choose');
        }

        return view('auth.register');
    }

    /**
     * Proses register via API.
     */
    public function store(Request $request)
    {
        // Validasi input sisi client
        $request->validate([
            'name'     => ['required', 'string', 'min:2'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'min:8'],
        ], [
            'name.required'     => 'Nama wajib diisi.',
            'name.min'          => 'Nama minimal 2 karakter.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
        ]);

        // Kirim request ke API
        // Sesudah (Disesuaikan dengan rute API Backend)
        $response = Http::post(config('app.api_url') . '/register', [
            'full_name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
        ]);

        if ($response->successful()) {
            $data = $response->json();

            // Simpan token & data user ke session
            Session::put('auth_token', $data['token'] ?? $data['access_token'] ?? null);
            Session::put('user', $data['user'] ?? null);

            return redirect()->route('family.choose');
        }

        // Handle error dari API (email sudah dipakai, dll)
        $message = $response->json('message') ?? 'Registrasi gagal. Silakan coba lagi.';

        // Jika API mengembalikan error per-field
        $errors = $response->json('errors') ?? [];
        if (!empty($errors)) {
            return back()->withInput()->withErrors($errors);
        }

        return back()
            ->withInput($request->only('name', 'email'))
            ->with('error', $message);
    }
}
