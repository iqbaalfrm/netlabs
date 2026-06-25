<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProgressTopik;
use App\Models\Topik;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    /**
     * Ambil progress topik siswa untuk satu pertemuan.
     * GET /api/progress/{pertemuan_id}
     */
    public function index($pertemuanId): JsonResponse
    {
        $userId = auth('api')->id();

        $topikIds = Topik::where('pertemuan_id', $pertemuanId)->pluck('id');

        $progress = ProgressTopik::where('user_id', $userId)
            ->whereIn('topik_id', $topikIds)
            ->get()
            ->keyBy('topik_id');

        $totalTopik = $topikIds->count();
        $selesai = $progress->where('is_selesai', true)->count();
        $persen = $totalTopik > 0 ? round(($selesai / $totalTopik) * 100) : 0;

        return response()->json([
            'success' => true,
            'data'    => [
                'total_topik' => $totalTopik,
                'selesai'     => $selesai,
                'persen'      => $persen,
                'detail'      => $progress->values(),
            ],
            'message' => 'Progress topik.',
        ]);
    }

    /**
     * Tandai topik selesai.
     * POST /api/progress/{topik_id}/selesai
     */
    public function tandaiSelesai($topikId): JsonResponse
    {
        $userId = auth('api')->id();

        $topik = Topik::find($topikId);

        if (! $topik) {
            return response()->json([
                'success' => false,
                'message' => 'Topik tidak ditemukan.',
            ], 404);
        }

        $progress = ProgressTopik::updateOrCreate(
            [
                'user_id'  => $userId,
                'topik_id' => $topikId,
            ],
            [
                'is_selesai'  => true,
                'selesai_pada' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'data'    => $progress,
            'message' => 'Topik ditandai selesai.',
        ]);
    }
}