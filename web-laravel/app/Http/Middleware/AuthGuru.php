<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthGuru
{
    /**
     * Cek apakah session('guru') ada.
     * Jika tidak, redirect ke halaman login.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!session('guru')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Update last activity setiap request
        session(['guru_last_activity' => time()]);

        return $next($request);
    }
}