<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SupabaseService;

class PengaturanController extends Controller
{
    private SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function index()
    {
        $guru = session('guru');
        return view('pengaturan.index', compact('guru'));
    }

    public function updateProfil(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'password_baru' => 'nullable|string|min:6|confirmed',
        ]);

        $guru = session('guru');
        $data = ['nama' => $request->nama];

        if ($request->filled('password_baru')) {
            $data['password_hash'] = password_hash($request->password_baru, PASSWORD_BCRYPT);
        }

        $result = $this->supabase->updateUser($guru['id'], $data);

        if ($result['success']) {
            session()->put('guru.nama', $request->nama);
            return redirect()->route('pengaturan.index')->with('success', 'Profil berhasil diperbarui');
        }

        return redirect()->route('pengaturan.index')->with('error', $result['message']);
    }
}
