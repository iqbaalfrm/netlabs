<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Login siswa via JWT.
     * POST /api/auth/login-siswa
     */
    public function loginSiswa(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Cari user dengan role siswa
        $user = User::where('email', $credentials['email'])
                    ->where('role', 'siswa')
                    ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
                'data'    => null,
            ], 401);
        }

        if (! $user->aktif) {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak aktif.',
                'data'    => null,
            ], 403);
        }

        $token = auth('api')->login($user);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'token' => $token,
                'user'  => $user->only('id', 'name', 'email', 'role', 'kelas', 'foto'),
            ],
        ]);
    }

    /**
     * Login guru via JWT.
     * POST /api/auth/login-guru
     */
    public function loginGuru(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Cari user dengan role guru/admin
        $user = User::where('email', $credentials['email'])
                    ->whereIn('role', ['guru', 'admin'])
                    ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
                'data'    => null,
            ], 401);
        }

        $token = auth('api')->login($user);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'token' => $token,
                'user'  => $user->only('id', 'name', 'email', 'role', 'foto'),
            ],
        ]);
    }

    /**
     * Logout — hapus token JWT.
     * POST /api/auth/logout
     */
    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
            'data'    => null,
        ]);
    }

    /**
     * Ambil profil user yang sedang login.
     * GET /api/auth/me
     */
    public function me(): JsonResponse
    {
        $user = auth('api')->user();

        return response()->json([
            'success' => true,
            'message' => 'Data user.',
            'data'    => $user->only('id', 'name', 'email', 'role', 'kelas', 'no_hp', 'foto'),
        ]);
    }
}