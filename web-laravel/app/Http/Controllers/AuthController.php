<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLogin()
    {
        // Jika sudah login, redirect ke dashboard
        if (session('guru')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Proses login guru via NIS & password
     */
    public function login(Request $request)
    {
        $request->validate([
            'nis'      => 'required|string|max:20',
            'password' => 'required|string|min:6',
        ], [
            'nis.required'      => 'NIS wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.min'      => 'Password minimal 6 karakter',
        ]);

        // Cari user berdasarkan NIS
        $user = User::where('nis', $request->nis)->first();

        // Cek kredensial
        if (!$user || !password_verify($request->password, $user->password_hash)) {
            return back()->withErrors(['login' => 'NIS atau password salah'])->withInput();
        }

        // Hanya guru yang boleh login web
        if ($user->role !== 'guru') {
            return back()->withErrors(['login' => 'Akun ini bukan akun guru'])->withInput();
        }

        // Regenerate session & simpan data guru
        $request->session()->regenerate();
        session([
            'guru'               => $user->toArray(),
            'guru_id'            => $user->id,
            'guru_last_activity' => time(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Selamat datang, ' . $user->nama);
    }

    /**
     * Logout — hapus session
     */
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah logout');
    }
}