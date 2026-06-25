<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Login umum (guru/siswa) via NIS.
     * POST /api/auth/login
     *
     * Mobile mengirim: { nis, password }
     * Response flat: { success, token, user }
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nis'      => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = User::where('nis', $request->nis)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'NIS atau password salah.',
            ], 401);
        }

        $token = auth('api')->login($user);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'token'   => $token,
            'user'    => $user->only('id', 'nis', 'nama', 'email', 'role', 'kelas'),
        ]);
    }

    /**
     * Login siswa via NIS.
     * POST /api/auth/login-siswa
     *
     * Mobile mengirim: { nis, password }
     * Response flat: { success, token, user }
     */
    public function loginSiswa(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nis'      => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = User::where('nis', $request->nis)
                    ->where('role', 'siswa')
                    ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'NIS atau password salah.',
            ], 401);
        }

        $token = auth('api')->login($user);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'token'   => $token,
            'user'    => $user->only('id', 'nis', 'nama', 'email', 'role', 'kelas'),
        ]);
    }

    /**
     * Login guru via NIS.
     * POST /api/auth/login-guru
     *
     * Mobile mengirim: { nis, password }
     * Response flat: { success, token, user }
     */
    public function loginGuru(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nis'      => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = User::where('nis', $request->nis)
                    ->whereIn('role', ['guru', 'admin'])
                    ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'NIS atau password salah.',
            ], 401);
        }

        $token = auth('api')->login($user);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'token'   => $token,
            'user'    => $user->only('id', 'nis', 'nama', 'email', 'role', 'kelas'),
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
        ]);
    }

    /**
     * Ambil profil user yang sedang login.
     * GET /api/auth/me
     *
     * Mobile membaca: res.data['data'] ?? res.data
     */
    public function me(): JsonResponse
    {
        $user = auth('api')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terautentikasi.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data user.',
            'data'    => $user->only('id', 'nis', 'nama', 'email', 'role', 'kelas'),
        ]);
    }

    /**
     * Update password user yang sedang login.
     * POST /api/auth/update-password
     *
     * Mobile mengirim: { password_lama, password_baru, password_baru_confirmation }
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'password_lama'              => 'required|string',
            'password_baru'              => 'required|string|min:4|confirmed',
        ], [
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
            'password_baru.min'       => 'Password baru minimal 4 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = auth('api')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terautentikasi.',
            ], 401);
        }

        // Cek password lama
        if (! Hash::check($request->password_lama, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai.',
            ], 422);
        }

        // Update password
        $user->password = Hash::make($request->password_baru);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diganti.',
        ]);
    }
}