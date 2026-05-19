<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChooseFamilyController extends Controller
{
    /**
     * Tampilkan halaman pilih / buat grup family.
     */
    public function index()
    {
        if (!session('auth_token')) {
            return redirect()->route('login');
        }

        return view('auth.choose-family');
    }

    /**
     * Tampilkan halaman form buat grup baru (Halo, Captain!).
     */
    public function create()
    {
        if (!session('auth_token')) {
            return redirect()->route('login');
        }

        return view('auth.create-family');
    }

    /**
     * Memproses data nama keluarga dan menyimpannya ke database MongoDB via API.
     */
    public function store(Request $request)
    {
        if (!session('auth_token')) {
            return redirect()->route('login');
        }

        $request->validate([
            'family_name' => 'required|string|min:3|max:50',
        ]);

        $token = session('auth_token');

        $response = Http::withToken($token)
            ->acceptJson()
            ->post('http://127.0.0.1:8001/api/v1/families', [
                'family_name' => $request->family_name,
            ]);

        if ($response->successful()) {
            $data = $response->json()['data'] ?? [];
            
            // 💡 PERBAIKAN: Gunakan helper session() permanen (bukan ->with) 
            // agar data tidak hilang saat berpindah dari page Success ke Invite.
            session([
                'join_code' => $data['join_code'] ?? 'FAM67X',
                'family_name' => $data['name'] ?? $request->family_name
            ]);

            return redirect()->route('family.success');
        }

        $errorMessage = $response->json()['message'] ?? 'Gagal membuat grup, silakan coba lagi.';
        return redirect()->back()->withInput()->with('error', $errorMessage);
    }

    /**
     * Tampilkan halaman sukses setelah grup berhasil dibuat (Grup Berhasil Dibuat).
     */
    public function success()
    {
        if (!session('auth_token')) {
            return redirect()->route('login');
        }

        // Karena kita sudah pakai session() di atas, kita tidak perlu session()->keep() lagi di sini.
        return view('auth.family-success');
    }

    /**
     * Tampilkan Halaman Ajak Keluarga Gabung (Invite Page).
     */
    public function invite()
    {
        if (!session('auth_token')) {
            return redirect()->route('login');
        }

        // Ambil data dari session, jika kosong beri nilai default sebagai cadangan
        $joinCode = session('join_code', 'FAM67X');
        $familyName = session('family_name', 'Keluarga');

        return view('auth.family-invite', compact('joinCode', 'familyName'));
    }

    /**
     * Proses Direct Link: Anggota otomatis bergabung saat link di-klik.
     */
    public function autoJoin($code)
    {
        if (!session('auth_token')) {
            return redirect()->route('login');
        }

        $token = session('auth_token');

        $response = Http::withToken($token)
            ->acceptJson()
            ->post('http://127.0.0.1:8001/api/v1/families/join', [
                'join_code' => $code,
            ]);

        if ($response->successful()) {
            return view('auth.family-join-success');
        }

        $errorMessage = $response->json()['message'] ?? 'Kode undangan tidak valid atau kedaluwarsa.';
        return redirect()->route('family.choose')->with('error', $errorMessage);
    }

    /**
     * Tampilkan form halaman input kode untuk bergabung (Join Family Page)
     */
    public function showJoinForm()
    {
        if (!session('auth_token')) {
            return redirect()->route('login');
        }

        return view('auth.family-join'); // Membuka file Blade input kode
    }

    /**
     * Memproses kode undangan yang diinput user ke API Backend
     */
    public function processJoin(Request $request)
    {
        if (!session('auth_token')) {
            return redirect()->route('login');
        }

        // Validasi input wajib diisi
        $request->validate([
            'join_code' => 'required|string|min:4|max:12',
        ]);

        $token = session('auth_token');

        // Kirim kode ke endpoint API Join milik backend kamu
        $response = Http::withToken($token)
            ->acceptJson()
            ->post('http://127.0.0.1:8001/api/v1/families/join', [
                'join_code' => $request->join_code,
            ]);

        if ($response->successful()) {
            // Jika backend sukses mendaftarkan user ke DB, lempar ke page sukses bergabung
            return redirect()->route('family.join.success');
        }

        // Jika kode salah / expired, kembalikan ke form dengan pesan error dari API
        $errorMessage = $response->json()['message'] ?? 'Kode undangan tidak valid atau sudah kedaluwarsa.';
        return redirect()->back()->withInput()->with('error', $errorMessage);
    }

    /**
     * Tampilkan halaman Selamat Bergabung setelah sukses masuk database
     */
    public function joinSuccess()
    {
        if (!session('auth_token')) {
            return redirect()->route('login');
        }

        return view('auth.family-join-success');
    }
}