<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * Ambil daftar modul.
     * GET /api/modul
     */
    public function index(): JsonResponse
    {
        $modules = Module::where('aktif', true)
                        ->orderBy('urutan')
                        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar modul.',
            'data'    => $modules,
        ]);
    }

    /**
     * Ambil detail satu modul + materi.
     * GET /api/modul/{id}
     */
    public function show(int $id): JsonResponse
    {
        $module = Module::with('materials')->find($id);

        if (! $module) {
            return response()->json([
                'success' => false,
                'message' => 'Modul tidak ditemukan.',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail modul.',
            'data'    => $module,
        ]);
    }
}