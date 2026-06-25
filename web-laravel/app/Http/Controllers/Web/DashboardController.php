<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Pertemuan;
use App\Models\SoalKuis;
use App\Models\User;
use App\Models\ChatHistory;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Halaman dashboard admin: 4 stat card + 10 chat terbaru.
     * GET /dashboard
     */
    public function index(): View
    {
        return view('dashboard', [
            'totalSiswa'    => User::where('role', 'siswa')->count(),
            'totalPertemuan'=> Pertemuan::count(),
            'totalSoal'     => SoalKuis::count(),
            'totalChat'     => ChatHistory::count(),
            'chatTerbaru'   => ChatHistory::with('siswa:id,name')
                                    ->orderBy('created_at', 'desc')
                                    ->limit(10)
                                    ->get(),
        ]);
    }
}