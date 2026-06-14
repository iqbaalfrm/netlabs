<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SupabaseService;

class AuthController extends Controller
{
    private SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function showLogin()
    {
        if (session('guru')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nis'      => 'required|string|max:20',
            'password' => 'required|string|min:6|max:128',
        ], [
            'nis.required'      => 'NIS wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.min'      => 'Password minimal 6 karakter',
        ]);

        $railwayUrl = config('services.railway.url');

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(15)
                ->post($railwayUrl . '/api/auth/login', [
                    'nis'      => $request->input('nis'),
                    'password' => $request->input('password'),
                ]);

            if (!$response->successful()) {
                $msg = $response->json('detail') ?? 'NIS atau password salah';
                return back()->withErrors(['login' => $msg])->withInput();
            }

            $data = $response->json('data') ?? $response->json();
            $token = $data['token'] ?? null;
            $user  = $data['user']  ?? null;

            if (!$token || !$user || ($user['role'] ?? '') !== 'guru') {
                return back()->withErrors(['login' => 'Akun ini bukan akun guru'])->withInput();
            }

            // Regenerate session for security
            $request->session()->regenerate();

            session([
                'guru'               => $user,
                'guru_token'         => $token,
                'guru_last_activity' => time(),
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Selamat datang, ' . $user['nama']);

        } catch (\Exception $e) {
            // Fallback: login via Supabase
            $result = $this->supabase->loginGuru(
                $request->input('nis'),
                $request->input('password')
            );

            if (!$result['success']) {
                return back()->withErrors(['login' => $result['message']])->withInput();
            }

            $request->session()->regenerate();

            session([
                'guru'               => $result['user'],
                'guru_token'         => null,
                'guru_last_activity' => time(),
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Selamat datang, ' . $result['user']['nama']);
        }
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah logout');
    }
}
