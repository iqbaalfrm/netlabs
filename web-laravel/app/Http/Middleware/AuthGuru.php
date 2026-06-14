<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthGuru
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('guru')) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        // Check session age — auto expire after 2 hours
        $lastActivity = session('guru_last_activity', 0);
        if (time() - $lastActivity > 7200) {
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('login')
                ->with('error', 'Sesi telah berakhir, silakan login kembali');
        }

        session(['guru_last_activity' => time()]);

        return $next($request);
    }
}
