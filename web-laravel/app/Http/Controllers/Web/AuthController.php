<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login. GET /login
     */
    public function showLogin(): View
    {
        // Jika sudah login, redirect ke dashboard
        if (session('guru')) {
            return redirect('/dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses login guru. POST /login
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Cari guru/admin
        $user = User::where('email', $credentials['email'])
                    ->whereIn('role', ['guru', 'admin'])
                    ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->with('error', 'Email atau password salah.')->withInput();
        }

        // Simpan ke session
        session(['guru' => [
            'id'    => $user->id,
            'nama'  => $user->nama,
            'email' => $user->email,
            'role'  => $user->role,
        ]]);

        return redirect('/dashboard')->with('success', 'Selamat datang, ' . $user->nama . '!');
    }

    /**
     * Logout — hapus session. POST /logout
     */
    public function logout(): RedirectResponse
    {
        session()->forget('guru');

        return redirect('/login')->with('success', 'Anda telah logout.');
    }
}