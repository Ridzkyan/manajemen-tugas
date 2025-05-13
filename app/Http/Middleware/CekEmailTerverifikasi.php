<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CekEmailTerverifikasi
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('mahasiswa')->user();

        // Pastikan user login dan role mahasiswa
        if ($user && $user->role === 'mahasiswa') {
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice')->with('error', 'Kamu harus verifikasi email dulu ya 😊');
            }
        }

        return $next($request);
    }
}
