<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\JsonResponse;

class MaterialController extends Controller
{
    /**
     * Detail satu materi.
     * GET /api/materi/{id}
     */
    public function show(int $id): JsonResponse
    {
        $material = Material::with('module')->find($id);

        if (! $material) {
            return response()->json([
                'success' => false,
                'message' => 'Materi tidak ditemukan.',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail materi.',
            'data'    => $material,
        ]);
    }
}